<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\JournalHeaderModel;
use App\Models\JournalDetailModel;

class JournalController extends BaseController
{
    protected JournalHeaderModel $headerModel;
    protected JournalDetailModel $detailModel;

    public function __construct()
    {
        $this->headerModel = new JournalHeaderModel();
        $this->detailModel = new JournalDetailModel();
    }

    public function index()
    {
        return view('accounting/journal/index', [
            'title' => 'Journal'
        ]);
    }

    public function datatable()
    {
        $request = service('request');

        $searchValue = $request->getPost('search')['value'] ?? null;
        $length      = (int)$request->getPost('length');
        $start       = (int)$request->getPost('start');
        $draw        = (int)$request->getPost('draw');

        $builder = $this->headerModel
            ->select('journal_headers.*, 
                      SUM(journal_details.debit) as total')
            ->join('journal_details','journal_details.journal_id = journal_headers.id','left')
            ->groupBy('journal_headers.id');

        $recordsTotal = (clone $builder)->countAllResults(false);

        if ($searchValue) {
            $builder->groupStart()
                ->like('journal_no', $searchValue)
                ->orLike('description', $searchValue)
            ->groupEnd();
        }

        $recordsFiltered = (clone $builder)->countAllResults(false);

        $rows = $builder
            ->orderBy('journal_headers.id','DESC')
            ->limit($length,$start)
            ->get()
            ->getResultArray();

        $result = [];
        $no = $start + 1;

        foreach ($rows as $row) {

            $badge = match($row['status']) {
                'draft'     => '<span class="badge bg-label-secondary">Draft</span>',
                'submitted' => '<span class="badge bg-label-warning">Submitted</span>',
                'approved'  => '<span class="badge bg-label-info">Approved</span>',
                'posted'    => '<span class="badge bg-label-success">Posted</span>',
                default     => '<span class="badge bg-label-dark">Unknown</span>',
            };

            $result[] = [
                'no'          => $no++.'.',
                'journal_no'  => esc($row['journal_no']),
                'date'        => date('d-m-Y', strtotime($row['journal_date'])),
                'description' => esc($row['description']),
                'total'       => number_format($row['total'] ?? 0,2),
                'status'      => $badge,
                'action'      => '
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-primary btn-view" data-id="'.$row['id'].'">View</button>
                        <button class="btn btn-sm btn-success btn-post" data-id="'.$row['id'].'">Post</button>
                    </div>
                '
            ];
        }

        return $this->response->setJSON([
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $result
        ]);
    }

    public function submit($id)
    {
        $journal = $this->header->find($id);

        (new ApprovalService())->init(
            'journal',
            $id,
            $journal['total_amount']
        );

        return response()->setJSON([
            'status' => true,
            'message' => 'Submitted for approval'
        ]);
    }
}
