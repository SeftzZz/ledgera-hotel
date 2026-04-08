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

        // 🔒 ANTI LOOP (AUTO JOURNAL)
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
        // 🔥 AMBIL DETAILS
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
        // 🔥 DETECT PLATFORM FEE (ACCOUNT 82)
        // =========================
        $platformFeeAmount = 0;

        foreach ($details as $d) {
            if ((int)$d['account_id'] === 82) {
                $platformFeeAmount += (float)$d['debit'];
            }
        }

        // =========================
        // 🔥 RESOLVE BRANCH → HOTEL (HEYWORK)
        // =========================
        $branch = $db->table('branches')
            ->select('id, hotel_id')
            ->where('id', 4)
            ->get()
            ->getRowArray();

        if (!$branch) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Branch not found'
            ]);
        }

        // =========================
        // 🔥 DETECT PAYMENT ACCOUNT (DINAMIS)
        // =========================
        $paymentAccountId = null;

        foreach ($details as $d) {
            // cari credit selain account 82 (biasanya kas/bank)
            if ((int)$d['account_id'] !== 82 && (float)$d['credit'] > 0) {
                $paymentAccountId = $d['account_id'];
                break;
            }
        }

        // fallback
        if (!$paymentAccountId && !empty($journal['payment_account_id'])) {
            $paymentAccountId = $journal['payment_account_id'];
        }

        // =========================
        // 🔥 UPDATE STATUS POSTED
        // =========================
        $this->headerModel->update($id, [
            'status'    => 'posted',
            'posted_at' => date('Y-m-d H:i:s'),
            'posted_by' => session()->get('user_id')
        ]);

        // =========================
        // 🔥 AUTO CREATE TRANSACTION (HEYWORK CONTEXT)
        // =========================
        if ($platformFeeAmount > 0) {

            $service = new \App\Services\TransactionService();

            $service->create([
                'company_id'         => $journal['company_id'],
                'branch_id'          => 4,
                'branch_name'        => 'HeyWork',
                'trx_date'           => $journal['journal_date'],
                'trx_type'           => 'sales_service',
                'reference_no'       => $journal['journal_no'],
                'amount'             => $platformFeeAmount,

                // 🔥 dinamis
                'payment_account_id' => $paymentAccountId,

                'tax_code_id'        => null,
                'tax_mode'           => 'exclusive',

                // 🔥 anti loop
                'from_journal'       => true
            ]);

            $category = $db->table('categories')
                ->select('id, name')
                ->where('name', 'IT')
                ->where('status', 'active')
                ->get()
                ->getRowArray();

            // =========================
            // CEK USER BY EMAIL
            // =========================
            $user = $userModel
                ->where('branch_id', 4)
                ->where('category_id', $category['id'])
                ->first();

            if ($user) {

                $userId = $user['id'];

            } else {

                // =========================
                // CREATE USER BARU
                // =========================
                $userId = $userModel->insert([
                    'branch_id'   => 4,
                    'category_id' => $category['id'],
                    'name'        => 'User IT',
                    'email'       => 'it@heywork.id',
                    'phone'       => '081234567890',
                    'password'    => password_hash('123456', PASSWORD_DEFAULT),
                    'role'        => 'customer',
                    'status'      => 'active'
                ]);
            }

            $cartId = $cartModel->insert([
                'user_id'   => $userId,
                'branch_id' => $branchId,
                'status'    => 'active'
            ]);
        }

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
}
