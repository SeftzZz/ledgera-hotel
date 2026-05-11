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
            'company_id'    => $data['company_id'],
            'branch_id'     => $data['branch_id'],
            'nama'          => $data['nama'],
            'divisi'        => $data['divisi'],
            'jabatan'       => $data['jabatan'],
            'tanggal'       => $tanggalFormatted,
            'status'        => 'Pengajuan'
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

        $companyId = (int) session('company_id');
        $branchId  = (int) session('branch_id');
        $isSuper   = session('is_super_admin');

        // ======================
        // WHERE
        // ======================
        $where = " company_id = {$companyId} ";

        if (!$isSuper && !empty($branchId)) {
            $where .= " AND branch_id = {$branchId} ";
        }

        // ======================
        // TOTAL
        // ======================
        $total = $db->query("
            SELECT COUNT(*) as total
            FROM form_pengajuan
            WHERE {$where}
        ")->getRow()->total ?? 0;

        // ======================
        // PENGAJUAN
        // ======================
        $pending = $db->query("
            SELECT COUNT(*) as total
            FROM form_pengajuan
            WHERE {$where}
              AND status = 'Pengajuan'
        ")->getRow()->total ?? 0;

        // ======================
        // PROSES
        // ======================
        $proses = $db->query("
            SELECT COUNT(*) as total
            FROM form_pengajuan
            WHERE {$where}
              AND status = 'Proses'
        ")->getRow()->total ?? 0;

        // ======================
        // SELESAI
        // ======================
        $selesai = $db->query("
            SELECT COUNT(*) as total
            FROM form_pengajuan
            WHERE {$where}
              AND status = 'Selesai'
        ")->getRow()->total ?? 0;

        // ======================
        // TODAY
        // ======================
        $today = $db->query("
            SELECT COUNT(*) as total
            FROM form_pengajuan
            WHERE {$where}
              AND DATE(created_at) = CURDATE()
        ")->getRow()->total ?? 0;

        return $this->response->setJSON([
            'status' => true,
            'data' => [
                'pengajuan' => (int) $pending,
                'proses'    => (int) $proses,
                'selesai'   => (int) $selesai,
                'total'     => (int) $total,
                'today'     => (int) $today
            ]
        ]);
    }

    public function inventoryList()
    {
        $db = \Config\Database::connect();

        $builder = $db->table('inventori i')
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

            ->where('i.is_delete', 0);

        // =========================
        // FILTER COMPANY
        // =========================
        if (!empty(session('company_id'))) {

            $builder->where(
                'v.company_id',
                (int) session('company_id')
            );
        }

        $data = $builder
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

        $companyId = (int) session('company_id');

        // =========================
        // TOTAL ITEM
        // =========================
        $totalItems = $db->query("
            SELECT COUNT(DISTINCT i.vendor_item_id) as total
            FROM inventori i
            LEFT JOIN vendors v
                ON v.id = i.vendor_id
            WHERE i.is_delete = 0
              AND v.company_id = {$companyId}
        ")->getRow()->total ?? 0;

        // =========================
        // STOK HABIS
        // =========================
        $stokHabis = $db->query("
            SELECT COUNT(*) as total FROM (
                SELECT
                    i.vendor_item_id,
                    SUM(i.qty) - SUM(i.is_used) as sisa
                FROM inventori i
                LEFT JOIN vendors v
                    ON v.id = i.vendor_id
                WHERE i.is_delete = 0
                  AND v.company_id = {$companyId}
                GROUP BY i.vendor_item_id
                HAVING sisa <= 0
            ) x
        ")->getRow()->total ?? 0;

        // =========================
        // STOK RENDAH
        // =========================
        $stokLow = $db->query("
            SELECT COUNT(*) as total FROM (
                SELECT
                    i.vendor_item_id,
                    SUM(i.qty) - SUM(i.is_used) as sisa
                FROM inventori i
                LEFT JOIN vendors v
                    ON v.id = i.vendor_id
                WHERE i.is_delete = 0
                  AND v.company_id = {$companyId}
                GROUP BY i.vendor_item_id
                HAVING sisa > 0 AND sisa <= 10
            ) x
        ")->getRow()->total ?? 0;

        // =========================
        // STOK TERSEDIA
        // =========================
        $available = $db->query("
            SELECT COUNT(*) as total FROM (
                SELECT
                    i.vendor_item_id,
                    SUM(i.qty) - SUM(i.is_used) as sisa
                FROM inventori i
                LEFT JOIN vendors v
                    ON v.id = i.vendor_id
                WHERE i.is_delete = 0
                  AND v.company_id = {$companyId}
                GROUP BY i.vendor_item_id
                HAVING sisa > 0
            ) x
        ")->getRow()->total ?? 0;

        // =========================
        // MASUK HARI INI
        // =========================
        $today = $db->query("
            SELECT COUNT(*) as total
            FROM inventori i
            LEFT JOIN vendors v
                ON v.id = i.vendor_id
            WHERE i.is_delete = 0
              AND v.company_id = {$companyId}
              AND DATE(i.created_at) = CURDATE()
        ")->getRow()->total ?? 0;

        // =========================
        // RESPONSE
        // =========================
        return $this->response->setJSON([
            'status' => true,
            'data' => [
                'total_items' => (int) $totalItems,
                'available'   => (int) $available,
                'stok_habis'  => (int) $stokHabis,
                'stok_low'    => (int) $stokLow,
                'today'       => (int) $today
            ]
        ]);
    }
}