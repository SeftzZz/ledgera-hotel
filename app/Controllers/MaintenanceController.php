<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RoomModel;
use App\Models\MaintenanceModel;

class MaintenanceController extends BaseController
{
    protected RoomModel $roomModel;
    protected MaintenanceModel $maintenanceModel;

    public function __construct()
    {
        $this->roomModel = new RoomModel();
        $this->maintenanceModel = new MaintenanceModel();
    }

    public function index()
    {
        $builder = $this->roomModel
            ->where('deleted_at', null);

        return view('maintenance/index', [
            'title' => 'Maintenance',
            'rooms' => $builder
                ->orderBy('room_no', 'ASC')
                ->findAll()
        ]);
    }

    // ===============================
    // DATATABLE SERVER SIDE - MAINTENANCE
    // ===============================
    public function datatable()
    {
        $request = service('request');

        $searchValue = $request->getPost('search')['value'] ?? null;
        $length = (int) $request->getPost('length');
        $start  = (int) $request->getPost('start');
        $order  = $request->getPost('order');

        $db = \Config\Database::connect();

        /* ================= BASE QUERY ================= */
        $baseBuilder = $db->table('maintenance m')
            ->join('rooms r', 'r.id = m.room_id', 'left')
            ->where('m.deleted_at', null);

        /* ================= FILTER SESSION ================= */
        $companyId = session()->get('company_id');
        $branchId  = session()->get('branch_id');
        $userId    = session()->get('user_id');

        $baseBuilder->where('m.company_id', $companyId);

        if ($branchId) {
            $baseBuilder->where('m.branch_id', $branchId);
        }

        /* ================= SEARCH ================= */
        if (!empty($searchValue)) {
            $searchLower = strtolower($searchValue);

            $baseBuilder->groupStart()
                ->like('r.room_no', $searchValue)
                ->orLike('m.location', $searchValue)
                ->orLike('m.issue', $searchValue)
                ->orLike('m.description', $searchValue)
                ->orLike('m.status', $searchValue);;

            // Flexible status search
            if (str_contains($searchLower, 'open')) {
                $baseBuilder->orWhere('m.status', 'open');
            }
            if (str_contains($searchLower, 'progress')) {
                $baseBuilder->orWhere('m.status', 'in_progress');
            }
            if (str_contains($searchLower, 'done')) {
                $baseBuilder->orWhere('m.status', 'done');
            }
            if (str_contains($searchLower, 'cancel')) {
                $baseBuilder->orWhere('m.status', 'cancelled');
            }

            $baseBuilder->groupEnd();
        }

        /* ================= COUNT FILTERED ================= */
        $builderFiltered = clone $baseBuilder;
        $recordsFiltered = $builderFiltered->countAllResults();

        /* ================= COUNT TOTAL ================= */
        $recordsTotal = $db->table('maintenance')
            ->where('deleted_at', null)
            ->where('company_id', $companyId)
            ->countAllResults();

        /* ================= GET DATA ================= */
        $builderData = clone $baseBuilder;

        $data = $builderData
            ->select('m.*, r.room_no')
            ->orderBy('m.id', 'DESC')
            ->limit($length, $start)
            ->get()
            ->getResultArray();

        if (!empty($order)) {
            $columns = [
                1 => 'r.room_no',
                2 => 'r.room_no',
                3 => 'm.location',
                4 => 'm.issue',
                5 => 'm.status'
            ];

            $colIndex = $order[0]['column'];
            $dir = $order[0]['dir'];

            if (isset($columns[$colIndex])) {
                $builderData->orderBy($columns[$colIndex], $dir);
            }
        } else {
            $builderData->orderBy('m.id', 'DESC');
        }

        /* ================= FORMAT DATA ================= */
        $result = [];
        $no = $start + 1;

        foreach ($data as $row) {
            // ================= STATUS =================
            $statusLabel = match($row['status']) {
                'open'         => 'Open',
                'in_progress'  => 'In Progress',
                'done'         => 'Done',
                'cancelled'    => 'Cancelled',
                default        => ucfirst($row['status'])
            };

            $badgeClass = match($row['status']) {
                'open'        => 'bg-label-primary',
                'in_progress' => 'bg-label-warning',
                'done'        => 'bg-label-success',
                'cancelled'   => 'bg-label-danger',
                default       => 'bg-label-secondary'
            };

            // ================= ACTION BUTTON =================
            $actionButtons = '';
            $actionButtons = '
                    <button class="btn btn-icon btn-info btn-detail" data-id="'.$row['id'].'" 
                        data-bs-toggle="tooltip" data-bs-placement="top" 
                        data-bs-custom-class="tooltip-info" title="Detail">
                        <i class="ti ti-eye"></i>
                    </button>';

            if ($row['status'] !== 'done') {
                $actionButtons .= '
                    <button class="btn btn-icon btn-primary btn-edit" data-id="'.$row['id'].'" 
                        data-bs-toggle="tooltip" data-bs-placement="top" 
                        data-bs-custom-class="tooltip-primary" title="Edit">
                        <i class="ti ti-pencil"></i>
                    </button>

                    <button class="btn btn-icon btn-danger btn-delete" data-id="'.$row['id'].'" 
                        data-bs-toggle="tooltip" data-bs-placement="top" 
                        data-bs-custom-class="tooltip-danger" title="Delete">
                        <i class="ti ti-trash"></i>
                    </button>
                ';
            }

            // ================= FORMAT =================
            $result[] = [
                'no_urut'      => $no++ . '.',
                'room'         => $row['room_no'] ?? '-',
                'location'     => esc($row['location'] ?? '-'),
                'issue'        => '<span title="'.esc($row['issue']).'">'
                                    .esc(mb_substr($row['issue'],0,25)).'...
                                   </span>',
                'status'       => '<span class="badge '.$badgeClass.'">'.$statusLabel.'</span>',
                'started_at'   => $row['started_at'] ? date('d M Y', strtotime($row['started_at'])) : '-',
                'completed_at' => $row['completed_at'] ? date('d M Y', strtotime($row['completed_at'])) : '-',
                'action'       => $actionButtons
            ];
        }

        return $this->response->setJSON([
            'draw'            => (int) $request->getPost('draw'),
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $result
        ]);
    }

    // ===============================
    // STORE MAINTENANCE
    // ===============================
    public function store()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // ================= INSERT MAINTENANCE =================
        $data = [
            'company_id'   => session()->get('company_id'),
            'branch_id'    => session()->get('branch_id'),
            'room_id'      => $this->request->getPost('room'),
            'location'     => $this->request->getPost('location'),
            'issue'        => $this->request->getPost('issue'),
            'description'  => $this->request->getPost('note'),
            'status'       => 'open',
            'started_at'   => $this->request->getPost('start'),
            'created_at'   => date('Y-m-d H:i:s'),
            'created_by'   => session()->get('user_id')
        ];

        $this->maintenanceModel->insert($data);

        $maintenanceId = $this->maintenanceModel->getInsertID();

        // ================= INSERT LOG =================
        $logData = [
            'maintenance_id' => $maintenanceId,
            'status'         => 'open',
            'notes'          => $this->request->getPost('issue'),
            'created_at'     => date('Y-m-d H:i:s'),
            'created_by'     => session()->get('user_id')
        ];

        $db->table('maintenance_logs')->insert($logData);

        $db->transComplete();

        // ================= RESPONSE =================
        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Failed to save maintenance'
            ]);
        }

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Maintenance added successfully'
        ]);
    }

    // ===============================
    // GET MAINTENANCE BY ID
    // ===============================
    public function getById()
    {
        $id = $this->request->getPost('id');
        $maintenance = $this->maintenanceModel
            ->where('deleted_at', null)
            ->find($id);

        if (!$maintenance) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data not found'
            ]);
        }

        return $this->response->setJSON([
            'status' => true,
            'data' => $maintenance
        ]);
    }

    // ===============================
    // GET INVENTORI
    // ===============================
    public function getInventori()
    {
        $db = \Config\Database::connect();

        $data = $db->table('inventori')
            ->select('id, sparepart, qty')
            ->where('is_delete', 0)
            ->where('qty >', 0)
            ->orderBy('sparepart', 'ASC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'status' => true,
            'data'   => $data
        ]);
    }

    // ===============================
    // UPDATE
    // ===============================
    public function update()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $id          = $this->request->getPost('id');
        $complete    = $this->request->getPost('complete');
        $status      = $this->request->getPost('status');
        $description = $this->request->getPost('note');

        $maintenance = $this->maintenanceModel->find($id);

        if (!$maintenance) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Data not found'
            ]);
        }

        // ================= VALIDASI ITEMS DULU =================
        $items = $this->request->getPost('items');

        if ($items) {
            foreach ($items as $item) {
                $inv = $db->table('inventori')
                    ->where('id', $item['id'])
                    ->get()
                    ->getRowArray();

                if (!$inv) {
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => 'Item tidak ditemukan'
                    ]);
                }

                // VALIDASI STOCK
                if ((int)$item['qty'] > (int)$inv['qty']) {
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => 'Stock tidak cukup untuk ' . $inv['sparepart']
                    ]);
                }
            }
        }

        // ================= UPDATE MAIN =================
        $data = [
            'description'   => $description,
            'status'        => $status,
            'completed_at'  => $complete,
            'updated_at'    => date('Y-m-d H:i:s'),
            'updated_by'    => session()->get('user_id')
        ];

        $this->maintenanceModel->update($id, $data);

        // ================= HANDLE ITEMS =================
        if ($items) {
            // hapus lama
            $db->table('maintenance_items')
                ->where('maintenance_id', $id)
                ->delete();

            foreach ($items as $item) {
                $inv = $db->table('inventori')
                    ->where('id', $item['id'])
                    ->get()
                    ->getRowArray();

                if (!$inv) continue;

                // insert item
                $db->table('maintenance_items')->insert([
                    'maintenance_id' => $id,
                    'item_name'      => $inv['sparepart'],
                    'qty'            => (int)$item['qty']
                ]);

                // kurangi stok
                $db->table('inventori')
                    ->where('id', $item['id'])
                    ->set('qty', 'qty - ' . (int)$item['qty'], false)
                    ->update();
            }
        }

        // ================= CREATE TRANSACTION =================
        $service = new \App\Services\TransactionService();

        $companyId = session()->get('company_id');
        $branchId  = session()->get('branch_id');
        $categoryId = (int) session()->get('category_id');

        $trxType = 'expense_other';

        if ($categoryId === 10) {
            $trxType = 'expense_maintenance';
        }

        if ($categoryId === 3) {
            $trxType = 'expense_housekeeping';
        }

        // ================= HITUNG TOTAL =================
        $totalAmount = 0;

        if ($items) {
            foreach ($items as $item) {
                // ambil inventori
                $inv = $db->table('inventori')
                    ->where('id', $item['id'])
                    ->get()
                    ->getRowArray();

                if (!$inv) continue;

                // ambil harga dari form_pengajuan_detail
                $detail = $db->table('form_pengajuan_detail')
                    ->where('sparepart', $inv['sparepart'])
                    ->orderBy('id', 'DESC') // ambil harga terakhir
                    ->get()
                    ->getRowArray();

                $harga = $detail['harga'] ?? 0;
                $pangajuanId = $detail['pengajuan_id'] ?? 0;

                $subtotal = (int)$item['qty'] * (float)$harga;

                $totalAmount += $subtotal;
            }
        }

        // ================= INSERT TRANSACTION =================
        $trxId = $service->create([
            'company_id'         => $companyId,
            'branch_id'          => $branchId > 0 ? $branchId : null,
            'branch_name'        => session()->get('branch_name'),
            'trx_date'           => date('Y-m-d'),
            'trx_type'           => $trxType,
            'reference_no'       => "PG-" . $pangajuanId,
            'amount'             => $totalAmount,
            'payment_account_id' => 3,

            // TAX
            'tax_code_id'        => 0,
            'tax_mode'           => 'exclusive'
        ]);

        // ================= INSERT LOG =================
        $db->table('maintenance_logs')->insert([
            'maintenance_id' => $id,
            'status'         => $status,
            'notes'          => $description,
            'created_at'     => date('Y-m-d H:i:s'),
            'created_by'     => session()->get('user_id')
        ]);

        $db->transComplete();

        // ================= RESULT =================
        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Update gagal'
            ]);
        }

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Maintenance successfully updated'
        ]);
    }

    // ===============================
    // DELETE (SOFT DELETE)
    // ===============================
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

        $db = \Config\Database::connect();
        $db->transStart();

        // HARD DELETE LOGS
        $db->table('maintenance_logs')
            ->where('maintenance_id', $id)
            ->delete();

        
        // SOFT DELETE MAIN DATA
        $data = [
            'deleted_at' => date('Y-m-d H:i:s'),
            'deleted_by' => session()->get('user_id')
        ];

        $this->maintenanceModel->update($id, $data);

        $db->transComplete();

        // RESULT
        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Failed to delete data'
            ]);
        }

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Data deleted successfully'
        ]);
    }

    // ===============================
    // DETAIL MAINTENACE
    // ===============================
    public function getDetail()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $id = $this->request->getPost('id');
        $db = \Config\Database::connect();

        $main = $db->table('maintenance m')
            ->select('m.*, r.room_no as room')
            ->join('rooms r', 'r.id = m.room_id', 'left')
            ->where('m.id', $id)
            ->get()
            ->getRowArray();

        if (!$main) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data not found'
            ]);
        }

        $items = $db->table('maintenance_items')
            ->where('maintenance_id', $id)
            ->get()
            ->getResultArray();

        $logs = $db->table('maintenance_logs')
            ->where('maintenance_id', $id)
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'status' => true,
            'data' => [
                ...$main,
                'items' => $items,
                'logs'  => $logs
            ]
        ]);
    }

    public function rooms()
    {
        return view('maintenance/rooms', [
            'title' => 'Rooms'
        ]);
    }

    // ===============================
    // DATATABLE SERVER SIDE - ROOMS
    // ===============================
    public function datatableroom()
    {
        $request = service('request');

        $searchValue = $request->getPost('search')['value'] ?? null;
        $length = (int) $request->getPost('length');
        $start  = (int) $request->getPost('start');
        $order  = $request->getPost('order');

        $db = \Config\Database::connect();

        /* ================= BASE QUERY ================= */
        $baseBuilder = $db->table('rooms r')
            ->join('branches b', 'b.id = r.branch_id', 'left')
            ->where('r.deleted_at', null);

        /* ================= FILTER SESSION ================= */
        $companyId = session()->get('company_id');
        $branchId  = session()->get('branch_id');
        $userId    = session()->get('user_id');

        $baseBuilder->where('r.company_id', $companyId);

        if ($branchId) {
            $baseBuilder->where('r.branch_id', $branchId);
        }

        /* ================= SEARCH ================= */
        if (!empty($searchValue)) {
            $searchLower = strtolower($searchValue);

            $baseBuilder->groupStart()
                ->like('r.room_no', $searchValue)
                ->orLike('b.branch_name', $searchValue);

            $baseBuilder->groupEnd();
        }

        /* ================= COUNT FILTERED ================= */
        $builderFiltered = clone $baseBuilder;
        $recordsFiltered = $builderFiltered->countAllResults();

        /* ================= COUNT TOTAL ================= */
        $recordsTotal = $db->table('rooms')
            ->where('deleted_at', null)
            ->where('company_id', $companyId)
            ->countAllResults();

        /* ================= GET DATA ================= */
        $builderData = clone $baseBuilder;

        /* ================= ORDERING ================= */
        $columns = [
            1 => null,              // no_urut
            2 => 'b.branch_name',   // branch
            3 => 'r.room_no'        // room
        ];

        if (!empty($order)) {
            $colIndex = $order[0]['column'];
            $dir      = $order[0]['dir'];

            if (isset($columns[$colIndex]) && $columns[$colIndex] !== null) {
                $builderData->orderBy($columns[$colIndex], $dir);
            }
        } else {
            $builderData->orderBy('r.id', 'DESC');
        }

        /* ================= EXECUTE ================= */
        $data = $builderData
            ->select('r.*, b.branch_name')
            ->limit($length, $start)
            ->get()
            ->getResultArray();

        if (!empty($order)) {
            $columns = [
                1 => 'b.branch_name',
                2 => 'b.branch_name',
                3 => 'r.room_no'
            ];

            $colIndex = $order[0]['column'];
            $dir = $order[0]['dir'];

            if (isset($columns[$colIndex])) {
                $builderData->orderBy($columns[$colIndex], $dir);
            }
        } else {
            $orderColumn = $columns[$colIndex] ?? 'r.id';
            $builderData->orderBy($orderColumn, $dir ?? 'DESC');
        }

        /* ================= FORMAT DATA ================= */
        $result = [];
        $no = $start + 1;

        foreach ($data as $row) {
            // ================= ACTION BUTTON =================
            $actionButtons = '
                <button class="btn btn-icon btn-primary btn-edit" data-id="'.$row['id'].'" 
                    data-bs-toggle="tooltip" data-bs-placement="top" 
                    data-bs-custom-class="tooltip-primary" title="Edit">
                    <i class="ti ti-pencil"></i>
                </button>

                <button class="btn btn-icon btn-danger btn-delete" data-id="'.$row['id'].'" 
                    data-bs-toggle="tooltip" data-bs-placement="top" 
                    data-bs-custom-class="tooltip-danger" title="Delete">
                    <i class="ti ti-trash"></i>
                </button>
            ';
            
            // ================= FORMAT =================
            $result[] = [
                'no_urut'      => $no++ . '.',
                'branch'       => $row['branch_name'] ?? '-',
                'room'         => $row['room_no'] ?? '-',
                'action'       => $actionButtons
            ];
        }

        return $this->response->setJSON([
            'draw'            => (int) $request->getPost('draw'),
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $result
        ]);
    }

    // ===============================
    // STORE ROOMS
    // ===============================
    public function storeroom()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $companyId = session()->get('company_id');
        $branchId  = session()->get('branch_id');

        // cek apakah name sudah ada
        $exist = $this->roomModel
            ->where('room_no', $this->request->getPost('roomno'))
            ->where('company_id', $companyId)
            ->where('branch_id', $branchId)
            ->where('deleted_at', null)
            ->first();

        if ($exist) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Room number already exists'
            ]);
        }

        // ================= INSERT ROOMS =================
        $data = [
            'company_id'   => session()->get('company_id'),
            'branch_id'    => session()->get('branch_id'),
            'room_no'      => $this->request->getPost('roomno'),
            'created_at'   => date('Y-m-d H:i:s'),
            'created_by'   => session()->get('user_id')
        ];

        $this->roomModel->insert($data);

        // ================= RESPONSE =================
        if ($this->roomModel->transStatus() === false) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Failed to save room'
            ]);
        }

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Room added successfully'
        ]);
    }

    // ===============================
    // GET ROOM BY ID
    // ===============================
    public function getByIdRoom()
    {
        $id = $this->request->getPost('id');
        $room = $this->roomModel
            ->where('company_id', session()->get('company_id'))
            ->where('branch_id', session()->get('branch_id'))
            ->where('deleted_at', null)
            ->find($id);

        if (!$room) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data not found'
            ]);
        }

        return $this->response->setJSON([
            'status' => true,
            'data' => $room
        ]);
    }

    // ===============================
    // UPDATE
    // ===============================
    public function updateroom()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $id       = $this->request->getPost('id');
        $roomno   = trim($this->request->getPost('roomno'));

        $room = $this->roomModel->find($id);

        if (!$room) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Data not found'
            ]);
        }

        // CEK DUPLIKAT NAME
        $duplicate = $this->roomModel
            ->where('company_id', session()->get('company_id'))
            ->where('branch_id', session()->get('branch_id'))
            ->where('room_no', $roomno)
            ->where('id !=', $id)
            ->where('deleted_at', null)
            ->first();

        if ($duplicate) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Room number already exists'
            ]);
        }

        // UPDATE DATA
        $data = [
            'room_no'    => $roomno,
            'updated_by' => session()->get('user_id'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->roomModel->update($id, $data);

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Room number successfully updated'
        ]);
    }

    // ===============================
    // DELETE ROOM (SOFT DELETE)
    // ===============================
    public function deleteroom()
    {
        $id = $this->request->getPost('id');

        // ambil data room
        $room = $this->roomModel->find($id);

        if (!$room) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Room tidak ditemukan'
            ]);
        }

        $this->roomModel->update($id, [
            'deleted_at' => date('Y-m-d H:i:s'),
            'deleted_by' => session()->get('user_id')
        ]);

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Room deleted successfully'
        ]);
    }
}
