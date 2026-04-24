<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\VendorModel;

class BusinessPartnerController extends BaseController
{
    protected VendorModel $model;

    public function __construct()
    {
        $this->model = new VendorModel();
    }

    public function index()
    {
        return view('master_data/partner/index', [
            'title' => 'Business Partner'
        ]);
    }

    public function datatable()
    {
        $request = service('request');

        $search = $request->getPost('search')['value'] ?? null;
        $length = (int) $request->getPost('length');
        $start  = (int) $request->getPost('start');
        $draw   = (int) $request->getPost('draw');
        $order  = $request->getPost('order');

        $builder = $this->model
            ->where('company_id', session('company_id'))
            ->where('is_delete', 0);

        $recordsTotal = (clone $builder)->countAllResults(false);

        if ($search) {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('kode', $search)
                ->orLike('phone', $search)
            ->groupEnd();
        }

        $recordsFiltered = (clone $builder)->countAllResults(false);

        if ($order) {
            $columns = [null, 'kode', 'name', 'phone', 'status'];
            $idx = $order[0]['column'];

            if (!empty($columns[$idx])) {
                $builder->orderBy($columns[$idx], $order[0]['dir']);
            }
        } else {
            $builder->orderBy('id', 'DESC');
        }

        $data = $builder->limit($length, $start)->get()->getResultArray();

        $result = [];
        $no = $start + 1;

        foreach ($data as $row) {

            $action = '
              <div class="d-flex gap-2">
                <a href="'.base_url('partner/detail/'.$row['id']).'" class="btn btn-sm btn-icon btn-info">
                  <i class="ti ti-eye"></i>
                </a>
                <button class="btn btn-sm btn-icon btn-primary btn-edit" data-id="'.$row['id'].'">
                  <i class="ti ti-pencil"></i>
                </button>
                <button class="btn btn-sm btn-icon btn-danger btn-delete" data-id="'.$row['id'].'">
                  <i class="ti ti-trash"></i>
                </button>
              </div>
            ';

            $result[] = [
                'no'     => $no++.'.',
                'kode'   => esc($row['kode']),
                'name'   => esc($row['name']),
                'phone'  => esc($row['phone']),
                'status' => esc($row['status']),
                'action' => $action
            ];
        }

        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $result
        ]);
    }

    public function store()
    {
        $data = $this->request->getPost();

        $this->model->insert([
            'name'      => $data['name'],
            'kode'      => $data['kode'],
            'no_po'     => $data['no_po'],
            'pic'       => $data['pic'],
            'phone'     => $data['phone'],
            'address'   => $data['address'],
            'status'    => $data['status'] ?? 'Aktif',
            'is_delete' => 0,
            'created_at'=> date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON([
            'status' => true,
            'message'=> 'Business Partner saved successfully'
        ]);
    }

    public function detail($id)
    {
        return view('master_data/partner/items', [
            'title' => 'Business Partner Items',
            'vendor_id' => $id
        ]);
    }
}