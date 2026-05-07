<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TaxCodeModel;

class TaxController extends BaseController
{
    protected TaxCodeModel $model;

    public function __construct()
    {
        $this->model = new TaxCodeModel();
    }

    public function index()
    {
        return $this->render('master_data/tax/index', [
            'title' => 'Tax Codes'
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
            ->where('deleted_at', null);

        $recordsTotal = (clone $builder)->countAllResults(false);

        if ($search) {
            $builder->groupStart()
                ->like('tax_code', $search)
                ->orLike('tax_name', $search)
            ->groupEnd();
        }

        $recordsFiltered = (clone $builder)->countAllResults(false);

        if ($order) {
            $columns = [null, 'tax_code', 'tax_name', 'tax_rate'];
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

            $statusBadge = $row['is_active']
                ? '<span class="badge bg-label-success">Active</span>'
                : '<span class="badge bg-label-danger">Inactive</span>';

            $action = '
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-icon btn-primary btn-edit" data-id="'.$row['id'].'">
                        <i class="ti ti-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-icon btn-danger btn-delete" data-id="'.$row['id'].'">
                        <i class="ti ti-trash"></i>
                    </button>
                </div>
            ';

            $result[] = [
                'no'        => $no++.'.',
                'tax_code'  => esc($row['tax_code']),
                'tax_name'  => esc($row['tax_name']),
                'tax_rate'  => number_format($row['tax_rate'], 2).'%',
                'status'    => $statusBadge,
                'action'    => $action
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
            'company_id'=> session('company_id'),
            'tax_code'  => $data['tax_code'],
            'tax_name'  => $data['tax_name'],
            'tax_rate'  => $data['tax_rate'],
            'is_active' => $data['is_active'] ?? 1,
            'created_at'=> date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON([
            'status' => true,
            'message'=> 'Tax Code saved successfully'
        ]);
    }
}
