<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TransactionModel;
use App\Models\TransactionAccountMapModel;

class TransactionController extends BaseController
{
    protected TransactionModel $transactionModel;

    public function __construct()
    {
        $this->transactionModel = new TransactionModel();
    }

    public function index()
    {
        $coaModel = new \App\Models\CoaModel();
        $taxModel = new \App\Models\TaxCodeModel();

        $paymentAccounts = $coaModel
            ->where('company_id', session('company_id'))
            ->whereIn('account_type', ['asset','liability'])
            ->where('parent_id IS NOT NULL')
            ->orderBy('account_code', 'ASC')
            ->findAll();

        $taxCodes = $taxModel
            ->select('tax_codes.*, coa.account_name as coa_account_name')
            ->join('coa', 'coa.id = tax_codes.coa_account_id', 'left')
            ->where('tax_codes.is_active', 1)
            ->where('tax_codes.deleted_at', '0000-00-00 00:00:00')
            ->findAll();

        return view('accounting/transaction/index', [
            'title'           => 'Transaction',
            'trxTypes'        => (new \App\Models\TransactionAccountMapModel())->findAll(),
            'paymentAccounts' => $paymentAccounts,
            'taxCodes'        => $taxCodes   // 🔥 tambah ini
        ]);
    }
    
    public function datatable()
    {
        $request = service('request');

        $searchValue = $request->getPost('search')['value'] ?? null;
        $length      = (int)$request->getPost('length');
        $start       = (int)$request->getPost('start');
        $draw        = (int)$request->getPost('draw');

        $builder = $this->transactionModel
            ->select('transactions.*');

        $recordsTotal = $this->transactionModel->countAll();

        if ($searchValue) {
            $builder->groupStart()
                ->like('reference_no', $searchValue)
                ->orLike('trx_type', $searchValue)
            ->groupEnd();
        }

        $recordsFiltered = $builder->countAllResults(false);

        $rows = $builder
            ->orderBy('id','DESC')
            ->limit($length,$start)
            ->get()
            ->getResultArray();

        $result = [];
        $no = $start + 1;

        foreach ($rows as $row) {

            $result[] = [
                'no'           => $no++.'.',
                'reference_no' => esc($row['reference_no']),
                'date'         => date('d-m-Y', strtotime($row['trx_date'])),
                'type'         => esc($row['trx_type']),
                'amount'       => number_format($row['amount'], 2),
                // 'action'       => '
                //     <button class="btn btn-sm btn-primary btn-view" data-id="'.$row['id'].'">View</button>
                // '
            ];
        }

        return $this->response->setJSON([
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $result
        ]);
    }

    public function store()
    {
        try {

            $service = new \App\Services\TransactionService();

            $branchId = $this->request->getPost('branch_id');

            $trxId = $service->create([
                'company_id'         => $this->request->getPost('company_id'),
                'branch_id'          => $branchId > 0 ? $branchId : null,
                'branch_name'        => $this->request->getPost('branch_name'),
                'trx_date'           => $this->request->getPost('trx_date'),
                'trx_type'           => $this->request->getPost('trx_type'),
                'reference_no'       => $this->request->getPost('reference_no'),
                'amount'             => (float) $this->request->getPost('amount'),
                'payment_account_id' => $this->request->getPost('payment_account_id'),

                // 🔥 TAX
                'tax_code_id'        => $this->request->getPost('tax_code_id'),
                'tax_mode'           => $this->request->getPost('tax_mode') ?? 'exclusive'
            ]);

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Transaction & Journal created successfully',
                'id'      => $trxId
            ]);

        } catch (\Throwable $e) {

            return $this->response->setJSON([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
