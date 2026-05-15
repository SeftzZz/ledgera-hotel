<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\CompanyModel;
use App\Models\BranchModel;
use App\Models\CategoryModel;

class UserController extends BaseController
{
    protected $user;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->companyModel = new CompanyModel();
        $this->branchModel = new BranchModel();
        $this->categoryModel = new CategoryModel();
    }

    public function index()
    {
        $companyId = session()->get('company_id');
        $branchId  = session()->get('branch_id');

        $isSuperAdmin = $companyId == 0;

        // =========================================
        // COMPANIES
        // =========================================
        $companyQuery = $this->companyModel
            ->where('deleted_at', null);

        if (!$isSuperAdmin) {

            $companyQuery->where(
                'id',
                $companyId
            );
        }

        // =========================================
        // BRANCHES
        // =========================================
        $branchQuery = $this->branchModel;

        if (!$isSuperAdmin) {

            $branchQuery->where(
                'company_id',
                $companyId
            );

            if ($branchId) {

                $branchQuery->where(
                    'id',
                    $branchId
                );
            }
        }

        $data = [
            'title' => 'Users',

            'companies' => $companyQuery
                ->orderBy('company_name', 'ASC')
                ->findAll(),

            'branches' => $branchQuery
                ->orderBy('branch_name', 'ASC')
                ->findAll()
        ];

        return view('users/index', $data);
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
            'users.name',
            'companies.company_name',
            'branches.branch_name',
            'users.email',
            'users.phone',
            'users.is_active',
            null
        ];

        $companyId = (int) session()->get('company_id');
        $branchId  = (int) session()->get('branch_id');

        // =========================================
        // QUERY FILTERED COUNT
        // =========================================
        $countQuery = $this->userModel
            ->join(
                'companies',
                'companies.id = users.company_id',
                'left'
            )
            ->join(
                'branches',
                'branches.id = users.branch_id',
                'left'
            )
            ->where('users.deleted_at', null);

        // COMPANY SCOPE
        if ($companyId !== 0) {
            $countQuery->where(
                'users.company_id',
                $companyId
            );
        }

        // BRANCH SCOPE
        if ($branchId !== 0) {
            $countQuery->where(
                'users.branch_id',
                $branchId
            );
        }

        if ($searchValue) {

            $countQuery->groupStart()
                ->like('users.name', $searchValue)
                ->orLike('companies.company_name', $searchValue)
                ->orLike('branches.branch_name', $searchValue)
                ->orLike('users.email', $searchValue)
                ->orLike('users.phone', $searchValue)
                ->orLike('users.is_active', $searchValue)
            ->groupEnd();
        }

        $recordsFiltered = $countQuery->countAllResults();

        // =========================================
        // QUERY TOTAL
        // =========================================
        $totalQuery = $this->userModel
            ->join(
                'companies',
                'companies.id = users.company_id',
                'left'
            )
            ->join(
                'branches',
                'branches.id = users.branch_id',
                'left'
            )
            ->where('users.deleted_at', null);

        // COMPANY SCOPE
        if ($companyId !== 0) {
            $totalQuery->where(
                'users.company_id',
                $companyId
            );
        }

        // BRANCH SCOPE
        if ($branchId !== 0) {
            $totalQuery->where(
                'users.branch_id',
                $branchId
            );
        }

        $recordsTotal = $totalQuery->countAllResults();

        // =========================================
        // QUERY DATA
        // =========================================
        $dataQuery = $this->userModel
            ->select('
                users.*,
                companies.company_name,
                branches.branch_name
            ')
            ->join(
                'companies',
                'companies.id = users.company_id',
                'left'
            )
            ->join(
                'branches',
                'branches.id = users.branch_id',
                'left'
            )
            ->where('users.deleted_at', null);

        // COMPANY SCOPE
        if ($companyId !== 0) {
            $dataQuery->where(
                'users.company_id',
                $companyId
            );
        }

        // BRANCH SCOPE
        if ($branchId !== 0) {
            $dataQuery->where(
                'users.branch_id',
                $branchId
            );
        }

        if ($searchValue) {

            $dataQuery->groupStart()
                ->like('users.name', $searchValue)
                ->orLike('companies.company_name', $searchValue)
                ->orLike('branches.branch_name', $searchValue)
                ->orLike('users.email', $searchValue)
                ->orLike('users.phone', $searchValue)
                ->orLike('users.is_active', $searchValue)
            ->groupEnd();
        }

        // =========================================
        // ORDERING
        // =========================================
        if ($order) {

            $idx = (int) $order[0]['column'];

            if (!empty($orderColumns[$idx])) {

                $dataQuery->orderBy(
                    $orderColumns[$idx],
                    $order[0]['dir']
                );
            }

        } else {

            $dataQuery->orderBy(
                'users.id',
                'DESC'
            );
        }

        $data = $dataQuery
            ->limit($length, $start)
            ->get()
            ->getResultArray();

        // =========================================
        // FORMAT DATA
        // =========================================
        $result = [];

        $no = $start + 1;

        foreach ($data as $row) {

            $status = strtolower(
                $row['is_active']
            );

            $badgeStatus = match ($status) {

                'active' =>
                    '<span class="badge bg-label-success">Active</span>',

                'inactive' =>
                    '<span class="badge bg-label-danger">Inactive</span>',

                default =>
                    '<span class="badge bg-label-secondary">'
                    . ucfirst(esc($status)) .
                    '</span>',
            };

            $actionBtn = '<div class="d-flex gap-2">';

            if (hasPermission('users.edit')) {

                $actionBtn .= '
                    <button
                        class="btn btn-sm btn-icon btn-primary btn-edit"
                        data-id="'.$row['id'].'"
                        title="Edit">
                        <i class="ti ti-pencil"></i>
                    </button>
                ';
            }

            if (
                hasPermission('users.delete') &&
                session()->get('user_id') != $row['id']
            ) {

                $actionBtn .= '
                    <button
                        class="btn btn-sm btn-icon btn-danger btn-delete"
                        data-id="'.$row['id'].'"
                        title="Delete">
                        <i class="ti ti-trash"></i>
                    </button>
                ';
            }

            $actionBtn .= '</div>';

            $result[] = [
                'no_urut'       => $no++.'.',
                'name_user'     => esc($row['name']),
                'company_user'  => esc($row['company_name'] ?? '-'),
                'branch_user'   => esc($row['branch_name'] ?? '-'),
                'email_user'    => esc($row['email']),
                'hp_user'       => '+62' . esc($row['phone']),
                'status_user'   => $badgeStatus,
                'photo_user'    => esc($row['photo']),
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
            'name'       => $request->getPost('name_user'),
            'email'      => $request->getPost('email_user'),
            'phone'      => $request->getPost('hp_user'),
            'password'   => password_hash($request->getPost('pass_user'), PASSWORD_DEFAULT),
            'company_id' => $request->getPost('company_user'),
            'category_id'=> $request->getPost('role_user'),
            'branch_id'  => $request->getPost('branch_user'),
            'is_active'  => $request->getPost('status_user'),
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => session()->get('user_id'),
            'updated_by' => session()->get('user_id')
        ];

        // upload foto
        $file = $request->getFile('foto_user');
        if ($file && $file->isValid() && ! $file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move('uploads/profiles/', $newName);
            $data['photo'] = $newName;
        }

        $this->userModel->insert($data);

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

        $user = $this->userModel->find($id);

        if (!$user) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        return $this->response->setJSON([
            'status' => true,
            'data'   => $user
        ]);
    }

    public function update()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $id = $this->request->getPost('id');
        $user = $this->userModel->find($id);

        if (!$user) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        $data = [
            'name'       => $this->request->getPost('name_user'),
            'company_id' => $this->request->getPost('company_user'),
            'phone'      => $this->request->getPost('hp_user'),
            'is_active'  => $this->request->getPost('status_user'),
            'updated_by' => session()->get('user_id')
        ];

        // GANTI PASS
        if ($this->request->getPost('pass_user')) {
            $data['password'] = password_hash(
                $this->request->getPost('pass_user'),
                PASSWORD_DEFAULT
            );
        }

        // UPLOAD FOTO
        $file = $this->request->getFile('foto_user');
        if ($file && $file->getError() !== UPLOAD_ERR_NO_FILE) {
            // VALIDASI FILE
            if (! $file->isValid() || ! in_array($file->getMimeType(), [
                'image/png',
                'image/jpeg',
                'image/webp'
            ])) {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Photo format must be PNG, JPG or WEBP'
                ]);
            }
            // UPLOAD FILE
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/profiles/', $newName);

            // HAPUS FOTO LAMA
            if (!empty($user['photo']) && file_exists(FCPATH . 'uploads/profiles/' . $user['photo'])) {
                unlink(FCPATH . 'uploads/profiles/' . $user['photo']);
            }

            // SIMPAN DENGAN PATH
            $data['photo'] = $newName;
        }

        if ($this->userModel->update($id, $data)) {
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

    public function delete()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $id = $this->request->getPost('id');

        if (!$id) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'ID not valid'
            ]);
        }

        $data = [
            'updated_by'  => session()->get('user_id'),
            'deleted_at'  => date('Y-m-d H:i:s'),
            'deleted_by'  => session()->get('user_id')
        ];

        $deleted = $this->userModel->update($id, $data); // SOFT DELETE

        if ($deleted) {
            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Data deleted successfully'
            ]);
        }

        return $this->response->setJSON([
            'status'  => false,
            'message' => 'Failed to delete data'
        ]);
    }

    public function getRoles()
    {
        $request = service('request');

        $companyId = $request->getGet('company_id');
        $branchId  = $request->getGet('branch_id');

        $query = $this->categoryModel
            ->select('
                id,
                name,
                company_id,
                branch_id
            ')
            ->where('status', 'active');

        // =========================================
        // FILTER COMPANY
        // =========================================
        if (!empty($companyId)) {

            $query->where(
                'company_id',
                $companyId
            );
        }

        // =========================================
        // FILTER BRANCH
        // =========================================
        if (!empty($branchId)) {

            $query->where(
                'branch_id',
                $branchId
            );
        }

        $roles = $query
            ->orderBy('name', 'ASC')
            ->findAll();

        return $this->response->setJSON([
            'status' => true,
            'data'   => $roles
        ]);
    }
}
