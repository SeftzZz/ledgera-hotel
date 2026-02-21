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
            c.id,
            c.account_name,
            c.account_type,
            COALESCE(ob.opening_balance,0) as opening_balance,
            COALESCE(SUM(jd.debit),0) as total_debit,
            COALESCE(SUM(jd.credit),0) as total_credit
        ");

        $builder->join(
            'coa_opening_balances ob',
            "ob.coa_id = c.id 
             AND ob.period_year = {$year}
             AND ob.company_id = {$companyId}",
            'left'
        );

        $builder->join(
            'journal_details jd',
            'jd.account_id = c.id',
            'left'
        );

        $builder->join(
            'journal_headers jh',
            "jh.id = jd.journal_id
             AND jh.status = 'posted'
             AND jh.period_year = {$year}
             AND jh.company_id = {$companyId}",
            'left'
        );

        $builder->where('c.company_id', $companyId);
        $builder->where('c.parent_id IS NOT NULL'); // 🔥 PENTING
        $builder->whereIn('c.account_type', ['revenue','expense','cogs']);
        $builder->groupBy('c.id');
        $builder->orderBy('c.account_code','ASC');

        $rows = $builder->get()->getResultArray();

        $revenue = [];
        $expense = [];
        $cogs    = [];

        $totalRevenue = 0;
        $totalExpense = 0;
        $totalCogs    = 0;

        foreach ($rows as $row) {

            // ===== NORMAL BALANCE RULE =====
            if ($row['account_type'] == 'revenue') {
                $ending = $row['opening_balance']
                        + $row['total_credit']
                        - $row['total_debit'];
            } else {
                $ending = $row['opening_balance']
                        + $row['total_debit']
                        - $row['total_credit'];
            }

            $ending = max(0, $ending);

            if ($row['account_type'] == 'revenue') {
                $revenue[] = $row + ['balance'=>$ending];
                $totalRevenue += $ending;
            }

            if ($row['account_type'] == 'cogs') {
                $cogs[] = $row + ['balance'=>$ending];
                $totalCogs += $ending;
            }

            if ($row['account_type'] == 'expense') {
                $expense[] = $row + ['balance'=>$ending];
                $totalExpense += $ending;
            }
        }

        $grossProfit = $totalRevenue - $totalCogs;
        $netProfit   = $grossProfit - $totalExpense;

        return view('accounting/income_statement/index', [
            'title'         => 'Income Statement',
            'revenue'       => $revenue,
            'expense'       => $expense,
            'cogs'          => $cogs,
            'totalRevenue'  => $totalRevenue,
            'totalExpense'  => $totalExpense,
            'totalCogs'     => $totalCogs,
            'grossProfit'   => $grossProfit,
            'netProfit'     => $netProfit,
            'year'          => $year
        ]);
    }
}