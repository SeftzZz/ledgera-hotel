<?php

namespace App\Controllers;

use App\Services\ClosingService;
use CodeIgniter\Controller;

class ClosingController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = db_connect();
    }

    public function index()
    {
        return view('closing/index', [
            'title' => 'Closing Period'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | DATATABLE
    |--------------------------------------------------------------------------
    */
    public function datatable()
    {
        $companyId = session('company_id');

        $periods = $this->db->table('accounting_periods')
            ->where('company_id', $companyId)
            ->orderBy('period_year', 'DESC')
            ->orderBy('period_month', 'DESC')
            ->get()
            ->getResultArray();

        $data = [];
        $no = 1;

        foreach ($periods as $row) {

            $status = $row['is_closed']
                ? '<span class="badge bg-danger">Closed</span>'
                : '<span class="badge bg-success">Open</span>';

            $action = '';

            if (!$row['is_closed']) {
                $action .= '<button class="btn btn-danger btn-sm btn-close" data-id="'.$row['id'].'">Close</button>';
            } else {
                $action .= '<button class="btn btn-warning btn-sm btn-open" data-id="'.$row['id'].'">Reopen</button>';
            }

            $data[] = [
                'no'     => $no++,
                'period' => $row['period_month'].'-'.$row['period_year'],
                'status' => $status,
                'action' => $action
            ];
        }

        return $this->response->setJSON(["data" => $data]);
    }

    /*
    |--------------------------------------------------------------------------
    | CLOSE PERIOD
    |--------------------------------------------------------------------------
    */
    public function close($id)
    {
        $period = $this->db->table('accounting_periods')
            ->where('id', $id)
            ->get()
            ->getRowArray();

        if (!$period) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Period not found'
            ]);
        }

        try {

            $this->closeYear($period['period_year']);

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Period closed successfully'
            ]);

        } catch (\Exception $e) {

            return $this->response->setJSON([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | CORE CLOSING ENGINE
    |--------------------------------------------------------------------------
    */
    public function closeYear($year)
    {
        $companyId = session('company_id');

        $db = $this->db;
        $db->transStart();

        // 🔒 Prevent double closing
        $exists = $db->table('journal_headers')
            ->where('company_id',$companyId)
            ->where('journal_no','CLOSING-'.$year)
            ->countAllResults();

        if ($exists) {
            throw new \Exception("Year already closed.");
        }

        // 1️⃣ Calculate Net Profit
        $netProfit = $this->calculateNetProfit($year);

        // 2️⃣ Insert Journal Header
        $db->table('journal_headers')->insert([
            'company_id'  => $companyId,
            'journal_no'  => 'CLOSING-'.$year,
            'journal_date'=> $year.'-12-31',
            'period_month'=> 12,
            'period_year' => $year,
            'status'      => 'posted',
            'is_locked'   => 1
        ]);

        $journalId = $db->insertID();
        $totalAmount = 0;

        // 3️⃣ Close Revenue
        $revenues = $db->table('coa')
            ->where('company_id',$companyId)
            ->where('account_type','revenue')
            ->get()->getResultArray();

        foreach ($revenues as $rev) {
            $balance = $this->getAccountBalance($rev['id'],$year);

            if ($balance != 0) {
                $db->table('journal_details')->insert([
                    'journal_id'=>$journalId,
                    'account_id'=>$rev['id'],
                    'debit'=>abs($balance),
                    'credit'=>0
                ]);

                $totalAmount += abs($balance);
            }
        }

        // 4️⃣ Close Expense & COGS
        $expenses = $db->table('coa')
            ->where('company_id',$companyId)
            ->whereIn('account_type',['expense','cogs'])
            ->get()->getResultArray();

        foreach ($expenses as $exp) {
            $balance = $this->getAccountBalance($exp['id'],$year);

            if ($balance != 0) {
                $db->table('journal_details')->insert([
                    'journal_id'=>$journalId,
                    'account_id'=>$exp['id'],
                    'debit'=>0,
                    'credit'=>abs($balance)
                ]);

                $totalAmount += abs($balance);
            }
        }

        // 5️⃣ Retained Earnings (Dynamic)
        $retained = $db->table('coa')
            ->where('company_id',$companyId)
            ->where('account_code','3400')
            ->get()
            ->getRowArray();

        if (!$retained) {
            throw new \Exception("Retained earnings account not found.");
        }

        if ($netProfit > 0) {
            $db->table('journal_details')->insert([
                'journal_id'=>$journalId,
                'account_id'=>$retained['id'],
                'credit'=>$netProfit,
                'debit'=>0
            ]);
        } else {
            $db->table('journal_details')->insert([
                'journal_id'=>$journalId,
                'account_id'=>$retained['id'],
                'debit'=>abs($netProfit),
                'credit'=>0
            ]);
        }

        $totalAmount += abs($netProfit);

        // 6️⃣ Update total amount
        $db->table('journal_headers')
            ->where('id',$journalId)
            ->update(['total_amount'=>$totalAmount]);

        // 7️⃣ Lock Period
        $db->table('accounting_periods')
            ->where('company_id',$companyId)
            ->where('period_year',$year)
            ->update(['is_closed'=>1]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new \Exception("Closing failed.");
        }
    }

    /*
    |--------------------------------------------------------------------------
    | BALANCE CALCULATOR
    |--------------------------------------------------------------------------
    */
    private function getAccountBalance($accountId,$year)
    {
        $row = $this->db->table('journal_details jd')
            ->select('SUM(jd.debit) as debit, SUM(jd.credit) as credit')
            ->join('journal_headers jh','jh.id = jd.journal_id')
            ->where('jd.account_id',$accountId)
            ->where('jh.period_year',$year)
            ->where('jh.status','posted')
            ->get()
            ->getRowArray();

        return ($row['debit'] ?? 0) - ($row['credit'] ?? 0);
    }

    /*
    |--------------------------------------------------------------------------
    | NET PROFIT
    |--------------------------------------------------------------------------
    */
    private function calculateNetProfit($year)
    {
        $companyId = session('company_id');

        $rows = $this->db->table('coa')
            ->where('company_id',$companyId)
            ->whereIn('account_type',['revenue','expense','cogs'])
            ->get()
            ->getResultArray();

        $net = 0;

        foreach ($rows as $row) {
            $balance = $this->getAccountBalance($row['id'],$year);

            if ($row['account_type'] == 'revenue') {
                $net += abs($balance);
            } else {
                $net -= abs($balance);
            }
        }

        return $net;
    }

}