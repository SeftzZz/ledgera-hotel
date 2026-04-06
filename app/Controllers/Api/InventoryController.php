<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;

class InventoryController extends BaseController
{
    // =========================
    // GET ALL
    // =========================
    public function index()
    {
        $db = \Config\Database::connect();

        $data = $db->table('form_pengajuan')
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'status' => true,
            'data'   => $data
        ]);
    }

    // =========================
    // GET DETAIL
    // =========================
    public function show($id)
    {
        $db = \Config\Database::connect();

        $header = $db->table('form_pengajuan')
            ->where('id', $id)
            ->get()
            ->getRowArray();

        $items = $db->table('form_pengajuan_detail d')
            ->select('
              d.*, 
              vi.sparepart, 
              v.name as vendor_name,
              v.id as vendor_id,
              v.kode as vendor_kode
            ')
            ->join('vendor_items vi', 'vi.id = d.vendor_item_id', 'left')
            ->join('vendors v', 'v.id = vi.vendor_id', 'left')
            ->where('d.pengajuan_id', $id)
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'status' => true,
            'data' => [
                'header' => $header,
                'items'  => $items
            ]
        ]);
    }

    // =========================
    // STORE
    // =========================
    public function store()
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $data = $this->request->getJSON(true);

        // ======================
        // VALIDASI BASIC
        // ======================
        if (empty($data['items'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Item tidak boleh kosong'
            ]);
        }
        
        // ======================
        // INSERT HEADER
        // ======================
        $tanggal = $data['tanggal'] ?? date('Y-m-d');

        // convert ke dd-mm-yyyy
        $tanggalFormatted = date('d-m-Y', strtotime($tanggal));

        $db->table('form_pengajuan')->insert([
            'nama'     => $data['nama'] ?? '',
            'divisi'   => $data['divisi'] ?? '',
            'jabatan'  => $data['jabatan'] ?? '',
            'tanggal'  => $tanggalFormatted,
            'status'   => 'Pengajuan'
        ]);

        $pengajuanId = $db->insertID();

        // ======================
        // INSERT DETAIL (AMBIL DARI vendor_items)
        // ======================
        foreach ($data['items'] as $item) {

            if (empty($item['vendor_item_id']) || empty($item['qty'])) continue;

            // 🔥 ambil data dari vendor_items
            $vendorItem = $db->table('vendor_items')
                ->where('id', $item['vendor_item_id'])
                ->get()
                ->getRowArray();

            if (!$vendorItem) continue;

            $db->table('form_pengajuan_detail')->insert([
                'pengajuan_id'   => $pengajuanId,
                'vendor_item_id' => $vendorItem['id'],
                'sparepart'      => $vendorItem['sparepart'],
                'kondisi'        => '-', // default
                'qty'            => $item['qty'],
                'harga'          => $vendorItem['harga'],
                'is_bon'         => 0,
                'is_delete'      => 0
            ]);
        }

        $db->transComplete();

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Pengajuan berhasil disimpan',
            'id'      => $pengajuanId
        ]);
    }

    public function stats()
    {
        $db = \Config\Database::connect();

        $pengajuan = $db->table('form_pengajuan')->where('status', 'Pengajuan')->countAllResults();
        $proses    = $db->table('form_pengajuan')->where('status', 'Proses')->countAllResults();
        $selesai   = $db->table('form_pengajuan')->where('status', 'Selesai')->countAllResults();
        $today     = $db->table('form_pengajuan')->where('DATE(created_at)', date('Y-m-d'))->countAllResults();
        $total     = $db->table('form_pengajuan')->countAllResults();

        return $this->response->setJSON([
            'status' => true,
            'data' => [
                'pengajuan' => $pengajuan,
                'proses'    => $proses,
                'selesai'   => $selesai,
                'total'     => $total,
                'today'     => $today
            ]
        ]);
    }
}