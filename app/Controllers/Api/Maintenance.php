<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Config\Database;

class Maintenance extends BaseController
{
    public function create()
    {
        $db = Database::connect();
        $db->transStart();

        try {

            // =========================
            // VALIDASI INPUT
            // =========================
            $data = $this->request->getPost();

            if (empty($data['tgl_order']) || empty($data['vehicle_id'])) {
                throw new \Exception('Data header tidak lengkap');
            }

            if (empty($data['items'])) {
                throw new \Exception('Detail item kosong');
            }

            // =========================
            // INSERT HEADER
            // =========================
            $header = [
                'tgl_order'     => $data['tgl_order'],
                'tgl_selesai'   => $data['tgl_selesai'] ?? $data['tgl_order'],
                'jam_order'     => $data['jam_order'] ?? date('H:i'),
                'jam_selesai'   => $data['jam_selesai'] ?? date('H:i'),
                'type'          => $data['type'] ?? '',
                'requester'     => $data['requester'] ?? '',
                'vehicle_id'    => $data['vehicle_id'],
                'no_pintu'      => $data['no_pintu'] ?? '',
                'staff_gudang'  => $data['staff_gudang'] ?? '',
                'security'      => $data['security'] ?? '',
                'driver_id'     => $data['driver_id'] ?? 0,
            ];

            $db->table('maintenances')->insert($header);
            $maintenanceId = $db->insertID();

            // =========================
            // LOOP DETAIL
            // =========================
            foreach ($data['items'] as $item) {

                if (empty($item['inventori_id']) || empty($item['qty'])) {
                    continue;
                }

                $qty = (int) $item['qty'];

                // =========================
                // CEK STOK INVENTORI
                // =========================
                $inventori = $db->table('inventori')
                    ->where('id', $item['inventori_id'])
                    ->get()
                    ->getRowArray();

                if (!$inventori) {
                    throw new \Exception('Inventori tidak ditemukan');
                }

                $stokSisa = $inventori['qty'] - $inventori['is_used'];

                if ($qty > $stokSisa) {
                    throw new \Exception('Stok tidak cukup untuk item: ' . $inventori['sparepart']);
                }

                // =========================
                // INSERT DETAIL
                // =========================
                $detail = [
                    'maintenance_id'        => $maintenanceId,
                    'inventori_id'         => $item['inventori_id'], // 🔥 penting
                    'permintaan_perbaikan' => $item['permintaan_perbaikan'] ?? '',
                    'sparepart'            => $inventori['sparepart'],
                    'qty'                  => $qty,
                    'kondisi'              => $item['kondisi'] ?? '',
                    'posisi'               => $item['posisi'] ?? '',
                    'keterangan'           => $item['keterangan'] ?? '',
                    'no_seri'              => $item['no_seri'] ?? '',
                ];

                $db->table('maintenance_orders')->insert($detail);

                // =========================
                // UPDATE STOK (🔥 CORE LOGIC)
                // =========================
                $db->table('inventori')
                    ->where('id', $item['inventori_id'])
                    ->update([
                        'is_used' => $inventori['is_used'] + $qty
                    ]);
            }

            $db->transComplete();

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Maintenance berhasil disimpan'
            ]);

        } catch (\Throwable $e) {

            $db->transRollback();

            return $this->response->setJSON([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}