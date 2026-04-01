<?php

namespace App\Controllers\Api;

use App\Services\TransactionService;
use App\Services\OrderService;
use App\Services\JournalService;
use Config\Database;

class Orders extends BaseApiController
{

    public function checkout()
    {
        $service = new OrderService();

        $data = $this->request->getJSON(true);
        $data['user_id'] = $this->request->user->id;

        $order = $service->checkout($data);

        return $this->success([
            'order_number'=>$order
        ]);
    }

    public function list($userId)
    {
        $db = Database::connect();

        $orders = $db->table('orders')
            ->where('user_id',$userId)
            ->orderBy('id','DESC')
            ->get()
            ->getResultArray();

        return $this->success($orders);
    }

    public function detail($id)
    {
        $db = Database::connect();

        $order = $db->table('orders')
            ->select('
                orders.*,
                users.name as customer_name,
                users.email as customer_email,
                users.phone as customer_phone
            ')
            ->join('users','users.id = orders.user_id','left')
            ->where('orders.id',$id)
            ->get()
            ->getRowArray();

        if(!$order){
            return $this->error('Order not found');
        }

        $items = $db->table('order_items')
            ->select('
                order_items.*,
                items.name
            ')
            ->join('items','items.id = order_items.item_id','left')
            ->where('order_items.order_id',$id)
            ->get()
            ->getResultArray();

        return $this->success([
            'order' => $order,
            'items' => $items
        ]);
    }

    public function pay()
    {
        $data = $this->request->getJSON(true);

        if (empty($data['order_id'])) {
            return $this->error('order_id required');
        }

        $db = \Config\Database::connect();

        // =========================
        // GET ORDER
        // =========================
        $order = $db->table('orders')
            ->where('id', $data['order_id'])
            ->get()
            ->getRowArray();

        if (!$order) {
            return $this->error('Order not found');
        }

        // =========================
        // 🔒 VALIDASI TENANT
        // =========================
        // if ($order['company_id'] != session('company_id')) {
        //     return $this->error('Unauthorized company');
        // }

        // if ($order['branch_id'] != session('branch_id')) {
        //     return $this->error('Unauthorized branch');
        // }

        // =========================
        // 🔥 AUTO TAX DARI ITEMS
        // =========================
        $orderItems = $db->table('order_items oi')
            ->select('bi.tax_type')
            ->join(
                'branch_items bi',
                'bi.item_id = oi.item_id AND bi.branch_id = ' . (int)session('branch_id'),
                'left'
            )
            ->where('oi.order_id', $order['id'])
            ->get()
            ->getResultArray();

        $taxType = null;

        foreach ($orderItems as $item) {

            if ($item['tax_type'] === 'pb1') {
                $taxType = 'pb1';
                break;
            }

            if ($item['tax_type'] === 'ppn') {
                $taxType = 'ppn';
            }
        }

        // =========================
        // GET TAX CODE DINAMIS
        // =========================
        $taxCodeId = null;

        if ($taxType) {

            $tax = $db->table('tax_codes')
                ->where('company_id', session('company_id'))
                ->where('tax_type', $taxType)
                ->where('tax_direction', 'output')
                ->where('is_active', 1)
                ->get()
                ->getRowArray();

            if ($tax) {
                $taxCodeId = $tax['id'];
            }
        }

        // =========================
        // PAYMENT LOGIC
        // =========================
        $depositInput = (float) ($data['deposit'] ?? 0);
        $manualStatus = $data['status'] ?? null;

        if ($depositInput < 0) {
            return $this->error('Deposit tidak boleh minus');
        }

        $oldDeposit = (float) $order['deposit'];
        $newDeposit = $oldDeposit + $depositInput;

        // =========================
        // STATUS LOGIC
        // =========================
        if ($manualStatus === 'paid') {

            $status = 'paid';
            $paymentStatus = 'paid';

            // 🔥 hanya sisa
            $depositInput = $order['total_amount'] - $oldDeposit;
            $newDeposit = $order['total_amount'];

        } else {

            if ($newDeposit >= $order['total_amount']) {
                $status = 'paid';
                $paymentStatus = 'paid';
            } elseif ($newDeposit > 0) {
                $status = 'processing';
                $paymentStatus = 'pending';
            } else {
                $status = 'pending';
                $paymentStatus = 'pending';
            }
        }

        // =========================
        // UPDATE ORDER
        // =========================
        $db->table('orders')
            ->where('id', $data['order_id'])
            ->update([
                'deposit' => $newDeposit,
                'status'  => $status
            ]);

        // =========================
        // UPDATE PAYMENT
        // =========================
        $db->table('payments')
            ->where('order_id', $data['order_id'])
            ->update([
                'status'  => $paymentStatus,
                'paid_at' => $status === 'paid' ? date('Y-m-d H:i:s') : null
            ]);

        // =========================
        // 🔥 ACCOUNTING
        // =========================
        $trxService = new \App\Services\TransactionService();
        $coaModel = new \App\Models\CoaModel();

        $existingSales = $db->table('transactions')
            ->where('reference_no', $order['order_number'])
            ->whereIn('trx_type', ['sales', 'sales_partial'])
            ->countAllResults();

        $kasAccount = $coaModel
            ->where('company_id', session('company_id'))
            ->where('account_code', '1101')
            ->where('is_active', 1)
            ->first();

        if (!$kasAccount) {
            return $this->error('Akun Kas (1101) tidak ditemukan');
        }

        $paymentAccountId = $kasAccount['id'];

        // =========================
        // 🔥 ACCOUNTING
        // =========================
        $trxService = new \App\Services\TransactionService();
        $coaModel = new \App\Models\CoaModel();

        // =========================
        // 🔥 AMBIL ITEM → trx_type
        // =========================
        $orderItems = $db->table('order_items oi')
            ->select('bi.tax_type, i.category_id')
            ->join('branch_items bi', 'bi.item_id = oi.item_id AND bi.branch_id = ' . (int)$order['branch_id'], 'left')
            ->join('items i', 'i.id = oi.item_id', 'left')
            ->where('oi.order_id', $order['id'])
            ->get()
            ->getResultArray();

        // =========================
        // 🔥 DETERMINE trx_type
        // =========================
        $trxType = 'sales'; // default (hotel fallback)

        if (!empty($orderItems)) {

            // contoh mapping sederhana (bisa kamu refine)
            $categoryId = $orderItems[0]['category_id'];

            switch ($categoryId) {
                case 1: $trxType = 'sales_food'; break;
                case 2: $trxType = 'sales_beverage'; break;
                case 3: $trxType = 'sales_shisha'; break;
                case 4: $trxType = 'sales_catering'; break;
                default: $trxType = 'sales_package'; break;
            }
        }

        // =========================
        // 🔥 CHECK EXISTING
        // =========================
        $existingSales = $db->table('transactions')
            ->where('reference_no', $order['order_number'])
            ->like('trx_type', 'sales')
            ->countAllResults();

        // =========================
        // GET CASH ACCOUNT
        // =========================
        $kasAccount = $coaModel
            ->where('company_id', session('company_id'))
            ->where('account_code', '1101')
            ->where('is_active', 1)
            ->first();

        if (!$kasAccount) {
            return $this->error('Akun Kas (1101) tidak ditemukan');
        }

        $paymentAccountId = $kasAccount['id'];

        // =========================
        // CASE 1: FIRST TRANSACTION
        // =========================
        if ($existingSales == 0) {

            // 🔥 PARTIAL
            if ($depositInput > 0 && $status !== 'paid') {

                $trxService->create([
                    'company_id'         => session('company_id'),
                    'branch_id'          => $order['branch_id'], // 🔥 FIX
                    'trx_date'           => date('Y-m-d'),

                    'trx_type'           => $trxType . '_partial', // 🔥 DINAMIS

                    'reference_no'       => $order['order_number'],

                    'amount'             => (float) $order['total_amount'],
                    'gross_amount'       => (float) $order['total_amount'],
                    'paid_amount'        => $depositInput,

                    'payment_account_id' => $paymentAccountId,
                    'tax_code_id'        => $taxCodeId,
                    'tax_mode'           => $order['tax_mode'] ?? 'inclusive'
                ]);
            }

            // 🔥 FULL PAYMENT
            elseif ($status === 'paid') {

                $trxService->create([
                    'company_id'         => session('company_id'),
                    'branch_id'          => $order['branch_id'], // 🔥 FIX
                    'trx_date'           => date('Y-m-d'),

                    'trx_type'           => $trxType, // 🔥 DINAMIS

                    'reference_no'       => $order['order_number'],

                    'amount'             => (float) $order['total_amount'],
                    'gross_amount'       => (float) $order['total_amount'],

                    'payment_account_id' => $paymentAccountId,
                    'tax_code_id'        => $taxCodeId,
                    'tax_mode'           => $order['tax_mode'] ?? 'inclusive'
                ]);
            }
        }

        // =========================
        // CASE 2: PAYMENT LANJUTAN
        // =========================
        else {

            if ($depositInput > 0) {

                $trxService->create([
                    'company_id'         => session('company_id'),
                    'branch_id'          => $order['branch_id'],
                    'trx_date'           => date('Y-m-d'),

                    'trx_type'           => 'receive_payment',

                    'reference_no'       => $order['order_number'],

                    'amount'             => $depositInput,
                    'payment_account_id' => $paymentAccountId
                ]);
            }
        }

        return $this->success([
            'deposit' => $newDeposit,
            'status'  => $status,
            'tax_type'=> $taxType
        ], 'Payment updated');
    }

    public function orders()
    {
      $db = \Config\Database::connect();

      $orders = $db->table('orders')
        ->select("
          orders.id,
          orders.order_number,
          orders.created_at,
          orders.total_amount,
          orders.status,
          users.name as customer,
          users.email,
          users.photo,
          branches.branch_name as branch,
          payments.payment_method,
          payments.status as payment_status
        ")
        ->join('users','users.id = orders.user_id','left')
        ->join('branches','branches.id = orders.branch_id','left')
        ->join('payments','payments.order_id = orders.id','left')
        ->orderBy('orders.id','DESC')
        ->get()
        ->getResultArray();

      $result = [];

      foreach ($orders as $row) {

        $date = date('Y-m-d', strtotime($row['created_at']));
        $time = date('H:i:s', strtotime($row['created_at']));

        // payment mapping
        $payment = 2;
        if ($row['payment_status'] == 'paid') $payment = 1;
        if ($row['payment_status'] == 'failed') $payment = 3;

        // order status mapping
        $status = 1;
        if ($row['status'] == 'paid') $status = 2;
        if ($row['status'] == 'processing') $status = 3;
        if ($row['status'] == 'ready') $status = 4;

        $result[] = [
          "id" => $row['id'],
          "order" => $row['order_number'],
          "date" => $date,
          "time" => $time,
          "customer" => $row['customer'],
          "email" => $row['email'],
          "avatar" => $row['photo'],
          "payment" => $payment,
          "status" => $status,
          "method" => $row['payment_method'],
          "method_number" => $row['branch']
        ];
      }

      return $this->success($result);
    }

    public function summary()
    {

      $db = \Config\Database::connect();

      $pending = $db->table('orders')
        ->where('status', 'pending')
        ->countAllResults();

      $processing = $db->table('orders')
        ->where('status', 'processing')
        ->countAllResults();

      $completed = $db->table('orders')
        ->where('status', 'paid')
        ->countAllResults();

      $failed = $db->table('payments')
        ->where('status', 'failed')
        ->countAllResults();

      $refunded = $db->table('payments')
        ->where('status', 'refunded')
        ->countAllResults();

      return $this->success([
        "pending" => $pending,
        "processing" => $processing,
        "completed" => $completed,
        "refunded" => $refunded,
        "failed" => $failed
      ]);

    }
}