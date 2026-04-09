<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\JournalHeaderModel;
use App\Models\JournalDetailModel;

class JournalController extends BaseController
{
    protected JournalHeaderModel $headerModel;
    protected JournalDetailModel $detailModel;

    public function __construct()
    {
        $this->headerModel = new JournalHeaderModel();
        $this->detailModel = new JournalDetailModel();
    }

    public function index()
    {
        return view('accounting/journal/index', [
            'title' => 'Journal'
        ]);
    }

    public function datatable()
    {
        $request = service('request');

        $searchValue = $request->getPost('search')['value'] ?? null;
        $length      = (int)$request->getPost('length');
        $start       = (int)$request->getPost('start');
        $draw        = (int)$request->getPost('draw');

        $builder = $this->headerModel
            ->select('journal_headers.*, 
                      COALESCE(SUM(journal_details.debit),0) as total')
            ->join('journal_details','journal_details.journal_id = journal_headers.id','left')
            ->groupBy('journal_headers.id');

        $recordsTotal = (clone $builder)->countAllResults(false);

        if ($searchValue) {
            $builder->groupStart()
                ->like('journal_no', $searchValue)
                ->orLike('description', $searchValue)
            ->groupEnd();
        }

        $recordsFiltered = (clone $builder)->countAllResults(false);

        $rows = $builder
            ->orderBy('journal_headers.id','DESC')
            ->limit($length,$start)
            ->get()
            ->getResultArray();

        $result = [];
        $no = $start + 1;

        foreach ($rows as $row) {

            $badge = match($row['status']) {
                'draft'     => '<span class="badge bg-label-secondary">Draft</span>',
                'submitted' => '<span class="badge bg-label-warning">Submitted</span>',
                'approved'  => '<span class="badge bg-label-info">Approved</span>',
                'posted'    => '<span class="badge bg-label-success">Posted</span>',
                default     => '<span class="badge bg-label-dark">Unknown</span>',
            };

            $btnPost = $row['status'] === 'posted'
                ? '<button class="btn btn-sm btn-success" disabled>Posted</button>'
                : '<button class="btn btn-sm btn-success btn-post" data-id="'.$row['id'].'">Post</button>';

            $result[] = [
                'no'          => $no++.'.',
                'journal_no'  => esc($row['journal_no']),
                'date'        => date('d-m-Y', strtotime($row['journal_date'])),
                'description' => esc($row['description']),
                'status'      => $badge,
                'action'      => '
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-primary btn-view" data-id="'.$row['id'].'">View</button>
                        '.$btnPost.'
                    </div>
                '
            ];
        }

        return $this->response->setJSON([
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $result
        ]);
    }

    public function submit($id)
    {
        $journal = $this->header->find($id);

        (new ApprovalService())->init(
            'journal',
            $id,
            $journal['total_amount']
        );

        return response()->setJSON([
            'status' => true,
            'message' => 'Submitted for approval'
        ]);
    }

    public function post($id)
    {
        $db = \Config\Database::connect();

        // =========================
        // GET JOURNAL
        // =========================
        $journal = $this->headerModel->find($id);

        if (!$journal) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Journal not found'
            ]);
        }

        // 🔒 ANTI LOOP
        if (!empty($journal['from_journal'])) {
            return $this->response->setJSON([
                'status' => true,
                'message' => 'Auto journal skipped'
            ]);
        }

        if ($journal['status'] === 'posted') {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Already posted'
            ]);
        }

        // =========================
        // DETAILS
        // =========================
        $details = $db->table('journal_details')
            ->where('journal_id', $id)
            ->get()
            ->getResultArray();

        if (!$details) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Journal has no details'
            ]);
        }

        // =========================
        // DETECT ACCOUNT
        // =========================
        $platformFeeAmount = 0;
        $depositAmount     = 0;

        foreach ($details as $d) {

            // 🔥 82 = platform fee
            if ((int)$d['account_id'] === 82) {
                $platformFeeAmount += (float)$d['debit'];
            }

            // 🔥 55 = deposit
            if ((int)$d['account_id'] === 55) {
                $depositAmount += (float)$d['credit'];
            }
        }

        // =========================
        // 🔥 LOAD COA MAP
        // =========================
        $coaList = $db->table('coa')
            ->select('id, account_name, parent_id, account_type')
            ->get()
            ->getResultArray();

        $coaMap = [];
        foreach ($coaList as $c) {
            $coaMap[$c['id']] = $c;
        }

        // =========================
        // 🔥 HELPER: TRACE ROOT
        // =========================
        $getRootType = function($accountId) use ($coaMap) {

            $visited = [];

            while (isset($coaMap[$accountId])) {

                if (in_array($accountId, $visited)) break;
                $visited[] = $accountId;

                $coa = $coaMap[$accountId];

                if (empty($coa['parent_id'])) {
                    return $coa['account_type']; // 🔥 pakai account_type
                }

                $accountId = $coa['parent_id'];
            }

            return null;
        };

        // =========================
        // 🔥 DETECT ACCOUNT
        // =========================
        $inventoryAccounts = [];
        $expenseAccounts   = [];

        foreach ($details as $d) {

            $accountId = (int)$d['account_id'];

            // 🔥 skip tetap
            if (in_array($accountId, [82, 55])) continue;

            if (!isset($coaMap[$accountId])) continue;

            $coa = $coaMap[$accountId];
            $rootType = $getRootType($accountId);

            // =========================
            // INVENTORY (ASSET)
            // =========================
            if ($rootType === 'asset') {

                // hanya yang benar2 persediaan
                if (stripos($coa['account_name'], 'persediaan') !== false) {
                    $inventoryAccounts[] = [
                        'account_id'   => $accountId,
                        'account_name' => $coa['account_name'],
                        'debit'        => $d['debit'],
                        'credit'       => $d['credit']
                    ];
                }
            }

            // =========================
            // EXPENSE
            // =========================
            if ($rootType === 'expense') {

                $expenseAccounts[] = [
                    'account_id'   => $accountId,
                    'account_name' => $coa['account_name'],
                    'debit'        => $d['debit'],
                    'credit'       => $d['credit']
                ];
            }
        }

        // =========================
        // BRANCH
        // =========================
        $branchId = $journal['branch_id'];

        // =========================
        // PAYMENT ACCOUNT
        // =========================
        $paymentAccountId = null;

        foreach ($details as $d) {
            if ((int)$d['account_id'] !== 82 && (float)$d['credit'] > 0) {
                $paymentAccountId = $d['account_id'];
                break;
            }
        }

        if (!$paymentAccountId && !empty($journal['payment_account_id'])) {
            $paymentAccountId = $journal['payment_account_id'];
        }

        // =========================
        // UPDATE STATUS
        // =========================
        $this->headerModel->update($id, [
            'status'    => 'posted',
            'posted_at' => date('Y-m-d H:i:s'),
            'posted_by' => session()->get('user_id')
        ]);

        // =========================
        // 🔥 AUTO CREATE FEE → ORDER
        // =========================
        if ($platformFeeAmount > 0) {

            $service      = new \App\Services\TransactionService();
            $orderService = new \App\Services\OrderService();
            $userModel    = new \App\Models\UserModel();
            $cartModel    = new \App\Models\CartModel();

            // =========================
            // TRANSACTION
            // =========================
            $service->create([
                'company_id'         => $journal['company_id'],
                'branch_id'          => 4,
                'branch_name'        => 'HeyWork',
                'trx_date'           => $journal['journal_date'],
                'trx_type'           => 'sales_service',
                'reference_no'       => $journal['journal_no'],
                'amount'             => $platformFeeAmount,
                'payment_account_id' => $paymentAccountId,
                'tax_code_id'        => null,
                'tax_mode'           => 'exclusive',
                'from_journal'       => true
            ]);

            // =========================
            // CATEGORY IT
            // =========================
            $category = $db->table('categories')
                ->where('name', 'IT')
                ->where('status', 'active')
                ->get()
                ->getRowArray();

            // =========================
            // USER
            // =========================
            $user = $userModel
                ->where('branch_id', 4)
                ->where('category_id', $category['id'])
                ->first();

            $userId = $user ? $user['id'] : $userModel->insert([
                'branch_id'   => 4,
                'category_id' => $category['id'],
                'name'        => 'User IT',
                'email'       => 'it@heywork.id',
                'password'    => password_hash('123456', PASSWORD_DEFAULT),
                'role'        => 'customer',
                'status'      => 'active'
            ]);

            // =========================
            // CART
            // =========================
            $cartId = $cartModel->insert([
                'user_id'   => $userId,
                'branch_id' => 4,
                'status'    => 'active'
            ], true);

            // =========================
            // ITEM
            // =========================
            $item = $db->table('items')
                ->where('category_id', $category['id'])
                ->orderBy('id', 'ASC')
                ->get()
                ->getRow();

            if (!$item) {
                return $this->error('Item IT tidak ditemukan');
            }

            // =========================
            // CART ITEM
            // =========================
            $db->table('cart_items')->insert([
                'cart_id'  => $cartId,
                'item_id'  => $item->id,
                'quantity' => 1,
                'price'    => $platformFeeAmount
            ]);

            // =========================
            // ORDER NUMBER = LINK
            // =========================
            $orderNumber = $journal['journal_no']; // 🔥 penting

            // =========================
            // CHECKOUT
            // =========================
            $order = $orderService->checkout([
                'cart_id'        => $cartId,
                'user_id'        => $userId,
                'order_number'   => $orderNumber,
                'payment_method' => 'cash',
                'branch_id'      => 4
            ]);
        }

        // =========================
        // 🔥 UPDATE DEPOSIT (ACCOUNT 55)
        // =========================
        if ($depositAmount != 0) {

            $trx = $db->table('transactions')
                ->where('journal_id', $id)
                ->get()
                ->getRowArray();

            if (!$trx) {
                return $this->error('Transaction tidak ditemukan');
            }

            $order = $db->table('orders')
                ->where('order_number', $trx['reference_no'])
                ->get()
                ->getRowArray();

            if (!$order) {
                return $this->error('Order tidak ditemukan dari reference');
            }

            $db->table('orders')
                ->where('id', $order['id'])
                ->update([
                    'deposit' => $depositAmount,
                    'status'  => 'paid'
                ]);

            $db->table('payments')
                ->where('order_id', $order['id'])
                ->update([
                    'status'  => 'paid',
                    'paid_at' => date('Y-m-d H:i:s')
                ]);
        }

        // =========================
        // 🔥 GET TRANSACTION
        // =========================
        $trx = $db->table('transactions')
            ->where('journal_id', $id)
            ->get()
            ->getRowArray();

        $pengajuan = null;
        $pengajuanId = null;
        $pengajuanDetails = [];

        if ($trx && !empty($trx['reference_no'])) {

            // ambil PG-6 → 6
            if (preg_match('/PG-(\d+)/', $trx['reference_no'], $match)) {

                $pengajuanId = (int)$match[1];

                // =========================
                // HEADER PENGAJUAN
                // =========================
                $pengajuan = $db->table('form_pengajuan')
                    ->where('id', $pengajuanId)
                    ->get()
                    ->getRowArray();

                // =========================
                // DETAIL
                // =========================
                $pengajuanDetails = $db->table('form_pengajuan_detail')
                    ->where('pengajuan_id', $pengajuanId)
                    ->get()
                    ->getResultArray();

                $formPurchasing = $db->table('form_purchasing')
                    ->where('pengajuan_id', $pengajuanId)
                    ->orderBy('id', 'DESC')
                    ->get()
                    ->getRowArray();

                $formPurchasingId = $formPurchasing['id'] ?? 0;

                if (!$formPurchasingId) {
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => 'Form purchasing tidak ditemukan'
                    ]);
                }

                if (!empty($pengajuanId)) {
                    $db->table('form_pengajuan')
                        ->where('id', $pengajuanId)
                        ->update([
                            'status' => 'Selesai',
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                }

                // ambil vendor_id
                $vendorItemIds = array_column($pengajuanDetails, 'vendor_item_id');

                $vendorItems = $db->table('vendor_items')
                    ->whereIn('id', $vendorItemIds)
                    ->get()
                    ->getResultArray();

                $vendorMap = [];
                foreach ($vendorItems as $v) {
                    $vendorMap[$v['id']] = $v;
                }

                if (empty($pengajuanDetails)) {
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => 'Detail pengajuan tidak ditemukan'
                    ]);
                }

                foreach ($pengajuanDetails as $item) {

                    if (!isset($vendorMap[$item['vendor_item_id']])) {
                        continue; // atau throw error
                    }

                    $vendorId = $vendorMap[$item['vendor_item_id']]['vendor_id'] ?? 0;

                    $existing = $db->table('inventori')
                        ->where('vendor_item_id', $item['vendor_item_id'])
                        ->where('form_purchasing_id', $formPurchasingId)
                        ->where('sparepart', $item['sparepart'])
                        ->where('is_delete', 0)
                        ->get()
                        ->getRowArray();

                    if ($existing) {

                        $db->table('inventori')
                            ->where('id', $existing['id'])
                            ->set('qty', 'qty + ' . (int)$item['qty'], false)
                            ->update();

                    } else {

                        $db->table('inventori')->insert([
                            'vendor_id'          => $vendorId,
                            'vendor_item_id'     => $item['vendor_item_id'],
                            'sparepart'          => $item['sparepart'],
                            'kondisi'            => $item['kondisi'],
                            'qty'                => (int)$item['qty'],
                            'is_used'            => 0,
                            'is_delete'          => 0,
                            'form_purchasing_id' => $formPurchasingId,
                            'created_at'         => date('Y-m-d H:i:s'),
                            'updated_at'         => date('Y-m-d H:i:s')
                        ]);
                    }
                }
            }
        }

        $this->emitWS('journal_posted', $journal['branch_id']);

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Journal posted successfully'
        ]);
    }

    public function detail($id)
    {
        $db = \Config\Database::connect();

        $header = $this->headerModel
            ->select('journal_headers.*, 
                      transactions.id as transaction_id,
                      transactions.amount as base_amount')
            ->join('transactions', 'transactions.journal_id = journal_headers.id', 'left')
            ->where('journal_headers.id', $id)
            ->first();

        if (!$header) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Journal not found'
            ]);
        }

        $taxSummary = 0;

        if (!empty($header['transaction_id'])) {

            $taxRow = $db->table('transaction_taxes')
                ->select('SUM(tax_amount) as total_tax')
                ->where('transaction_id', $header['transaction_id'])
                ->get()
                ->getRow();

            $taxSummary = $taxRow->total_tax ?? 0;
        }

        $header['tax_amount']   = (float) $taxSummary;
        $header['total_amount'] = (float) $header['base_amount'] + (float) $taxSummary;

        $details = $this->detailModel
            ->select('journal_details.*, coa.account_name')
            ->join('coa','coa.id = journal_details.account_id')
            ->where('journal_id',$id)
            ->findAll();

        return $this->response->setJSON([
            'status'  => true,
            'header'  => $header,
            'details' => $details
        ]);
    }

    function getRootAccountType($accountId, $coaMap)
    {
        $visited = [];

        while (isset($coaMap[$accountId])) {

            // 🔒 anti infinite loop
            if (in_array($accountId, $visited)) break;
            $visited[] = $accountId;

            $coa = $coaMap[$accountId];

            // kalau sudah root (tidak punya parent)
            if (empty($coa['parent_id'])) {
                return strtolower($coa['account_name']); 
            }

            $accountId = $coa['parent_id'];
        }

        return null;
    }

    private function emitWS($type, $branchId)
    {
        $payload = [
            'type' => $type,
            'branch_id' => $branchId
        ];

        $ch = curl_init('http://localhost:4003/emit');

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode($payload),
        ]);

        curl_exec($ch);
        curl_close($ch);
    }
}
