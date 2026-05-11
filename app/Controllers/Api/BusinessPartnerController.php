<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\BusinessPartnerModel;

class BusinessPartnerController extends BaseController
{
    protected BusinessPartnerModel $model;

    public function __construct()
    {
        $this->model = new BusinessPartnerModel();
    }

    // =========================
    // GET ALL VENDORS
    // =========================
    public function index()
    {
        $data = $this->model
            ->where('is_delete', 0)
            ->findAll();

        return $this->response->setJSON([
            'status' => true,
            'data'   => $data
        ]);
    }

    // =========================
    // CREATE VENDOR
    // =========================
    public function store()
    {
        $data = $this->request->getJSON(true);

        $id = $this->model->insert([
            'company_id'=> session('company_id'),
            'name'      => $data['name'] ?? '',
            'kode'      => $data['kode'] ?? '',
            'no_po'     => $data['no_po'] ?? '',
            'pic'       => $data['pic'] ?? '',
            'phone'     => $data['phone'] ?? '',
            'address'   => $data['address'] ?? '',
            'status'    => $data['status'] ?? 'Aktif',
            'is_delete' => 0
        ], true);

        return $this->response->setJSON([
            'status' => true,
            'id'     => $id
        ]);
    }

    // =========================
    // GET DETAIL VENDOR
    // =========================
    public function show($id = null)
    {
        $data = $this->model
            ->where('id', $id)
            ->where('is_delete', 0)
            ->first();

        if (!$data) {
            return $this->response->setJSON([
                'status' => false,
                'message'=> 'Vendor not found'
            ]);
        }

        return $this->response->setJSON([
            'status' => true,
            'data'   => $data
        ]);
    }

    // =========================
    // UPDATE VENDOR
    // =========================
    public function update($id = null)
    {
        $data = $this->request->getJSON(true);

        $vendor = $this->model->find($id);

        if (!$vendor) {
            return $this->response->setJSON([
                'status' => false,
                'message'=> 'Vendor not found'
            ]);
        }

        $this->model->update($id, [
            'name'    => $data['name'] ?? $vendor['name'],
            'kode'    => $data['kode'] ?? $vendor['kode'],
            'no_po'   => $data['no_po'] ?? $vendor['no_po'],
            'pic'     => $data['pic'] ?? $vendor['pic'],
            'phone'   => $data['phone'] ?? $vendor['phone'],
            'address' => $data['address'] ?? $vendor['address'],
            'status'  => $data['status'] ?? $vendor['status'],
        ]);

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Vendor updated'
        ]);
    }

    // =========================
    // DELETE (SOFT DELETE)
    // =========================
    public function delete($id = null)
    {
        $this->model->update($id, [
            'is_delete' => 1
        ]);

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Vendor deleted'
        ]);
    }

    // =========================
    // GET VENDOR ITEMS
    // =========================
    public function items($vendorId = null)
    {
        $data = \Config\Database::connect()
            ->table('vendor_items vi')

            ->select('
                vi.id,
                vi.vendor_id,
                vi.sparepart,
                vi.type,
                vi.harga,
                vi.no_seri,
                vi.satuan,
                vi.status,
                vi.is_delete,
                vi.created_at,
                vi.updated_at,

                v.company_id,
                v.name,
                v.kode,
                v.no_po,
                v.pic,
                v.phone,
                v.address
            ')

            ->join('vendors v', 'v.id = vi.vendor_id', 'left')

            ->where('v.company_id', session('company_id'))
            ->where('vi.vendor_id', $vendorId)
            ->where('vi.is_delete', 0)

            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'status' => true,
            'data'   => $data
        ]);
    }

    // =========================
    // GET ALL VENDOR ITEMS 🔥
    // =========================
    public function allItems()
    {
        $data = \Config\Database::connect()
            ->table('vendor_items vi')
            ->select('
                vi.*, 
                v.name as vendor_name,
                v.kode as vendor_kode
            ')
            ->join('vendors v', 'v.id = vi.vendor_id', 'left')
            ->where('company_id', session('company_id'))
            ->where('vi.is_delete', 0)
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'status' => true,
            'data'   => $data
        ]);
    }

    // =========================
    // STORE VENDOR ITEM
    // =========================
    public function storeItem()
    {
        $data = $this->request->getJSON(true);

        $id = \Config\Database::connect()
            ->table('vendor_items')
            ->insert([
                'vendor_id' => $data['vendor_id'],
                'sparepart' => $data['sparepart'],
                'type'      => $data['type'],
                'satuan'    => $data['satuan'],
                'harga'     => $data['harga'],
                'no_seri'   => $data['no_seri'],
                'status'    => $data['status'] ?? 'Aktif',
                'is_delete' => 0
            ]);

        return $this->response->setJSON([
            'status' => true
        ]);
    }

    // =========================
    // UPDATE VENDOR ITEM
    // =========================
    public function updateItem($id)
    {
        $data = $this->request->getJSON(true);

        \Config\Database::connect()
            ->table('vendor_items')
            ->where('id', $id)
            ->update([
                'sparepart' => $data['sparepart'],
                'type'      => $data['type'],
                'harga'     => $data['harga'],
                'no_seri'   => $data['no_seri'],
                'status'    => $data['status']
            ]);

        return $this->response->setJSON([
            'status' => true
        ]);
    }

    // =========================
    // DELETE ITEM (SOFT)
    // =========================
    public function deleteItem($id)
    {
        \Config\Database::connect()
            ->table('vendor_items')
            ->where('id', $id)
            ->update(['is_delete' => 1]);

        return $this->response->setJSON([
            'status' => true
        ]);
    }

    // GET ITEM DETAIL
    public function showItem($id)
    {
        $data = \Config\Database::connect()
            ->table('vendor_items')
            ->where('id', $id)
            ->get()
            ->getRowArray();

        return $this->response->setJSON([
            'status' => true,
            'data'   => $data
        ]);
    }
}