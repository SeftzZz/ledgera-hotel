<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CompanyModel;
use CodeIgniter\HTTP\ResponseInterface;
use App\Services\TransactionService;

class CompanyController extends BaseController
{
    protected CompanyModel $model;

    public function __construct()
    {
        $this->model = new CompanyModel();
    }

    public function index()
    {
        return $this->render('master_data/company/index', [
            'title' => 'Company'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | POST /api/companies/datatable
    |--------------------------------------------------------------------------
    */
    public function datatable(): ResponseInterface
    {
        $request = service('request');

        $searchValue = $request->getPost('search')['value'] ?? null;
        $length      = (int) $request->getPost('length');
        $start       = (int) $request->getPost('start');
        $draw        = (int) $request->getPost('draw');
        $order       = $request->getPost('order');

        $orderColumns = [
            null,
            'company_name',
            'created_at',
            null
        ];

        $builder = $this->model
            ->where('id', session('company_id'))
            ->where('deleted_at', null);

        // TOTAL
        $recordsTotal = (clone $builder)->countAllResults(false);

        // SEARCH
        if ($searchValue) {
            $builder->groupStart()
                ->like('company_name', $searchValue)
            ->groupEnd();
        }

        $recordsFiltered = (clone $builder)->countAllResults(false);

        // ORDER
        if ($order) {
            $idx = (int) $order[0]['column'];
            if (!empty($orderColumns[$idx])) {
                $builder->orderBy($orderColumns[$idx], $order[0]['dir']);
            }
        } else {
            $builder->orderBy('id', 'DESC');
        }

        $data = $builder
            ->limit($length, $start)
            ->get()
            ->getResultArray();

        // FORMAT RESULT
        $result = [];
        $no = $start + 1;

        foreach ($data as $row) {

            $action = '
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-icon btn-primary btn-edit" data-id="'.$row['id'].'">
                        <i class="ti ti-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-icon btn-danger btn-delete" data-id="'.$row['id'].'">
                        <i class="ti ti-trash"></i>
                    </button>
                    <button class="btn btn-sm btn-icon btn-warning btn-loan" 
                            data-id="'.$row['id'].'" 
                            title="Loan / Installment">
                        <i class="ti ti-credit-card"></i>
                    </button>
                </div>
            ';

            $result[] = [
                'no'            => $no++.'.',
                'company_code'  => esc($row['company_code']),
                'company_name'  => esc($row['company_name']),
                'company_addr'  => esc($row['company_addr']),
                'created_at'    => date('d-m-Y', strtotime($row['created_at'] ?? 'now')),
                'action'        => $action
            ];
        }

        return $this->response->setJSON([
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $result
        ]);
    }

    public function store(): ResponseInterface
    {
        try {

            $request = service('request');
            $db      = \Config\Database::connect();

            $companyCode = trim($request->getPost('company_code'));
            $companyName = trim($request->getPost('company_name'));
            $companyAddr = trim($request->getPost('company_addr'));

            if (empty($companyCode) || empty($companyName)) {

                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Company code and name are required'
                ]);
            }

            // =========================================
            // CHECK DUPLICATE
            // =========================================
            $exists = $this->model
                ->where('company_code', $companyCode)
                ->where('deleted_at', null)
                ->first();

            if ($exists) {

                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Company code already exists'
                ]);
            }

            $db->transBegin();

            // =========================================
            // INSERT COMPANY
            // =========================================
            $this->model->insert([
                'company_code' => $companyCode,
                'company_name' => $companyName,
                'company_addr' => $companyAddr,
                'is_active'    => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'created_by'   => session('user_id') ?? 0,
                'deleted_at'   => null
            ]);

            $companyId = $this->model->getInsertID();

            // =========================================
            // DUPLICATE FISCAL YEARS
            // =========================================
            $fiscalYears = $db->table('fiscal_years')
                ->where('company_id', 1)
                ->orderBy('id', 'ASC')
                ->get()
                ->getResultArray();

            foreach ($fiscalYears as $fy) {

                unset($fy['id']);

                $fy['company_id'] = $companyId;

                $db->table('fiscal_years')->insert($fy);
            }

            // =========================================
            // GET COA TEMPLATE
            // =========================================
            $coaTemplate = $db->table('coa')
                ->where('company_id', 1)
                ->where('deleted_at', null)
                ->orderBy('id', 'ASC')
                ->get()
                ->getResultArray();

            // =========================================
            // DUPLICATE COA
            // =========================================
            $coaIdMap = [];

            foreach ($coaTemplate as $coa) {

                $oldId = $coa['id'];

                unset($coa['id']);

                $coa['company_id'] = $companyId;
                $coa['parent_id']  = null;

                $db->table('coa')->insert($coa);

                $newId = $db->insertID();

                $coaIdMap[$oldId] = $newId;
            }

            // =========================================
            // UPDATE PARENT ID
            // =========================================
            foreach ($coaTemplate as $coa) {

                if (!empty($coa['parent_id'])) {

                    $oldId       = $coa['id'];
                    $oldParentId = $coa['parent_id'];

                    $newId       = $coaIdMap[$oldId];
                    $newParentId = $coaIdMap[$oldParentId] ?? null;

                    $db->table('coa')
                        ->where('id', $newId)
                        ->update([
                            'parent_id' => $newParentId
                        ]);
                }
            }

            // =========================================
            // GET TRANSACTION ACCOUNT MAP TEMPLATE
            // =========================================
            $trxMaps = $db->table('transaction_account_map')
                ->where('company_id', 1)
                ->orderBy('id', 'ASC')
                ->get()
                ->getResultArray();

            // =========================================
            // DUPLICATE TRANSACTION ACCOUNT MAP
            // =========================================
            foreach ($trxMaps as $trx) {

                unset($trx['id']);

                $trx['company_id'] = $companyId;

                // REMAP COA IDS
                $trx['debit_account_id'] = !empty($trx['debit_account_id'])
                    ? ($coaIdMap[$trx['debit_account_id']] ?? null)
                    : null;

                $trx['credit_account_id'] = !empty($trx['credit_account_id'])
                    ? ($coaIdMap[$trx['credit_account_id']] ?? null)
                    : null;

                $trx['service_account_id'] = !empty($trx['service_account_id'])
                    ? ($coaIdMap[$trx['service_account_id']] ?? null)
                    : null;

                $trx['interest_account_id'] = !empty($trx['interest_account_id'])
                    ? ($coaIdMap[$trx['interest_account_id']] ?? null)
                    : null;

                $trx['fee_account_id'] = !empty($trx['fee_account_id'])
                    ? ($coaIdMap[$trx['fee_account_id']] ?? null)
                    : null;

                $db->table('transaction_account_map')
                    ->insert($trx);
            }

            // =========================================
            // COMMIT
            // =========================================
            if ($db->transStatus() === false) {

                $db->transRollback();

                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Failed to create company'
                ]);
            }

            $db->transCommit();

            return $this->response->setJSON([
                'status'     => true,
                'message'    => 'Company successfully added',
                'company_id' => $companyId
            ]);

        } catch (\Throwable $e) {

            if (isset($db)) {
                $db->transRollback();
            }

            return $this->response->setJSON([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function loan(): ResponseInterface
    {
        try {

            $request = service('request');

            $companyId = $request->getPost('company_id');
            $amount = (float) $request->getPost('amount');
            $tenor  = (int) $request->getPost('tenor');
            $start  = $request->getPost('start_date');

            if ($amount <= 0 || $tenor <= 0 || empty($start)) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Invalid input'
                ]);
            }

            $startDate = date('Y-m-01', strtotime($start));

            $trxService = new TransactionService();

            // 1. CREDIT BANK
            $trxService->create([
                'company_id' => $companyId,
                'branch_name'=> session('branch_name'),
                'trx_type'   => 'credit_bank',
                'amount'     => $amount,
                'trx_date'   => $startDate,
                'reference_no' => 'LOAN-' . time(),
                'category_id' => 5,
                'payment_account_id' => 35,
            ]);

            // 2. OPTIONAL: GENERATE SCHEDULE (TAPI DRAFT)
            $perMonth = floor(($amount / $tenor) * 100) / 100; // fix 2 decimal
            $totalAllocated = 0;

            $interestRate = 0.03;
            $feeRate      = 0.009;

            $outstanding = $amount;

            $interestPerMonth = round($amount * $interestRate, 2);

            for ($i = 0; $i < $tenor; $i++) {

                $date = date('Y-m-d', strtotime("+".($i+1)." month", strtotime($startDate)));

                // =========================
                // PRINCIPAL (TETAP)
                // =========================
                if ($i === $tenor - 1) {
                    $principal = round($outstanding, 2);
                } else {
                    $principal = $perMonth;
                }

                // =========================
                // 🔥 BUNGA FLAT (TETAP)
                // =========================
                $interestAmount = $interestPerMonth;

                // =========================
                // FEE
                // =========================
                $feeAmount = round($principal * $feeRate, 2);

                // =========================
                // SAVE
                // =========================
                $trxService->create([
                    'company_id'         => $companyId,
                    'branch_name'        => session('branch_name'),
                    'trx_type'           => 'loan_installment',
                    'amount'             => $principal + $interestAmount + $feeAmount,
                    'principal'          => $principal,
                    'interest'           => $interestAmount,
                    'fee'                => $feeAmount,
                    'trx_date'           => $date,
                    'reference_no'       => 'LOAN-INSTALL-' . $i,
                    'category_id'        => 5,
                    'payment_account_id' => 1,
                    'status'             => 'draft'
                ]);

                // tetap dikurangi (buat principal saja)
                $outstanding -= $principal;
            }

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Loan & installment created successfully',
                'meta' => json_encode([
                    'principal' => $principal,
                    'interest'  => $interestAmount,
                    'fee'       => $feeAmount
                ])
            ]);

        } catch (\Throwable $e) {

            return $this->response->setJSON([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
