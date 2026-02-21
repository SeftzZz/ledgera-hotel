<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CoaModel;
use App\Models\CompanyModel;
use App\Models\CoaOpeningBalanceModel;

class CoaController extends BaseController
{
    protected $coa;
    protected $opening;

    public function __construct()
    {
        $this->coaModel = new CoaModel();
        $this->companyModel = new CompanyModel();
        $this->opening = new CoaOpeningBalanceModel();
    }

    public function index()
    {
        $data = [
            'title'  => 'COA',
            'companies' => $this->companyModel
                ->where('deleted_at', null)
                ->orderBy('company_name', 'ASC')
                ->findAll()
        ];

        return view('coa/index', $data);
    }

    // DATATABLE SERVER SIDE
    public function datatable()
    {
        $request = service('request');

        $searchValue = $request->getPost('search')['value'] ?? null;
        $length = (int) $request->getPost('length');
        $start  = (int) $request->getPost('start');
        $draw   = (int) $request->getPost('draw');

        $order = $request->getPost('order');

        $orderColumns = [
            null,
            null,
            'companies.company_name',
            'coa.account_code',
            'coa.account_name',
            'coa.account_type',
            'coa.parent_id',
            'coa.cashflow_type',
            'coa.is_active',
            null
        ];

        $companyId = (int) session()->get('company_id');

        // QUERY FILTERED (COUNT)
        $countQuery = $this->coaModel
            ->select('coa.*, companies.company_name, parent.account_code AS parent_code')
            ->join('companies', 'companies.id = coa.company_id', 'left')
            ->join('coa parent', 'parent.id = coa.parent_id', 'left')
            ->where('coa.deleted_at', null);

        // Company Scope
        if ($companyId !== 0) {
            $countQuery->where('coa.company_id', $companyId);
        }

        if ($searchValue) {
            $countQuery->groupStart()
                ->like('coa.account_code', $searchValue)
                ->orLike('companies.company_name', $searchValue)
                ->orLike('coa.account_name', $searchValue)
                ->orLike('coa.account_type ', $searchValue)
                ->orLike('coa.cashflow_type ', $searchValue)
                ->orLike('coa.is_active', $searchValue)
            ->groupEnd();
        }

        $recordsFiltered = $countQuery->countAllResults();

        // QUERY TOTAL
        $totalQuery = $this->coaModel
            ->select('coa.*, companies.company_name, parent.account_code AS parent_code')
            ->join('companies', 'companies.id = coa.company_id', 'left')
            ->join('coa parent', 'parent.id = coa.parent_id', 'left')
            ->where('coa.deleted_at', null);

        // Company Scope
        if ($companyId !== 0) {
            $totalQuery->where('coa.company_id', $companyId);
        }

        $recordsTotal = $totalQuery->countAllResults();

        // QUERY DATA
        $dataQuery = $this->coaModel
            ->select('coa.*, companies.company_name, parent.account_code AS parent_code')
            ->join('companies', 'companies.id = coa.company_id', 'left')
            ->join('coa parent', 'parent.id = coa.parent_id', 'left')
            ->where('coa.deleted_at', null);

        // Company Scope
        if ($companyId !== 0) {
            $dataQuery->where('coa.company_id', $companyId);
        }

        if ($searchValue) {
            $dataQuery->groupStart()
                ->like('coa.account_code', $searchValue)
                ->orLike('companies.company_name', $searchValue)
                ->orLike('coa.account_name', $searchValue)
                ->orLike('coa.account_type ', $searchValue)
                ->orLike('coa.cashflow_type ', $searchValue)
                ->orLike('coa.is_active', $searchValue)
            ->groupEnd();
        }

        // ORDERING
        if ($order) {
            $idx = (int) $order[0]['column'];
            if (!empty($orderColumns[$idx])) {
                $dataQuery->orderBy($orderColumns[$idx], $order[0]['dir']);
            }
        } else {
            $dataQuery->orderBy('coa.id', 'DESC');
        }

        $data = $dataQuery
            ->limit($length, $start)
            ->get()
            ->getResultArray();

        // FORMAT DATA
        $result = [];
        $no = $start + 1;
        foreach ($data as $row) {
            $status = strtolower($row['is_active']);
            $badgeStatus = match ($status) {
                '1' => '<span class="badge bg-label-success">Active</span>',
                '0' => '<span class="badge bg-label-danger">Inactive</span>',
                default    => '<span class="badge bg-label-secondary">'.ucfirst(esc($status)).'</span>',
            };

            $actionBtn = '<div class="d-flex gap-2">';

            if (hasPermission('coa.edit')) {
                $actionBtn .= '
                    <button class="btn btn-sm btn-icon btn-primary btn-edit" data-id="'.$row['id'].'" title="Edit">
                        <i class="ti ti-pencil"></i>
                    </button>
                ';
            }

            if (hasPermission('coa.delete') && session()->get('user_id') != $row['id']) {
                $actionBtn .= '
                    <button class="btn btn-sm btn-icon btn-danger btn-delete" data-id="'.$row['id'].'" title="Delete">
                        <i class="ti ti-trash"></i>
                    </button>
                ';
            }

            $actionBtn .= '</div>';

            $result[] = [
                'no_urut'       => $no++.'.',
                'kantor_coa'    => esc($row['company_name'] ?? '-'),
                'kode_coa'      => esc($row['account_code']),
                'nama_coa'      => esc($row['account_name']),
                'tipe_coa'      => esc($row['account_type']),
                'induk_coa'     => esc($row['parent_code'] ?? '-'),
                'aruskas_coa'   => esc($row['cashflow_type']),
                'status_coa'    => $badgeStatus,
                'action'        => $actionBtn
            ];
        }

        return $this->response->setJSON([
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $result
        ]);
    }

    public function store()
    {
        $request = service('request');
        $data = [
            'company_id'    => $request->getPost('kantor_coa'),
            'account_code'  => $request->getPost('kode_coa'),
            'account_name'  => $request->getPost('nama_coa'),
            'account_type'  => $request->getPost('tipe_coa'),
            'parent_id'     => $request->getPost('induk_coa'),
            'cashflow_type' => $request->getPost('aruskas_coa'),
            'is_active'     => $request->getPost('status_coa'),
            'created_at'    => date('Y-m-d H:i:s'),
            'created_by'    => session()->get('user_id'),
            'updated_at'    => date('Y-m-d H:i:s'),
            'updated_by'    => session()->get('user_id')
        ];

        $this->coaModel->insert($data);

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Data added successfully'
        ]);
    }

    public function getById()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $id = $this->request->getPost('id');

        $coa = $this->coaModel->find($id);

        if (!$coa) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        return $this->response->setJSON([
            'status' => true,
            'data'   => $coa
        ]);
    }

    public function update()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $id = $this->request->getPost('id');
        $coa = $this->coaModel->find($id);

        if (!$coa) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        $data = [
            'company_id'    => $this->request->getPost('kantor_coa'),
            'account_code'  => $this->request->getPost('kode_coa'),
            'account_name'  => $this->request->getPost('nama_coa'),
            'account_type'  => $this->request->getPost('tipe_coa'),
            'parent_id'     => $this->request->getPost('induk_coa'),
            'cashflow_type' => $this->request->getPost('aruskas_coa'),
            'is_active'     => $this->request->getPost('status_coa'),
            'updated_at'    => date('Y-m-d H:i:s'),
            'updated_by'    => session()->get('user_id')
        ];

        if ($this->coaModel->update($id, $data)) {
            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Data updated successfully'
            ]);
        }

        return $this->response->setJSON([
            'status'  => false,
            'message' => 'Gagal memperbarui data'
        ]);
    }

    public function openingBalance()
    {
        $companyId = session()->get('company_id');

        $coaModel = new \App\Models\CoaModel();

        $accounts = $coaModel
            ->where('company_id', $companyId)
            ->where('is_active', 1)
            ->orderBy('account_code', 'ASC')
            ->findAll();

        return view('accounting/equity/opening_balance', [
            'title'  => 'Opening Balance',
            'accounts' => $accounts
        ]);
    }

    public function saveOpeningBalance()
    {
        $companyId = session()->get('company_id');
        $data = $this->request->getJSON(true);

        $model = new \App\Models\CoaOpeningBalanceModel();

        // delete existing first
        $model->where('company_id', $companyId)->delete();

        foreach ($data as $row) {

            if (empty($row['debit']) && empty($row['credit'])) {
                continue;
            }

            $model->insert([
                'coa_id'     => $row['coa_id'],
                'company_id' => $companyId,
                'debit'      => $row['debit'] ?? 0,
                'credit'     => $row['credit'] ?? 0
            ]);
        }

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Opening Balance Saved'
        ]);
    }



}

