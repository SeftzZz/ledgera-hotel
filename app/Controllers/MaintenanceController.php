<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RoomModel;

class MaintenanceController extends BaseController
{
    protected RoomModel $roomModel;

    public function __construct()
    {
        $this->roomModel = new RoomModel();
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
