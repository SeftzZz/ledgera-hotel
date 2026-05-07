<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class InventoryController extends BaseController
{
    public function index()
    {
        return $this->render('inventory/index', [
            'title' => 'Inventory List',
        ]);
    }

    public function detail($id = null)
    {
        return $this->render('inventory/detail', [
            'title' => 'Detail Pengajuan',
            'id' => $id
        ]);
    }

    public function datatable()
    {
        $db = \Config\Database::connect();

        $builder = $db->table('form_pengajuan');

        // =========================
        // ALWAYS FILTER COMPANY
        // =========================
        $builder->where(
            'company_id',
            (int) session('company_id')
        );

        // =========================
        // FILTER BRANCH
        // =========================
        if (
            !session('is_super_admin') &&
            session('branch_id')
        ) {

            $builder->where(
                'branch_id',
                (int) session('branch_id')
            );
        }

        $data = $builder
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'data' => $data
        ]);
    }

    public function pengajuan()
    {
        return $this->render('inventory/pengajuan', [
            'title' => 'Form Pengajuan',
            'order_number' => 'PGJ-' . date('ymdHis')
        ]);
    }

    public function pengajuan_detail($id = null)
    {
        return $this->render('inventory/detail', [
            'title' => 'Detail Pengajuan',
            'id' => $id
        ]);
    }
}