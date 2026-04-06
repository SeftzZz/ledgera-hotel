<?php

namespace App\Models;

use CodeIgniter\Model;

class PengajuanModel extends Model
{
    protected $table            = 'form_pengajuan';
    protected $primaryKey       = 'id';

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    // =========================
    // ALLOWED FIELDS
    // =========================
    protected $allowedFields = [
        'order_id',
        'nama',
        'divisi',
        'jabatan',
        'tanggal',
        'status',
        'created_at',
        'updated_at'
    ];

    // =========================
    // TIMESTAMP
    // =========================
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // =========================
    // VALIDATION
    // =========================
    protected $validationRules = [
        'nama'    => 'required|min_length[2]',
        'divisi'  => 'required',
        'jabatan' => 'required',
        'tanggal' => 'required',
        'status'  => 'in_list[Pengajuan,Proses,Selesai]'
    ];

    protected $validationMessages = [
        'nama' => [
            'required' => 'Nama wajib diisi'
        ],
        'divisi' => [
            'required' => 'Divisi wajib diisi'
        ],
        'jabatan' => [
            'required' => 'Jabatan wajib diisi'
        ]
    ];

    // =========================
    // DEFAULT STATUS
    // =========================
    protected $beforeInsert = ['setDefaultStatus'];

    protected function setDefaultStatus(array $data)
    {
        if (!isset($data['data']['status'])) {
            $data['data']['status'] = 'Pengajuan';
        }
        return $data;
    }

    // =========================
    // HELPER FUNCTION
    // =========================

    public function getWithOrder($id)
    {
        return $this->select('form_pengajuan.*, orders.order_number, orders.status as order_status')
            ->join('orders', 'orders.id = form_pengajuan.order_id', 'left')
            ->where('form_pengajuan.id', $id)
            ->first();
    }

    public function getAllWithOrder()
    {
        return $this->select('form_pengajuan.*, orders.order_number')
            ->join('orders', 'orders.id = form_pengajuan.order_id', 'left')
            ->orderBy('form_pengajuan.id', 'DESC')
            ->findAll();
    }
}