<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BranchModel;
use CodeIgniter\HTTP\ResponseInterface;

class BranchController extends BaseController
{
    protected BranchModel $model;

    public function __construct()
    {
        $this->model = new BranchModel();
    }

    public function index()
    {
        $companyModel = new \App\Models\CompanyModel();

        return $this->render('master_data/branch/index', [
            'title'     => 'Branch',
            'companies' => $companyModel
                            ->where('deleted_at', null)
                            ->orderBy('company_name', 'ASC')
                            ->findAll()
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | POST /branch/datatable
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
            null, // no
            'companies.company_name',
            'branches.branch_code',
            'branches.branch_name',
            null
        ];

        $builder = $this->model
            ->select('
                branches.*, 
                companies.company_name,
                GROUP_CONCAT(CONCAT(branches_target.id, ":", branches_target.target)) as targets
            ')
            ->join('companies', 'companies.id = branches.company_id', 'left')
            ->join('branches_target', 'branches_target.branch_id = branches.id', 'left')
            ->where('companies.id', session('company_id'))
            ->groupBy('branches.id');

        // TOTAL
        $recordsTotal = (clone $builder)->countAllResults(false);

        // SEARCH
        if ($searchValue) {
            $builder->groupStart()
                ->like('branches.branch_code', $searchValue)
                ->orLike('branches.branch_name', $searchValue)
                ->orLike('companies.company_name', $searchValue)
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
            $builder->orderBy('branches.id', 'DESC');
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
                    <button class="btn btn-sm btn-icon btn-primary edit-record"
                        data-id="'.$row['id'].'"
                        data-company_id="'.$row['company_id'].'"
                        data-branch_code="'.esc($row['branch_code']).'"
                        data-branch_name="'.esc($row['branch_name']).'">
                        <i class="ti ti-pencil"></i>
                    </button>

                    <button class="btn btn-sm btn-icon btn-danger btn-delete"
                        data-id="'.$row['id'].'">
                        <i class="ti ti-trash"></i>
                    </button>
                </div>
            ';

            $result[] = [
                'no'           => $no++.'.',
                'company_name' => esc($row['company_name'] ?? '-'),
                'branch_code'  => esc($row['branch_code']),
                'branch_name'  => esc($row['branch_name']),
                'branch_id'    => esc($row['id']),
                'targets'      => $row['targets'],
                'action'       => $action
            ];
        }

        return $this->response->setJSON([
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $result
        ]);
    }

    public function ratio($branch_id, $id)
    {
        return $this->render('master_data/branch/ratio', [
            'title'     => 'Branch Ratio',
            'branch_id' => $branch_id,
            'target_id' => $id,
        ]);
    }
}
