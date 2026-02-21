<?php

namespace App\Controllers;

class DashboardController extends BaseController
{
    public function index()
    {
        $companyId = session('company_id');
        $month = date('n');
        $year  = date('Y');

        $db = db_connect();

        $summary = $db->query("
            SELECT
                SUM(
                    CASE 
                        WHEN coa.account_type='revenue' 
                        THEN COALESCE(jd.credit,0) - COALESCE(jd.debit,0)
                        ELSE 0 
                    END
                ) AS revenue,

                SUM(
                    CASE 
                        WHEN coa.account_type='cogs'
                        THEN COALESCE(jd.debit,0) - COALESCE(jd.credit,0)
                        ELSE 0 
                    END
                ) AS cogs,

                SUM(
                    CASE 
                        WHEN coa.account_type='expense' 
                        THEN COALESCE(jd.debit,0) - COALESCE(jd.credit,0)
                        ELSE 0 
                    END
                ) AS expense

            FROM journal_details jd
            JOIN journal_headers jh ON jh.id = jd.journal_id
            JOIN coa ON coa.id = jd.account_id

            WHERE jh.company_id=?
              AND jh.period_month=?
              AND jh.period_year=?
              AND jh.status='posted'
              AND coa.parent_id IS NOT NULL
        ", [$companyId, $month, $year])->getRowArray();

        $pending = $db->table('journal_headers')
            ->where('company_id', $companyId)
            ->where('status', 'waiting')
            ->countAllResults();

        $revenue = $summary['revenue'] ?? 0;
        $cogs    = $summary['cogs'] ?? 0;
        $expense = $summary['expense'] ?? 0;

        $grossProfit = $revenue - $cogs;
        $profit      = $grossProfit - $expense;
        
        return view('dashboard/index', [
            'title'   => 'Dashboard',
            'month'   => $month,
            'year'    => $year,
            'revenue' => $revenue,
            'expense' => $expense,
            'profit'  => $profit,
            'pending' => $pending
        ]);
    }
}