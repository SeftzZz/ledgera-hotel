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

        return $this->success($order);
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
        $DEBUG = true;

        function dd($label, $data) {
            echo "\n===== DEBUG: $label =====\n";
            var_dump($data);
            echo "\n=========================\n";
            die;
        }

        $data = $this->request->getJSON(true);

        if (empty($data['order_id'])) {
            return $this->error('order_id required');
        }

        $trxTypeInput = $data['trxType'] ?? null;

        $db = \Config\Database::connect();


        // =========================
        // 🔒 VALIDASI TENANT
        // =========================
        // if ($order['company_id'] != $companyId) {
        //     return $this->error('Unauthorized company');
        // }

        // if ($order['branch_id'] != session('branch_id')) {
        //     return $this->error('Unauthorized branch');
        // }

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
        // 🔥 GET COMPANY FROM BRANCH
        // =========================
        $branch = $db->table('branches')
            ->select('company_id')
            ->where('id', $order['branch_id'])
            ->get()
            ->getRowArray();

        if (!$branch) {
            return $this->error('Branch tidak ditemukan');
        }

        $companyId = $branch['company_id'];

        // =========================
        // 🔥 AUTO TAX DARI ITEMS
        // =========================
        $orderItems = $db->table('order_items oi')
            ->select("
                oi.item_id,
                i.id as item_id_pos,
                vi.id as vendor_item_id,
                bi.tax_type,
                i.category_id
            ")
            ->join('items i', 'i.id = oi.item_id', 'left')
            ->join('vendor_items vi', 'vi.id = oi.item_id', 'left')
            ->join(
                'branch_items bi',
                'bi.item_id = i.id AND bi.branch_id = ' . (int)$order['branch_id'],
                'left'
            )
            ->where('oi.order_id', $order['id'])
            ->get()
            ->getResultArray();

        foreach ($orderItems as &$item) {

            if (!empty($item['item_id_pos'])) {
                $item['source'] = 'items';
            } elseif (!empty($item['vendor_item_id'])) {
                $item['source'] = 'vendor_items';
            } else {
                $item['source'] = 'unknown';
            }
        }

        // if ($DEBUG) dd('ORDER DATA', $orderItems);

        $taxType = null;

        foreach ($orderItems as $item) {

            if ($item['source'] === 'vendor_items') {
                continue;
            }

            if ($item['tax_type'] === 'pb1') {
                $taxType = 'pb1';
                break;
            }

            if ($item['tax_type'] === 'ppn') {
                $taxType = 'ppn';
            }

            if ($item['tax_type'] === 'fee') {
                $taxType = 'fee';
            }
        }

        // =========================
        // GET TAX CODE DINAMIS
        // =========================
        $taxCodeId = null;

        if ($taxType) {

            $tax = $db->table('tax_codes')
                ->where('company_id', $companyId)
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

        // =========================
        // 🔥 GET CASH ACCOUNT
        // =========================
        $kasAccount = $coaModel
            ->where('company_id', $companyId)
            ->where('account_code', '1101')
            ->where('is_active', 1)
            ->first();

        if (!$kasAccount) {
            return $this->error('Akun Kas (1101) tidak ditemukan');
        }

        $paymentAccountId = $kasAccount['id'];

        // =========================
        // 🔥 DETERMINE trx_type (DYNAMIC)
        // =========================

        // ambil category user (SOURCE OF TRUTH untuk vendor)
        $user = $db->table('users')
            ->select('category_id')
            ->where('id', $order['user_id'])
            ->get()
            ->getRowArray();

        $categoryId = $user['category_id'] ?? 0;

        // PRIORITAS 1: dari frontend (override manual)
        if (!empty($trxTypeInput)) {

            $trxType = $trxTypeInput;

        } else {

            $trxType = 'expense_other'; // fallback aman

            // =========================
            // DETECT SOURCE
            // =========================
            $hasVendor = false;
            $hasPOS = false;
            $posCategoryId = null;

            foreach ($orderItems as $item) {

                if ($item['source'] === 'vendor_items') {
                    $hasVendor = true;
                }

                if ($item['source'] === 'items') {
                    $hasPOS = true;
                    $posCategoryId = $item['category_id']; // ambil category item POS
                }
            }

            // =========================
            // CASE 1: POS (SALES)
            // =========================
            if ($hasPOS && $posCategoryId) {

                // ambil trx_type dari mapping category_trx_map
                $trxMap = $db->table('category_trx_map')
                    ->where('category_id', $posCategoryId)
                    ->get()
                    ->getResultArray();

                foreach ($trxMap as $map) {

                    // ambil trx_type yang ada di transaction_account_map
                    $exists = $db->table('transaction_account_map')
                        ->where('company_id', $companyId)
                        ->where('trx_type', $map['trx_type'])
                        ->countAllResults();

                    if ($exists) {
                        $trxType = $map['trx_type'];
                        break;
                    }
                }
            }

            // =========================
            // CASE 2: VENDOR (PURCHASE / EXPENSE)
            // =========================
            elseif ($hasVendor && $categoryId) {

                $trxMaps = $db->table('category_trx_map')
                    ->where('category_id', $categoryId)
                    ->get()
                    ->getResultArray();

                foreach ($trxMaps as $map) {

                    $exists = $db->table('transaction_account_map')
                        ->where('company_id', $companyId)
                        ->where('trx_type', $map['trx_type'])
                        ->countAllResults();

                    if ($exists) {
                        $trxType = $map['trx_type'];
                        break;
                    }
                }
            }
        }

        // if ($DEBUG) dd('TRX TYPE', $trxType);

        $trxExists = $db->table('transaction_account_map')
            ->where('company_id', $companyId)
            ->where('trx_type', $trxType)
            ->countAllResults();

        if (!$trxExists) {
            return $this->error('trxType tidak valid: ' . $trxType);
        }

        // =========================
        // 🔥 CHECK EXISTING
        // =========================
        $existingSales = $db->table('transactions')
            ->where('reference_no', $order['order_number'])
            ->whereIn('trx_type', [
                $trxType,
                $trxType . '_partial'
            ])
            ->countAllResults();

        // =========================
        // GET CASH ACCOUNT
        // =========================
        $kasAccount = $coaModel
            ->where('company_id', $companyId)
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
                    'company_id'         => $companyId,
                    'branch_id'          => $order['branch_id'],
                    'branch_name'        => $data['branch_name'],
                    'trx_date'           => date('Y-m-d'),

                    'trx_type'           => $trxType . '_partial',

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
                    'company_id'         => $companyId,
                    'branch_id'          => $order['branch_id'],
                    'branch_name'        => $data['branch_name'],
                    'trx_date'           => date('Y-m-d'),

                    'trx_type'           => $trxType,

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
                    'company_id'         => $companyId,
                    'branch_id'          => $order['branch_id'],
                    'branch_name'        => $data['branch_name'], // 🔥 FIX
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

        $builder = $db->table('orders')
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
            ->join('users', 'users.id = orders.user_id', 'left')
            ->join('branches', 'branches.id = orders.branch_id', 'left')
            ->join('payments', 'payments.order_id = orders.id', 'left');

        // =========================
        // FILTER COMPANY
        // =========================
        if (!empty(session('company_id'))) {
            $builder->where('branches.company_id', session('company_id'));
        }

        // =========================
        // FILTER BRANCH
        // =========================
        if (
            !session('is_super_admin') &&
            !empty(session('branch_id'))
        ) {
            $builder->where(
                'branches.id',
                session('branch_id')
            );
        }

        $orders = $builder
            ->orderBy('orders.id', 'DESC')
            ->get()
            ->getResultArray();

        $result = [];

        foreach ($orders as $row) {

            $date = date('Y-m-d', strtotime($row['created_at']));
            $time = date('H:i:s', strtotime($row['created_at']));

            // =========================
            // PAYMENT MAPPING
            // =========================
            $payment = 2;

            if ($row['payment_status'] == 'paid') {
                $payment = 1;
            }

            if ($row['payment_status'] == 'failed') {
                $payment = 3;
            }

            // =========================
            // ORDER STATUS MAPPING
            // =========================
            $status = 1;

            if ($row['status'] == 'paid') {
                $status = 2;
            }

            if ($row['status'] == 'processing') {
                $status = 3;
            }

            if ($row['status'] == 'ready') {
                $status = 4;
            }

            $result[] = [
                "id"            => $row['id'],
                "order"         => $row['order_number'],
                "date"          => $date,
                "time"          => $time,
                "customer"      => $row['customer'],
                "email"         => $row['email'],
                "avatar"        => $row['photo'],
                "payment"       => $payment,
                "status"        => $status,
                "method"        => $row['payment_method'],
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