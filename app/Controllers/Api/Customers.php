<?php

namespace App\Controllers\Api;

use Config\Database;

class Customers extends BaseApiController
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /*
    ===============================
    LIST CUSTOMERS
    GET /customers
    ===============================
    */
    public function index()
    {

        $users = $this->db->table('users')
            ->select('
                users.id,
                users.name,
                users.email,
                users.phone,
                users.photo,
                users.created_at,

                COUNT(orders.id) as total_orders,
                COALESCE(SUM(orders.total_amount),0) as total_spent
            ')
            ->join('orders','orders.user_id = users.id','left')
            ->where('branch_id', session('branch_id'))
            ->groupBy('users.id')
            ->orderBy('users.id','DESC')
            ->get()
            ->getResultArray();

        // cast numeric values
        foreach ($users as &$u) {
            $u['total_orders'] = (int) $u['total_orders'];
            $u['total_spent']  = (float) $u['total_spent'];
        }

        return $this->success($users);
    }

    /*
    ===============================
    CUSTOMER DETAIL
    GET /customers/{id}
    ===============================
    */
    public function show($id = null)
    {

        if(!$id){
            return $this->error('Customer ID required');
        }

        /*
        ===============================
        CUSTOMER PROFILE
        ===============================
        */

        $user = $this->db->table('users')
            ->select('
                users.id,
                users.name,
                users.email,
                users.phone,
                users.photo,
                users.status,
                users.created_at,

                COALESCE(wallets.balance,0) as wallet_balance,
                COALESCE(user_points.points,0) as points,
                COUNT(orders.id) as total_orders,
                COALESCE(SUM(orders.total_amount),0) as total_spent,

                loyalty_tiers.name as membership_tier,
                loyalty_tiers.cashback_percent,
                loyalty_tiers.point_multiplier,
                loyalty_tiers.free_drink_per_month
            ')
            ->join('orders','orders.user_id = users.id','left')
            ->join('wallets','wallets.user_id = users.id','left')
            ->join('user_points','user_points.user_id = users.id','left')
            ->join('user_memberships','user_memberships.user_id = users.id','left')
            ->join('loyalty_tiers','loyalty_tiers.id = user_memberships.tier_id','left')
            ->where('users.id',$id)
            ->get()
            ->getRowArray();

        // cast numeric values
        $user['total_orders'] = (int) $user['total_orders'];
        $user['total_spent']  = (float) $user['total_spent'];
        $user['points']  = (float) $user['points'];
        $user['wallet_balance']  = (float) $user['wallet_balance'];

        if(!$user){
            return $this->error('Customer not found');
        }

        /*
        ===============================
        CUSTOMER ORDERS
        ===============================
        */

        $orders = $this->db->table('orders')
            ->select('
                id,
                order_number,
                subtotal,
                discount,
                total_amount,
                status,
                created_at
            ')
            ->where('user_id',$id)
            ->orderBy('id','DESC')
            ->get()
            ->getResultArray();

        foreach ($orders as &$o) {
            $o['subtotal'] = (int) $o['subtotal'];
            $o['total_amount']  = (float) $o['total_amount'];

        }
        
        return $this->success([
            'customer' => $user,
            'orders'   => $orders
        ]);
    }

    /*
    ===============================
    CUSTOMER ORDERS
    GET /customers/{id}/orders
    ===============================
    */
    public function orders($userId)
    {

        $orders = $this->db->table('orders')
            ->select('
                id,
                order_number,
                subtotal,
                discount,
                total_amount,
                status,
                created_at
            ')
            ->where('user_id',$userId)
            ->orderBy('id','DESC')
            ->get()
            ->getResultArray();

        return $this->success($orders);
    }

    /*
    ===============================
    CUSTOMER WALLET
    GET /customers/{id}/wallet
    ===============================
    */
    public function wallet($userId)
    {
        $wallet = $this->db->table('wallets')
            ->where('user_id',$userId)
            ->get()
            ->getRowArray();

        if (!$wallet) {
            return $this->success([
                'wallet' => [
                    'balance' => 0
                ],
                'transactions' => []
            ]);
        }

        $transactions = $this->db->table('wallet_transactions')
            ->select('amount,type,description,created_at')
            ->where('wallet_id',$wallet['id']) // ✅ FIX
            ->orderBy('id','DESC')
            ->get()
            ->getResultArray();

        return $this->success([
            'wallet'=>$wallet,
            'transactions'=>$transactions
        ]);
    }

    /*
    ===============================
    CUSTOMER POINTS
    ===============================
    */
    public function points($userId)
    {

        $points = $this->db->table('user_points')
            ->where('user_id',$userId)
            ->get()
            ->getRowArray();

        $history = $this->db->table('point_transactions')
            ->select('points,type,description,created_at')
            ->where('user_id',$userId)
            ->orderBy('id','DESC')
            ->get()
            ->getResultArray();

        return $this->success([
            'points'=>$points,
            'history'=>$history
        ]);
    }

    /*
    ===============================
    CUSTOMER MEMBERSHIP
    ===============================
    */
    public function membership($userId)
    {

        $membership = $this->db->table('user_memberships')
            ->where('user_id',$userId)
            ->get()
            ->getRowArray();

        $history = $this->db->table('membership_history')
            ->where('user_id',$userId)
            ->orderBy('id','DESC')
            ->get()
            ->getResultArray();

        return $this->success([
            'membership'=>$membership,
            'history'=>$history
        ]);
    }

    /*
    ===============================
    CUSTOMER PROMOS
    ===============================
    */
    public function promos($userId)
    {

        $promos = $this->db->table('promo_claims')
            ->select('
                promos.title,
                promos.discount_type,
                promos.discount_value,
                promo_claims.used
            ')
            ->join('promos','promos.id = promo_claims.promo_id')
            ->where('promo_claims.user_id',$userId)
            ->get()
            ->getResultArray();

        return $this->success($promos);
    }

}