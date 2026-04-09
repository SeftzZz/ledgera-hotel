<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class InventoryController extends BaseController
{
    public function index()
    {
        return view('inventory/index', [
            'title' => 'Inventory List',
        ]);
    }

    public function detail($id = null)
    {
        return view('inventory/detail', [
            'title' => 'Detail Pengajuan',
            'id' => $id
        ]);
    }

    public function datatable()
    {
        $db = \Config\Database::connect();

        $data = $db->table('form_pengajuan')
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'data' => $data
        ]);
    }

    public function pengajuan()
    {
        return view('inventory/pengajuan', [
            'title' => 'Form Pengajuan',
            'order_number' => 'PGJ-' . date('ymdHis')
        ]);
    }

    public function pengajuan_detail($id = null)
    {
        return view('inventory/detail', [
            'title' => 'Detail Pengajuan',
            'id' => $id
        ]);
    }
}