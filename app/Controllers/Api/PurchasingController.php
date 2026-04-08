<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;

class PurchasingController extends BaseController
{
    // =========================
    // LIST PO
    // =========================
    public function index()
    {
        $db = \Config\Database::connect();

        $data = $db->table('form_pengajuan p')
            ->select('
                p.*,
                COUNT(d.id) as total_item,
                COALESCE(SUM(d.qty * d.harga),0) as total_harga
            ')
            ->join('form_pengajuan_detail d', 'd.pengajuan_id = p.id', 'left')
            ->groupBy('p.id')
            ->orderBy('p.id', 'DESC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'data' => $data
        ]);
    }

    // =========================
    // GENERATE PO DARI PENGAJUAN
    // =========================
    public function generateFromPengajuan($pengajuanId)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $details = $db->table('form_pengajuan_detail d')
            ->select('d.*, vi.vendor_id')
            ->join('vendor_items vi', 'vi.id = d.vendor_item_id')
            ->where('d.pengajuan_id', $pengajuanId)
            ->get()
            ->getResultArray();

        $grouped = [];

        foreach ($details as $row) {
            $grouped[$row['vendor_id']][] = $row;
        }

        foreach ($grouped as $vendorId => $items) {

            // ======================
            // GET VENDOR
            // ======================
            $vendor = $db->table('vendors')
                ->where('id', $vendorId)
                ->get()
                ->getRowArray();

            $kodeVendor = $vendor['kode'] ?? 'XX';

            // ======================
            // LAST PO
            // ======================
            $lastPo = $db->table('purchasing')
                ->select('no_po')
                ->where('vendor_id', $vendorId)
                ->orderBy('id', 'DESC')
                ->get()
                ->getRowArray();

            $running = 1;

            if ($lastPo && !empty($lastPo['no_po'])) {
                preg_match('/-(\d+)$/', $lastPo['no_po'], $matches);

                if (isset($matches[1])) {
                    $running = (int)$matches[1] + 1;
                }
            }

            $bulan = date('m');
            $tahun = date('y');

            $noPo = $kodeVendor . '/' . $bulan . $tahun . '-' . str_pad($running, 4, '0', STR_PAD_LEFT);

            // ======================
            // INSERT HEADER
            // ======================
            $db->table('purchasing')->insert([
                'vendor_id' => $vendorId,
                'no_po'     => $noPo,
                'status'    => 'Draft'
            ]);

            $poId = $db->insertID();

            // ======================
            // INSERT ITEMS
            // ======================
            foreach ($items as $item) {
                $db->table('purchasing_items')->insert([
                    'purchasing_id' => $poId,
                    'vendor_item_id'=> $item['vendor_item_id'],
                    'qty'           => $item['qty'],
                    'harga'         => $item['harga']
                ]);
            }
        }

        // update status pengajuan
        $db->table('form_pengajuan')
            ->where('id', $pengajuanId)
            ->update(['status' => 'Proses']);

        $db->transComplete();

        return $this->response->setJSON([
            'status' => true,
            'message'=> 'PO berhasil dibuat'
        ]);
    }

    // =========================
    // STATS PENGAJUAN
    // =========================
    public function stats()
    {
        $db = \Config\Database::connect();

        // ======================
        // TOTAL
        // ======================
        $total = $db->table('form_pengajuan')
            ->countAllResults();

        // ======================
        // PENGAJUAN (PENDING)
        // ======================
        $pending = $db->table('form_pengajuan')
            ->where('status', 'Pengajuan')
            ->countAllResults();

        // ======================
        // PROSES
        // ======================
        $proses = $db->table('form_pengajuan')
            ->where('status', 'Proses')
            ->countAllResults();

        // ======================
        // SELESAI
        // ======================
        $selesai = $db->table('form_pengajuan')
            ->where('status', 'Selesai')
            ->countAllResults();

        // ======================
        // HARI INI
        // ======================
        $today = $db->table('form_pengajuan')
            ->where('DATE(created_at)', date('Y-m-d'))
            ->countAllResults();

        return $this->response->setJSON([
            'status' => true,
            'data' => [
                'pengajuan' => $pending,
                'proses'    => $proses,
                'selesai'   => $selesai,
                'total'     => $total,
                'today'     => $today
            ]
        ]);
    }

    // =========================
    // SAVE PURCHASING
    // =========================
    public function save()
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $data = $this->request->getJSON(true);

        // ======================
        // VALIDASI
        // ======================
        if (empty($data['pengajuan_id'])) {
            return $this->response->setJSON([
                'status' => false,
                'message'=> 'pengajuan_id required'
            ]);
        }

        $service = new \App\Services\TransactionService();
        $coaModel = new \App\Models\CoaModel();

        // =========================
        // 🔥 GET COMPANY FROM BRANCH
        // =========================
        $branch = $db->table('branches')
            ->select('id, company_id')
            ->where('id', session('branch_id'))
            ->get()
            ->getRowArray();

        if (!$branch) {
            return $this->error('Branch tidak ditemukan');
        }

        $companyId = $branch['company_id'];

        // =========================
        // GET CASH ACCOUNT
        // =========================
        $kasAccount = $coaModel
            ->where('company_id', $companyId)
            ->where('account_code', '1101')
            ->where('is_active', 1)
            ->first();

        if (!$kasAccount) {
            return $this->error('Akun Kas (1101) tidak ditemukan');
        }

        $paymentAccountId = $kasAccount['id'];

        // ======================
        // INSERT HEADER PO
        // ======================
        $db->table('form_purchasing')->insert([
            'pengajuan_id' => $data['pengajuan_id'],
            'nama_po'      => $data['nama'] ?? '',
            'divisi_po'    => $data['divisi'] ?? '',
            'jabatan_po'   => $data['jabatan'] ?? '',
            'tanggal_po'   => $data['tanggal'] ?? date('d-m-Y')
        ]);

        // ======================
        // UPDATE DETAIL
        // ======================
        if (!empty($data['items'])) {

            foreach ($data['items'] as $item) {

                $db->table('form_pengajuan_detail')
                    ->where('id', $item['detail_id'])
                    ->update([
                        'vendor_item_id' => $item['vendor_item_id'],
                        'harga'          => $item['harga'],
                        'no_po'          => $item['no_po'] ?? null,
                        'is_bon'         => $item['is_bon'] ?? 0
                    ]);
            }
        }

        // ======================
        // UPDATE STATUS
        // ======================
        $db->table('form_pengajuan')
            ->where('id', $data['pengajuan_id'])
            ->update([
                'status' => 'Proses'
            ]);

        $branchId = $branch['id'];

        $trxId = $service->create([
            'company_id'         => $companyId,
            'branch_id'          => $branchId > 0 ? $branchId : null,
            'trx_date'           => $data['tanggal'],
            'trx_type'           => 'purchase_inventory',
            'reference_no'       => 'PG-'.$data['pengajuan_id'],
            'amount'             => (float) $data['total'],
            'payment_account_id' => $paymentAccountId
        ]);

        $db->transComplete();

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Purchasing berhasil disimpan',
            'data'    => [
                'order_id' => $trxId
            ]
        ]);
    }
}