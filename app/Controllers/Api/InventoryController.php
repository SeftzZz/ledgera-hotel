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
              vi.satuan,
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
                'purpose'        => $item['purpose'],
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

    public function inventoryList()
    {
        $db = \Config\Database::connect();

        $data = $db->table('inventori i')
            ->select('
                i.vendor_item_id,
                i.sparepart,
                SUM(i.qty) as total_qty,
                SUM(i.is_used) as total_used,
                (SUM(i.qty) - SUM(i.is_used)) as stock_available,
                vi.satuan,
                v.name as vendor_name
            ')
            ->join('vendor_items vi', 'vi.id = i.vendor_item_id', 'left')
            ->join('vendors v', 'v.id = i.vendor_id', 'left')
            ->where('i.is_delete', 0)
            ->groupBy('i.vendor_item_id')
            ->orderBy('i.sparepart', 'ASC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'status' => true,
            'data'   => $data
        ]);
    }

    public function inventoryStats()
    {
        $db = \Config\Database::connect();

        // =========================
        // TOTAL ITEM (distinct barang)
        // =========================
        $totalItems = $db->table('inventori')
            ->select('COUNT(DISTINCT vendor_item_id) as total')
            ->where('is_delete', 0)
            ->get()
            ->getRow()
            ->total ?? 0;

        // =========================
        // STOK HABIS
        // =========================
        $stokHabis = $db->query("
            SELECT COUNT(*) as total FROM (
                SELECT vendor_item_id, SUM(qty) - SUM(is_used) as sisa
                FROM inventori
                WHERE is_delete = 0
                GROUP BY vendor_item_id
                HAVING sisa <= 0
            ) x
        ")->getRow()->total ?? 0;

        // =========================
        // STOK RENDAH (<=10)
        // =========================
        $stokLow = $db->query("
            SELECT COUNT(*) as total FROM (
                SELECT vendor_item_id, SUM(qty) - SUM(is_used) as sisa
                FROM inventori
                WHERE is_delete = 0
                GROUP BY vendor_item_id
                HAVING sisa > 0 AND sisa <= 10
            ) x
        ")->getRow()->total ?? 0;

        // =========================
        // STOK TERSEDIA (>0)
        // =========================
        $available = $db->query("
            SELECT COUNT(*) as total FROM (
                SELECT vendor_item_id, SUM(qty) - SUM(is_used) as sisa
                FROM inventori
                WHERE is_delete = 0
                GROUP BY vendor_item_id
                HAVING sisa > 0
            ) x
        ")->getRow()->total ?? 0;

        // =========================
        // MASUK HARI INI
        // =========================
        $today = $db->table('inventori')
            ->where('is_delete', 0)
            ->where('DATE(created_at)', date('Y-m-d'))
            ->countAllResults();

        // =========================
        // RESPONSE
        // =========================
        return $this->response->setJSON([
            'status' => true,
            'data' => [
                'total_items' => (int)$totalItems,
                'available'   => (int)$available,
                'stok_habis'  => (int)$stokHabis,
                'stok_low'    => (int)$stokLow,
                'today'       => (int)$today
            ]
        ]);
    }
}