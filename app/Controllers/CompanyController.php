<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CompanyModel;
use CodeIgniter\HTTP\ResponseInterface;

class CompanyController extends BaseController
{
    protected CompanyModel $model;

    public function __construct()
    {
        $this->model = new CompanyModel();
    }

    public function index()
    {
        return view('master_data/company/index', [
            'title' => 'Company'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | POST /api/companies/datatable
    |--------------------------------------------------------------------------
    */
    public function datatable(): ResponseInterface
    {
        $request = service('request');

        $searchValue = $request->getPost('search')['value'] ?? null;
        $length      = (int) $request->getPost('length');
        $start       = (int) $request->getPost('start');
        $draw        = (int) $request->getPost('draw');
        $order       = $request->getPost('order');

        $orderColumns = [
            null,
            'company_name',
            'created_at',
            null
        ];

        $builder = $this->model
            ->where('deleted_at', null);

        // TOTAL
        $recordsTotal = (clone $builder)->countAllResults(false);

        // SEARCH
        if ($searchValue) {
            $builder->groupStart()
                ->like('company_name', $searchValue)
            ->groupEnd();
        }

        $recordsFiltered = (clone $builder)->countAllResults(false);

        // ORDER
        if ($order) {
            $idx = (int) $order[0]['column'];
            if (!empty($orderColumns[$idx])) {
                $builder->orderBy($orderColumns[$idx], $order[0]['dir']);
            }
        } else {
            $builder->orderBy('id', 'DESC');
        }

        $data = $builder
            ->limit($length, $start)
            ->get()
            ->getResultArray();

        // FORMAT RESULT
        $result = [];
        $no = $start + 1;

        foreach ($data as $row) {

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
                'no'            => $no++.'.',
                'company_code'  => esc($row['company_code']),
                'company_name'  => esc($row['company_name']),
                'company_addr'  => esc($row['company_addr']),
                'created_at'    => date('d-m-Y', strtotime($row['created_at'] ?? 'now')),
                'action'        => $action
            ];
        }

        return $this->response->setJSON([
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $result
        ]);
    }

    public function store(): ResponseInterface
    {
        try {

            $request = service('request');

            $companyCode = trim($request->getPost('company_code'));
            $companyName = trim($request->getPost('company_name'));
            $companyAddr = trim($request->getPost('company_addr'));

            if (empty($companyCode) || empty($companyName)) {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Company code and name are required'
                ]);
            }

            // Cek duplicate company code
            $exists = $this->model
                ->where('company_code', $companyCode)
                ->where('deleted_at', null)
                ->first();

            if ($exists) {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Company code already exists'
                ]);
            }

            $this->model->insert([
                'company_code' => $companyCode,
                'company_name' => $companyName,
                'company_addr' => $companyAddr,
                'is_active'    => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'created_by'   => session('user_id') ?? 0,
                'deleted_at'   => null
            ]);

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Company successfully added'
            ]);

        } catch (\Throwable $e) {

            return $this->response->setJSON([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
