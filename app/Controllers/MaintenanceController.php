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
            if ($row['status'] !== 'done') {
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

        $data = [
            'description'   => $description,
            'status'        => $status,
            'completed_at'  => $complete,
            'updated_at'    => date('Y-m-d H:i:s'),
            'updated_by'    => session()->get('user_id')
        ];

        $this->maintenanceModel->update($id, $data);

        $items = $this->request->getPost('items');
        if ($items) {
            // hapus lama
            $db->table('maintenance_items')
                ->where('maintenance_id', $id)
                ->delete();

            foreach ($items as $item) {
                // ambil nama sparepart dari inventori
                $inv = $db->table('inventori')
                    ->where('id', $item['id'])
                    ->get()
                    ->getRowArray();

                if (!$inv) continue;

                // insert ke maintenance_items
                $db->table('maintenance_items')->insert([
                    'maintenance_id' => $id,
                    'item_name' => $inv['sparepart'],
                    'qty' => (int)$item['qty']
                ]);

                // kurangi stok inventori
                $db->table('inventori')
                    ->where('id', $item['id'])
                    ->set('qty', 'qty - '.(int)$item['qty'], false)
                    ->update();
            }
        }

        // ================= INSERT LOG =================
        $logData = [
            'maintenance_id' => $id,
            'status'         => $status,
            'notes'          => $description,
            'created_at'     => date('Y-m-d H:i:s'),
            'created_by'     => session()->get('user_id')
        ];

        $db->table('maintenance_logs')->insert($logData);

        $db->transComplete();

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

    public function rooms()
    {
        return view('maintenance/rooms', [
            'title' => 'Rooms'
        ]);
    }

    public function datatableroom()
    {
        $request = service('request');

        $searchValue = $request->getPost('search')['value'] ?? null;
        $length = (int) $request->getPost('length');
        $start  = (int) $request->getPost('start');

        $builder = $this->roomModel
            ->where('deleted_at', null);

        if ($searchValue) {
            $builder->groupStart()
                ->like('room_no', $searchValue)
            ->groupEnd();
        }

        $recordsFiltered = $builder->countAllResults(false);

        $data = $builder
            ->orderBy('room_no', 'DESC')
            ->limit($length, $start)
            ->find();

        $recordsTotal = $this->roomModel
            ->where('deleted_at', null)
            ->countAllResults();

        $result = [];
        $no = $start + 1;

        foreach ($data as $row) {
            $action = '
                <button class="btn btn-icon btn-primary btn-edit" data-id="'.$row['id'].'" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-primary" title="Edit">
                    <i class="ti ti-pencil"></i>
                </button>

                <button class="btn btn-icon btn-danger btn-delete" data-id="'.$row['id'].'" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-danger" title="Delete">
                    <i class="ti ti-trash"></i>
                </button>
            ';

            $result[] = [
                'no_urut' => $no++ . '.',
                'room' => esc($row['room_no']),
                'action' => $action
            ];
        }

        return $this->response->setJSON([
            'draw' => (int)$request->getPost('draw'),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $result
        ]);
    }
}
