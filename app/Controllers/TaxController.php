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
        $coa = model('App\Models\CoaModel')
            ->where('company_id', session('company_id'))
            ->where('is_active', 1)
            ->orderBy('account_code', 'ASC')
            ->findAll();

        return $this->render('master_data/tax/index', [
            'title' => 'Tax Codes',
            'coa'   => $coa
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
            ->select('
                tax_codes.*,

                coa.account_code,
                coa.account_name
            ')
            ->join(
                'coa',
                'coa.id = tax_codes.coa_account_id',
                'left'
            )
            ->where(
                'tax_codes.company_id',
                session('company_id')
            )
            ->where(
                'tax_codes.deleted_at',
                null
            );

        // =========================
        // TOTAL
        // =========================
        $recordsTotal =
            (clone $builder)->countAllResults(false);

        // =========================
        // SEARCH
        // =========================
        if ($search) {

            $builder->groupStart()

                ->like(
                    'tax_codes.tax_code',
                    $search
                )

                ->orLike(
                    'tax_codes.tax_name',
                    $search
                )

                ->orLike(
                    'tax_codes.tax_type',
                    $search
                )

                ->orLike(
                    'coa.account_code',
                    $search
                )

            ->groupEnd();
        }

        $recordsFiltered =
            (clone $builder)->countAllResults(false);

        // =========================
        // ORDER
        // =========================
        if ($order) {

            $columns = [

                null,
                'tax_codes.tax_code',
                'tax_codes.tax_name',
                'tax_codes.tax_type',
                'tax_codes.tax_rate',
                'tax_codes.tax_direction',
                'tax_codes.is_active'

            ];

            $idx = $order[0]['column'];

            if (!empty($columns[$idx])) {

                $builder->orderBy(
                    $columns[$idx],
                    $order[0]['dir']
                );
            }

        } else {

            $builder->orderBy(
                'tax_codes.id',
                'DESC'
            );
        }

        // =========================
        // DATA
        // =========================
        $data = $builder
            ->limit($length, $start)
            ->get()
            ->getResultArray();

        $result = [];

        $no = $start + 1;

        foreach ($data as $row) {

            // =====================
            // STATUS
            // =====================
            $statusBadge =
                $row['is_active']

                ? '<span class="badge bg-label-success">Active</span>'

                : '<span class="badge bg-label-danger">Inactive</span>';

            // =====================
            // TAX TYPE
            // =====================
            $taxType =
                '<span class="badge bg-label-primary">' .
                strtoupper($row['tax_type']) .
                '</span>';

            // =====================
            // DIRECTION
            // =====================
            $direction =
                '<span class="badge bg-label-info">' .
                ucfirst($row['tax_direction']) .
                '</span>';

            // =====================
            // INCLUDED
            // =====================
            $included =
                $row['is_included']

                ? '<span class="badge bg-label-warning">Included</span>'

                : '<span class="badge bg-label-secondary">Excluded</span>';

            // =====================
            // CREDITABLE
            // =====================
            $creditable =
                $row['is_creditable']

                ? '<span class="badge bg-label-success">Yes</span>'

                : '<span class="badge bg-label-danger">No</span>';

            // =====================
            // COA
            // =====================
            $coa = '-';

            if (!empty($row['account_code'])) {

                $coa =
                    esc($row['account_code']) .
                    ' - ' .
                    esc($row['account_name']);
            }

            // =====================
            // ACTION
            // =====================
            $action = '

                <div class="d-flex gap-2">

                    <button
                        class="btn btn-sm btn-icon btn-primary btn-edit"
                        data-id="'.$row['id'].'">

                        <i class="ti ti-pencil"></i>

                    </button>

                    <button
                        class="btn btn-sm btn-icon btn-danger btn-delete"
                        data-id="'.$row['id'].'">

                        <i class="ti ti-trash"></i>

                    </button>

                </div>
            ';

            $result[] = [
                'no'              => $no++.'.',
                'tax_code'        => esc($row['tax_code']),
                'tax_name'        => esc($row['tax_name']),
                'tax_type'        => $taxType,
                'tax_rate'        => number_format(
                    $row['tax_rate'],
                    2
                ).'%',
                'tax_direction'   => $direction,
                'coa_account'     => $coa,
                'is_included'     => $included,
                'is_creditable'   => $creditable,
                'status'          => $statusBadge,
                'action'          => $action
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
        $data = $this->request->getPost();

        $this->model->insert([
            'company_id'     => $data['company_id'],
            'tax_code'       => $data['tax_code'],
            'tax_name'       => $data['tax_name'],
            'tax_type'       => $data['tax_type'],
            'tax_rate'       => $data['tax_rate'],
            'tax_direction'  => $data['tax_direction'] ?? 'both',
            'coa_account_id' => !empty($data['coa_account_id'])
                ? $data['coa_account_id']
                : null,
            'is_included'    => $data['is_included'] ?? 0,
            'is_creditable'  => $data['is_creditable'] ?? 1,
            'is_active'      => $data['is_active'] ?? 1
        ]);

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Tax Code saved successfully'
        ]);
    }
}
