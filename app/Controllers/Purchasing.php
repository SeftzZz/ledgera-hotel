<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Purchasing extends BaseController
{
    public function index()
    {
        return view('purchasing/index', [
            'title' => 'Purchasing'
        ]);
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
                v.name as vendor_name
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