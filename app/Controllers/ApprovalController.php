<?php

namespace App\Controllers;

use App\Services\ApprovalService;
use CodeIgniter\Controller;

class ApprovalController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = db_connect();
    }

    // ==============================
    // INDEX
    // ==============================
    public function index()
    {
        return view('approval/index', [
            'title' => 'Journal Approval'
        ]);
    }

    // ==============================
    // DATATABLE
    // ==============================
    public function datatable()
    {
        $request = service('request');

        $draw   = $request->getPost('draw');
        $start  = $request->getPost('start');
        $length = $request->getPost('length');
        $search = $request->getPost('search')['value'] ?? null;

        $companyId = session('company_id');
        $userId    = session('user_id');

        // ============================
        // BASE QUERY
        // ============================
        $builder = $this->db->table('journal_headers jh');

        $builder->select("
            jh.id,
            jh.journal_no,
            jh.journal_date,
            jh.description,
            jh.status,
            SUM(jd.debit) AS total_amount
        ");

        $builder->join('journal_details jd', 'jd.journal_id = jh.id', 'left');
        $builder->join('approval_logs al', 'al.journal_id = jh.id');
        $builder->join('user_roles ur', 'ur.role_id = al.role_id');

        $builder->where('jh.company_id', $companyId);
        $builder->where('jh.status', 'waiting');
        $builder->where('al.status', 'pending');
        $builder->where('ur.user_id', $userId);

        if ($search) {
            $builder->groupStart()
                ->like('jh.journal_no', $search)
                ->orLike('jh.description', $search)
                ->groupEnd();
        }

        $builder->groupBy('jh.id');

        // ============================
        // COUNT
        // ============================
        $countBuilder = clone $builder;
        $recordsTotal = $countBuilder->get()->getNumRows();

        // ============================
        // PAGINATION
        // ============================
        $builder->orderBy('jh.journal_date', 'DESC');
        $builder->limit($length, $start);

        $query = $builder->get();
        $data  = [];

        $no = $start + 1;

        foreach ($query->getResult() as $row) {

            $statusBadge = '<span class="badge bg-warning">Waiting</span>';

            $action = '
                <button class="btn btn-info btn-sm btn-history" data-id="'.$row->id.'">
                    History
                </button>
                <button class="btn btn-success btn-sm btn-approve" data-id="'.$row->id.'">
                    Approve
                </button>
                <button class="btn btn-danger btn-sm btn-reject" data-id="'.$row->id.'">
                    Reject
                </button>
            ';
            
            $data[] = [
                'no'            => $no++,
                'journal_no'    => esc($row->journal_no),
                'journal_date'  => date('d-m-Y', strtotime($row->journal_date)),
                'description'   => esc($row->description ?? '-'),
                'total_amount'  => number_format($row->total_amount ?? 0, 2),
                'status'        => $statusBadge,
                'action'        => $action
            ];
        }

        return $this->response->setJSON([
            "draw"            => intval($draw),
            "recordsTotal"    => $recordsTotal,
            "recordsFiltered" => $recordsTotal,
            "data"            => $data
        ]);
    }

    // ==============================
    // APPROVE
    // ==============================
    public function approve($id)
    {
        try {

            (new ApprovalService())->approve(
                'journal',
                $id,
                session('user_id')
            );

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Journal approved successfully'
            ]);

        } catch (\Exception $e) {

            return $this->response->setJSON([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // ==============================
    // REJECT
    // ==============================
    public function reject($id)
    {
        $reason = $this->request->getPost('reason');

        try {

            $this->db->transStart();

            // update approval log
            $this->db->table('approval_logs')
                ->where('journal_id', $id)
                ->where('status', 'pending')
                ->set([
                    'status'       => 'rejected',
                    'note'         => $reason,
                    'approved_by'  => session('user_id'),
                    'approved_at'  => date('Y-m-d H:i:s')
                ])
                ->update();

            // update journal status
            $this->db->table('journal_headers')
                ->where('id', $id)
                ->update(['status' => 'rejected']);

            $this->db->transComplete();

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Journal rejected'
            ]);

        } catch (\Exception $e) {

            return $this->response->setJSON([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function history($id)
    {
        $logs = $this->db->table('approval_logs al')
            ->select('al.*, u.name')
            ->join('users u', 'u.id = al.approved_by', 'left')
            ->where('al.journal_id', $id)
            ->orderBy('al.step_order', 'ASC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'status' => true,
            'data'   => $logs
        ]);
    }
}