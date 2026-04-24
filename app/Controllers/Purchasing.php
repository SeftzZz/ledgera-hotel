<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\DashboardService;

class Purchasing extends BaseController
{
    public function index()
    {
        $service = new DashboardService();

        $data = $service->getDashboardData([
            'company_id'  => session('company_id'),
            'branch_id'   => session('branch_id'),
            'category_id' => session('category_id'),
        ]);

        return view('purchasing/index', $data);
    }

    public function print($id)
    {
        $db = \Config\Database::connect();

        // ================= HEADER PO =================
        $po = $db->table('form_purchasing')
            ->where('pengajuan_id', $id)
            ->get()
            ->getRow();

        // ================= ITEMS =================
        $items = $db->table('form_pengajuan_detail d')
            ->select('
                d.*, 
                v.name as vendor_name,
                vi.satuan,
            ')
            ->join('vendor_items vi', 'vi.id = d.vendor_item_id', 'left')
            ->join('vendors v', 'v.id = vi.vendor_id', 'left')
            ->where('d.pengajuan_id', $id)
            ->get()
            ->getResultArray();

        // ================= VENDOR TABLE =================
        $vendors = $db->table('form_pengajuan_detail d')
            ->select('
                v.name as vendor_name,
                d.no_po,
                d.is_bon
            ')
            ->join('vendor_items vi', 'vi.id = d.vendor_item_id', 'left')
            ->join('vendors v', 'v.id = vi.vendor_id', 'left')
            ->where('d.pengajuan_id', $id)
            ->groupBy('v.id')
            ->get()
            ->getResultArray();

        // ================= TOTAL =================
        $total = 0;
        foreach ($items as $i) {
            $total += $i['qty'] * $i['harga'];
        }

        return view('purchasing/print', [
            'title'   => 'Print',
            'po'      => $po,
            'items'   => $items,
            'vendors' => $vendors,
            'total'   => $total
        ]);
    }
}