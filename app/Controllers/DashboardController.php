<?php

namespace App\Controllers;

use App\Services\DashboardService;

class DashboardController extends BaseController
{
    public function index()
    {
        $service = new DashboardService();

        $data = $service->getDashboardData([
            'company_id'  => session('company_id'),
            'branch_id'   => session('branch_id'),
            'category_id' => session('category_id'),
        ]);

        return view('dashboard/index', $data);
    }

    public function data()
    {
        $service = new \App\Services\DashboardService();

        $data = $service->getDashboardData([
            'company_id'  => session('company_id'),
            'branch_id'   => session('branch_id'),
            'category_id' => session('category_id'),
        ]);

        return $this->response->setJSON($data);
    }

    public function department_expense()
    {
        $service = new DashboardService();

        $data = $service->getDashboardData([
            'company_id'  => session('company_id'),
            'branch_id'   => session('branch_id'),
            'category_id' => session('category_id'),
        ]);

        $departmentSummary = $data['departmentSummary'] ?? [];

        // =========================
        // FLATTEN EXPENSE DATA
        // =========================
        $rows = [];

        foreach ($departmentSummary as $dept) {

            if (empty($dept['expense_data'])) {
                continue;
            }

            foreach ($dept['expense_data'] as $exp) {

                $rows[] = [

                    // ======================
                    // DEPARTMENT
                    // ======================
                    'department_id'   => $dept['id'],
                    'department_name' => $dept['name'],

                    // ======================
                    // LIMIT
                    // ======================
                    'limit_spend' => $dept['limit_spend'],
                    'expense'     => $dept['expense'],

                    // ======================
                    // STATUS
                    // ======================
                    'status_spend' => $dept['status_spend'],

                    // ======================
                    // EXPENSE DETAIL
                    // ======================
                    'transaction_id' => $exp['transaction_id'],
                    'trx_type'       => $exp['trx_type'],
                    'reference_no'   => $exp['reference_no'],
                    'trx_amount'     => $exp['trx_amount'],

                    // ======================
                    // JOURNAL
                    // ======================
                    'journal' => $exp['journal'] ?? [],

                    // ======================
                    // ACCOUNT
                    // ======================
                    'account' => $exp['account'] ?? [],

                    // ======================
                    // REQUEST
                    // ======================
                    'pengajuan' => $exp['pengajuan'] ?? [],

                    // ======================
                    // ITEM
                    // ======================
                    'item' => $exp['item'] ?? [],

                    // ======================
                    // PURCHASING
                    // ======================
                    'purchasing' => $exp['purchasing'] ?? [],
                ];
            }
        }

        return $this->response->setJSON([
            'status' => true,
            'data'   => $rows
        ]);
    }
}