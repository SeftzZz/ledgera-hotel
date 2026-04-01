<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Config\Database;

class Maintenance extends BaseController
{
    public function index()
    {
        $db = Database::connect();

        // =========================
        // AMBIL VENDOR ITEMS
        // =========================
        $vendorItems = $db->table('vendor_items vi')
            ->select('
                vi.id,
                vi.sparepart,
                vi.harga,
                vi.vendor_id,
                v.name as vendor_name
            ')
            ->join('vendors v', 'v.id = vi.vendor_id', 'left')
            ->where('vi.is_delete', 0)
            ->where('vi.status', 'Aktif')
            ->get()
            ->getResultArray();

        return view('maintenance/maintenance-add', [
            'title'         => 'Add Maintenance',
            'vendor_items'  => $vendorItems
        ]);
    }
}