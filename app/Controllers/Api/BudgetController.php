<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Services\DashboardService;

class BudgetController extends BaseController
{
    public function limit()
    {
        try {

            $service = new DashboardService();
            $db = \Config\Database::connect();

            // =========================
            // 🔥 AMBIL DATA DARI REQUEST
            // =========================
            $input = $this->request->getJSON(true);

            if (!$input) {
                $input = $this->request->getPost();
            }

            $companyId  = $input['company_id'] ?? null;
            $branchName = $input['branch_name'] ?? null;
            $department = $input['department'] ?? null;

            if (!$companyId || !$branchName || !$department) {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'company_id, branch_name, department wajib'
                ]);
            }

            // =========================
            // 🔥 CARI BRANCH BY NAME
            // =========================
            $branch = $db->query("
                SELECT 
                    b.id,
                    b.company_id,
                    b.branch_name,
                    bt.target
                FROM branches b
                LEFT JOIN branches_target bt 
                    ON bt.id = (
                        SELECT id 
                        FROM branches_target 
                        WHERE branch_id = b.id
                        ORDER BY id ASC
                        LIMIT 1
                    )
                WHERE b.company_id = ?
                AND b.branch_name = ?
                LIMIT 1
            ", [$companyId, $branchName])->getRowArray();

            if (!$branch) {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Branch tidak ditemukan'
                ]);
            }

            $branchId = (int)$branch['id'];

            // =========================
            // 🔥 MAP DEPARTMENT → CATEGORY_ID
            // =========================
            $categoryRow = $db->table('categories')
                ->select('id, name')
                ->where('branch_id', $branchId)
                ->where('LOWER(name)', strtolower($department)) // 🔥 lebih aman
                ->limit(1)
                ->get()
                ->getRowArray();

            if (!$categoryRow) {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Category tidak ditemukan'
                ]);
            }

            $categoryId = (int)$categoryRow['id'];

            // =========================
            // 🔥 AMBIL DASHBOARD DATA
            // =========================
            $data = $service->getDashboardData([
                'company_id'  => $companyId,
                'branch_id'   => $branchId,
                'category_id' => $categoryId
            ]);

            $departments = $data['departmentSummary'] ?? [];

            // =========================
            // 🔥 CARI DEPARTMENT
            // =========================
            $found = null;

            foreach ($departments as $d) {
                if (strtolower($d['name']) === strtolower($department)) {
                    $found = $d;
                    break;
                }
            }

            if (!$found) {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Department tidak ditemukan'
                ]);
            }

            // =========================
            // 🔥 SPEND LIMIT
            // =========================
            $limit     = (float) $found['limit_spend'];
            $used      = (float) $found['expense'];
            $remaining = max($limit - $used, 0);

            // =========================
            // 🔥 WORKER (PAKAI SERVICE)
            // =========================
            $workerData = $service->calculateWorker($found);

            // =========================
            // 🔥 RESPONSE FINAL
            // =========================
            return $this->response->setJSON([
                'status' => true,
                'data'   => [
                    'branch_id'   => $branchId,
                    'branch_name' => $branch['branch_name'],
                    'target'      => (float) $branch['target'],

                    // SPEND
                    'limit'       => $limit,
                    'used'        => $used,

                    // WORKER 🔥 dari service
                    ...$workerData
                ]
            ]);

        } catch (\Throwable $e) {

            return $this->response->setJSON([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}