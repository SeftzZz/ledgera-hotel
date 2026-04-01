<?php

namespace App\Services;

use Config\Database;
use App\Models\CartModel;
use App\Models\CartItemModel;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\PaymentModel;
use App\Models\WalletModel;
use App\Models\WalletTransactionModel;
use App\Models\PromoModel;
use App\Models\VoucherModel;
use App\Models\PointTransactionModel;
use App\Models\UserMembershipModel;
use App\Models\LoyaltyTierModel;

class OrderService
{

    public function checkout($data)
    {

        $db = Database::connect();
        $db->transStart();

        $cartModel = new CartModel();
        $cartItemModel = new CartItemModel();
        $orderModel = new OrderModel();
        $orderItemModel = new OrderItemModel();
        $paymentModel = new PaymentModel();
        $walletModel = new WalletModel();
        $walletTxModel = new WalletTransactionModel();
        $promoModel = new PromoModel();
        $voucherModel = new VoucherModel();
        $pointTxModel = new PointTransactionModel();
        $membershipModel = new UserMembershipModel();
        $tierModel = new LoyaltyTierModel();

        $cartId = $data['cart_id'];
        $cart = $cartModel->find($cartId);

        if(!$cart) {
            throw new \Exception('Cart not found');
        }

        $userId = $cart['user_id'];
        $orderNumber = $data['order_number'];
        $deposit = $data['deposit'] ?? 0;

        if(empty($data['payment_method'])){
            throw new \Exception('Payment method required');
        }

        // ==========================
        // VALIDATION RULE
        // ==========================
        if (!empty($data['use_wallet']) && !empty($data['voucher_code'])) {
            throw new \Exception('Voucher tidak bisa digabung dengan wallet');
        }
        
        /*
        ==========================
        GET CART ITEMS
        ==========================
        */
        if($cart['status'] != 'active'){
            throw new \Exception('Cart already checked out');
        }

        $items = $cartItemModel
            ->where('cart_id',$cartId)
            ->findAll();

        $subtotal = 0;

        if(empty($items)){
            throw new \Exception("Cart empty");
        }

        foreach ($items as $item)
        {
            $subtotal += $item['price'] * $item['quantity'];
        }

        /*
        ==========================
        APPLY PROMO
        ==========================
        */

        $promoDiscount = 0;

        if(!empty($data['promo_id']))
        {
            $promo = $promoModel->find($data['promo_id']);

            if ($promo && $promo['status'] == 'active') {

                if ($subtotal >= $promo['min_purchase']) {

                    if ($promo['discount_type'] == 'percent') {
                        $promoDiscount = $subtotal * ($promo['discount_value'] / 100);
                    } else {
                        $promoDiscount = $promo['discount_value'];
                    }

                }

            }
        }

        /*
        ==========================
        APPLY VOUCHER
        ==========================
        */

        $voucherDiscount = 0;

        if(!empty($data['voucher_code']))
        {
            $voucher = $voucherModel
                ->where('code',$data['voucher_code'])
                ->first();

            if($voucher && 
               $voucher['status']=='active' &&
               $voucher['expired_at'] >= date('Y-m-d')) {

                if($subtotal >= $voucher['min_purchase']) {

                    if($voucher['discount_type']=='percent'){
                        $voucherDiscount = $subtotal * ($voucher['discount_value']/100);
                    } else {
                        $voucherDiscount = $voucher['discount_value'];
                    }

                }

            }
        }

        // ==========================
        // PRIORITY: VOUCHER > PROMO
        // ==========================
        $discount = 0;

        if ($voucherDiscount > 0) {
            $discount = $voucherDiscount;
        } else {
            $discount = $promoDiscount;
        }

        /*
        ==========================
        FINAL TOTAL
        ==========================
        */

        $walletUsed = 0;
        $wallet = null;

        if(($data['payment_method'] ?? '') === 'wallet')
        {
            $wallet = $walletModel
                ->where('user_id',$userId)
                ->first();

            if(!$wallet || $wallet['balance'] <= 0){
                throw new \Exception('Saldo wallet tidak tersedia');
            }

            $payable = max(0, $subtotal - $discount);

            if($wallet['balance'] < $payable){
                throw new \Exception('Saldo tidak cukup');
            }

            $walletUsed = $payable;
        }

        $total = max(0, $subtotal - $discount);
        $amountToPay = max(0, $total - $walletUsed);

        /*
        ==========================
        CREATE ORDER
        ==========================
        */

        if ($amountToPay == 0) {
            $paymentStatus = 'paid';
            $orderStatus = 'paid';
        } else {
            $paymentStatus = 'pending';
            $orderStatus = 'pending';
        }

        $orderId = $orderModel->insert([
            'order_number'  =>$orderNumber,
            'user_id'       =>$userId,
            'branch_id'     =>$cart['branch_id'],
            'cart_id'       =>$cartId,
            'subtotal'      =>$subtotal,
            'discount'      =>$discount,
            'wallet_used'   =>$walletUsed,
            'deposit'       =>$deposit,
            'total_amount'  =>$total,
            'status'        =>$paymentStatus
        ]);

        /*
        ==========================
        WALLET
        ==========================
        */

        if($walletUsed > 0 && $wallet)
        {

            $result = $walletModel
                ->set('balance','balance-'.$walletUsed,false)
                ->where('id',$wallet['id'])
                ->where('balance >=',$walletUsed)
                ->update();

            if(!$result){
                throw new \Exception('Wallet balance changed');
            }

            $walletTxModel->insert([
                'wallet_id'=>$wallet['id'],
                'type'=>'debit',
                'amount'=>$walletUsed,
                'reference_id'=>$orderId,
                'reference_type'=>'order',
                'description'=>'Order payment'
            ]);

        }

        /*
        ==========================
        ORDER ITEMS
        ==========================
        */

        $batch = [];

        foreach($items as $item){
            $batch[] = [
                'order_id'  => $orderId,
                'item_id'   => $item['item_id'],
                'quantity'  => $item['quantity'],
                'price'     => $item['price'],
                'created_at'=> $item['created_at']
            ];
        }

        $orderItemModel->insertBatch($batch);

        /*
        ==========================
        PAYMENT
        ==========================
        */

        $paymentModel->insert([
            'order_id'=>$orderId,
            'payment_method'=>$data['payment_method'],
            'amount'=>$total,
            'status'=>$paymentStatus
        ]);

        /*
        ==========================
        POINTS
        ==========================
        */

        $rule = $db->table('point_rules')
            ->where('status','active')
            ->where('branch_id', $cart['branch_id'])
            ->orderBy('id','DESC')
            ->get()
            ->getRowArray();

        $pointsEarned = 0;

        if($rule){

            $pointsEarned = floor($subtotal / $rule['spend_amount']) * $rule['point_amount'];

        }

        if($pointsEarned>0)
        {

            $pointTxModel->insert([
                'user_id'=>$userId,
                'points'=>$pointsEarned,
                'type'=>'earn',
                'reference_type'=>'order',
                'reference_id'=>$orderId
            ]);

        }

        $exists = $db->table('user_points')
            ->where('user_id',$userId)
            ->get()
            ->getRowArray();

        if(!$exists) {

            $db->table('user_points')->insert([
                'user_id'=>$userId,
                'points'=>$pointsEarned
            ]);

        } else {

            $db->table('user_points')
                ->set('points',"points + $pointsEarned",false)
                ->where('user_id',$userId)
                ->update();
        }

        /*
        ==========================
        UPDATE MEMBERSHIP
        ==========================
        */

        $membership = $membershipModel
            ->where('user_id',$userId)
            ->first();

        if($membership)
        {

            $newSpend = $membership['total_spending'] + $subtotal;
            $newPoints = $membership['total_points'] + $pointsEarned;

            $membershipModel->update($membership['id'],[
                'total_spending'=>$newSpend,
                'total_points'=>$newPoints
            ]);

            /*
            UPGRADE TIER
            */

            $tiers = $tierModel
                ->orderBy('min_spending','ASC')
                ->findAll();

            $bestTier = null;

            foreach($tiers as $tier){
                if($newSpend >= $tier['min_spending']){
                    $bestTier = $tier['id'];
                }
            }

            if($bestTier){
                $membershipModel->update($membership['id'],[
                    'tier_id'=>$bestTier
                ]);
            }

        }

        /*
        ==========================
        UPDATE CART
        ==========================
        */

        $cartModel->update($cartId, [
            'status' => 'checked_out'
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new \Exception('checked out failed');
        }

        return $orderNumber;

    }

}