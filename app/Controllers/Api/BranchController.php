<?php

namespace App\Controllers\Api;

use App\Controllers\Api\BaseApiController;
use App\Models\BranchModel;
use App\Models\CategoryModel;

class BranchController extends BaseApiController
{
    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
    }

    public function index()
    {
        return $this->response->setJSON(
            (new BranchModel())->findAll()
        );
    }

    public function store()
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $data = $this->request->getPost();

        // ======================
        // VALIDASI BASIC
        // ======================
        if (
            empty($data['company_id']) ||
            empty($data['branch_code']) ||
            empty($data['branch_name'])
        ) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Field wajib belum lengkap'
            ]);
        }

        if (empty($data['targets']) || !is_array($data['targets'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Target minimal 1'
            ]);
        }

        // ======================
        // INSERT BRANCH
        // ======================
        $branchModel = new \App\Models\BranchModel();

        $branchId = $branchModel->insert([
            'company_id'  => $data['company_id'],
            'branch_code' => $data['branch_code'],
            'branch_name' => $data['branch_name']
        ], true);

        // ======================
        // INSERT MULTI TARGET
        // ======================
        $insertData = [];

        foreach ($data['targets'] as $i => $t) {

            $target = floatval($t['target'] ?? 0);
            $room   = floatval($t['room_revenue'] ?? 0);
            $fb     = floatval($t['fb_revenue'] ?? 0);
            $tax    = floatval($t['tax_service'] ?? 0);
            $margin = floatval($t['total_margin'] ?? 0);

            // ======================
            // VALIDASI PER ROW
            // ======================
            if ($target <= 0) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => "Target baris ke-" . ($i+1) . " tidak valid"
                ]);
            }

            if (($room + $fb) != 100) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => "Room + FB harus 100% (baris ke-" . ($i+1) . ")"
                ]);
            }

            if (($tax + $margin) != 100) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => "Tax + Margin harus 100% (baris ke-" . ($i+1) . ")"
                ]);
            }

            // ======================
            // OPTIONAL VALIDASI ASCENDING (RECOMMENDED)
            // ======================
            if ($i > 0) {
                $prevTarget = floatval($data['targets'][$i-1]['target'] ?? 0);

                if ($target <= $prevTarget) {
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => "Target harus naik (baris ke-" . ($i+1) . ")"
                    ]);
                }
            }

            $insertData[] = [
                'company_id'   => $data['company_id'],
                'branch_id'    => $branchId,
                'target'       => $target,
                'room_revenue' => $room,
                'fb_revenue'   => $fb,
                'tax_service'  => $tax,
                'total_margin' => $margin
            ];
        }

        // ======================
        // BATCH INSERT (OPTIMAL)
        // ======================
        $db->table('branches_target')->insertBatch($insertData);

        $db->transComplete();

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Branch & multi target berhasil dibuat',
            'id'      => $branchId
        ]);
    }

    public function update($id = null)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $data = $this->request->getPost();

        // ======================
        // VALIDASI
        // ======================
        if (empty($data['id'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'ID wajib'
            ]);
        }

        if (empty($data['company_id']) || empty($data['branch_code']) || empty($data['branch_name'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Field wajib belum lengkap'
            ]);
        }

        if (($data['room_revenue'] + $data['fb_revenue']) != 100) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Room + FB harus 100%'
            ]);
        }

        if (($data['tax_service'] + $data['total_margin']) != 100) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Tax + Margin harus 100%'
            ]);
        }

        $branchModel = new \App\Models\BranchModel();

        // ======================
        // CEK EXIST
        // ======================
        $branch = $branchModel->find($data['id']);
        if (!$branch) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Branch tidak ditemukan'
            ]);
        }

        // ======================
        // UPDATE BRANCH
        // ======================
        $branchModel->update($data['id'], [
            'company_id'  => $data['company_id'],
            'branch_code' => $data['branch_code'],
            'branch_name' => $data['branch_name']
        ]);

        // ======================
        // UPDATE / INSERT TARGET 🔥
        // ======================
        $targetTable = $db->table('branches_target');

        $existingTarget = $targetTable
            ->where('branch_id', $data['id'])
            ->get()
            ->getRowArray();

        if ($existingTarget) {
            // UPDATE
            $targetTable->where('branch_id', $data['id'])->update([
                'target'       => $data['target'],
                'room_revenue' => $data['room_revenue'],
                'fb_revenue'   => $data['fb_revenue'],
                'tax_service'  => $data['tax_service'],
                'total_margin' => $data['total_margin']
            ]);
        } else {
            // INSERT (fallback)
            $targetTable->insert([
                'branch_id'    => $data['id'],
                'target'       => $data['target'],
                'room_revenue' => $data['room_revenue'],
                'fb_revenue'   => $data['fb_revenue'],
                'tax_service'  => $data['tax_service'],
                'total_margin' => $data['total_margin']
            ]);
        }

        $db->transComplete();

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Branch & target berhasil diupdate'
        ]);
    }

    public function show($id = null)
    {
        if (empty($id)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'ID wajib'
            ]);
        }

        $db = \Config\Database::connect();

        $data = $db->table('branches')
            ->select('
                branches.id,
                branches.company_id,
                branches.branch_code,
                branches.branch_name,
                bt.target,
                bt.room_revenue,
                bt.fb_revenue,
                bt.tax_service,
                bt.total_margin
            ')
            ->join('branches_target bt', 'bt.branch_id = branches.id', 'left')
            ->where('branches.id', $id)
            ->get()
            ->getRowArray();

        if (!$data) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        return $this->response->setJSON([
            'status' => true,
            'data'   => $data
        ]);
    }

    public function ratio($id)
    {
        $db = \Config\Database::connect();

        $spend = $db->table('ratio_spend')
            ->where('hotel_id', $id)
            ->where('is_active', 1)
            ->where('label', 'OVER')
            ->get()
            ->getResultArray();

        $worker = $db->table('ratio_worker')
            ->where('hotel_id', $id)
            ->where('is_active', 1)
            ->where('label', 'OVER')
            ->get()
            ->getResultArray();

        $result = [];

        foreach ($spend as $row) {
            $dept = $row['department_category'];

            if (!isset($result[$dept])) {
                $result[$dept] = [
                    'ratio_spend' => [],
                    'ratio_worker' => []
                ];
            }

            $result[$dept]['ratio_spend'][] = $row;
        }

        foreach ($worker as $row) {
            $dept = $row['department_category'];

            if (!isset($result[$dept])) {
                $result[$dept] = [
                    'ratio_spend' => [],
                    'ratio_worker' => []
                ];
            }

            $result[$dept]['ratio_worker'][] = $row;
        }

        return $this->response->setJSON([
            'status' => true,
            'data'   => $result
        ]);
    }

    public function target($id = null)
    {
        if (!$id) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Branch ID required'
            ]);
        }

        $db = \Config\Database::connect();

        $data = $db->table('branches_target')
            ->where('branch_id', $id)
            ->get()
            ->getRowArray();

        if (!$data) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        // 🔥 HITUNG NOMINAL
        $target = (float)$data['target'];

        return $this->response->setJSON([
            'status' => true,
            'data' => [
                'target' => $target,
                'room'   => $target * ($data['room_revenue'] / 100),
                'fb'     => $target * ($data['fb_revenue'] / 100),
                'tax'    => $target * ($data['tax_service'] / 100),
                'margin' => $target * ($data['total_margin'] / 100),
            ]
        ]);
    }

    public function storeSpend()
    {
        $data = $this->request->getPost();

        if (empty($data['department_category']) || empty($data['max_value'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Field wajib belum lengkap'
            ]);
        }

        \Config\Database::connect()
            ->table('ratio_spend')
            ->insert([
                'hotel_id' => $data['hotel_id'],
                'department_category' => $data['department_category'],
                'min_value' => $data['min_value'] ?? 0,
                'max_value' => $data['max_value'],
                'label' => $data['label'] ?? 'OVER',
                'sort_order' => $data['sort_order'] ?? 1
            ]);

        return $this->response->setJSON([
            'status' => true
        ]);
    }

    public function storeWorker()
    {
        $data = $this->request->getPost();

        if (empty($data['department_category']) || empty($data['max_value'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Field wajib belum lengkap'
            ]);
        }

        \Config\Database::connect()
            ->table('ratio_worker')
            ->insert([
                'hotel_id' => $data['hotel_id'],
                'department_category' => $data['department_category'],
                'min_value' => $data['min_value'] ?? 0,
                'max_value' => $data['max_value'],
                'label' => $data['label'],
                'sort_order' => $data['sort_order'] ?? 1
            ]);

        return $this->response->setJSON([
            'status' => true
        ]);
    }
}
