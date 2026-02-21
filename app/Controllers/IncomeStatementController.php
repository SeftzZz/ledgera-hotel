<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class IncomeStatementController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $companyId = session()->get('company_id') ?? 1;
        $year      = date('Y');

        $builder = $this->db->table('coa c');

        $builder->select("
            c.account_name,
            c.account_type,
            COALESCE(ob.opening_balance,0) as opening_balance,
            COALESCE(SUM(jd.debit),0) as total_debit,
            COALESCE(SUM(jd.credit),0) as total_credit
        ");

        $builder->join(
            'coa_opening_balances ob',
            "ob.coa_id = c.id AND ob.period_year = {$year}",
            'left'
        );

        $builder->join('journal_details jd','jd.account_id = c.id','left');
        $builder->join(
            'journal_headers jh',
            "jh.id = jd.journal_id
             AND jh.status='posted'
             AND jh.period_year={$year}",
            'left'
        );

        $builder->where('c.company_id', $companyId);
        $builder->whereIn('c.account_type', ['revenue','expense','cogs']);
        $builder->groupBy('c.id');

        $rows = $builder->get()->getResultArray();

        $revenue = [];
        $expense = [];
        $cogs    = [];

        $totalRevenue = 0;
        $totalExpense = 0;
        $totalCogs    = 0;

        foreach ($rows as $row) {

            $balance = $row['opening_balance']
                     + $row['total_debit']
                     - $row['total_credit'];

            if ($row['account_type'] == 'revenue') {
                $amount = abs($balance);
                $revenue[] = $row + ['balance'=>$amount];
                $totalRevenue += $amount;
            }

            if ($row['account_type'] == 'cogs') {
                $amount = abs($balance);
                $cogs[] = $row + ['balance'=>$amount];
                $totalCogs += $amount;
            }

            if ($row['account_type'] == 'expense') {
                $amount = abs($balance);
                $expense[] = $row + ['balance'=>$amount];
                $totalExpense += $amount;
            }
        }

        $grossProfit = $totalRevenue - $totalCogs;
        $netProfit   = $grossProfit - $totalExpense;

        return view('income_statement/index', [
            'revenue'=>$revenue,
            'expense'=>$expense,
            'cogs'=>$cogs,
            'totalRevenue'=>$totalRevenue,
            'totalExpense'=>$totalExpense,
            'totalCogs'=>$totalCogs,
            'grossProfit'=>$grossProfit,
            'netProfit'=>$netProfit,
            'year'=>$year
        ]);
    }
}