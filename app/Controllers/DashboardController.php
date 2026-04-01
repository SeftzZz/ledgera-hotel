<?php

namespace App\Controllers;

class DashboardController extends BaseController
{
    public function index()
    {
        $companyId = session('company_id');
        $branchId  = session('branch_id');
        $month     = date('n');
        $year      = date('Y');

        $db = db_connect();

        /*
        ==============================
        ACCOUNTING SUMMARY (MONTH)
        ==============================
        */
        $summary = $db->query("
            SELECT
                SUM(CASE 
                    WHEN coa.account_type='revenue' 
                    THEN COALESCE(jd.credit,0) - COALESCE(jd.debit,0)
                    ELSE 0 
                END) AS revenue,

                SUM(CASE 
                    WHEN coa.account_type='cogs'
                    THEN COALESCE(jd.debit,0) - COALESCE(jd.credit,0)
                    ELSE 0 
                END) AS cogs,

                SUM(CASE 
                    WHEN coa.account_type='expense' 
                    THEN COALESCE(jd.debit,0) - COALESCE(jd.credit,0)
                    ELSE 0 
                END) AS expense

            FROM journal_details jd
            JOIN journal_headers jh ON jh.id = jd.journal_id
            JOIN coa ON coa.id = jd.account_id

            WHERE jh.company_id=?
              AND jh.period_month=?
              AND jh.period_year=?
              AND jh.status='posted'
              AND coa.parent_id IS NOT NULL
        ", [$companyId, $month, $year])->getRowArray();


        /*
        ==============================
        TODAY SUMMARY
        ==============================
        */
        $todaySummary = $db->query("
            SELECT
                SUM(CASE 
                    WHEN coa.account_type='revenue' 
                    THEN COALESCE(jd.credit,0) - COALESCE(jd.debit,0)
                    ELSE 0 
                END) AS revenue,

                SUM(CASE 
                    WHEN coa.account_type='cogs'
                    THEN COALESCE(jd.debit,0) - COALESCE(jd.credit,0)
                    ELSE 0 
                END) AS cogs,

                SUM(CASE 
                    WHEN coa.account_type='expense' 
                    THEN COALESCE(jd.debit,0) - COALESCE(jd.credit,0)
                    ELSE 0 
                END) AS expense

            FROM journal_details jd
            JOIN journal_headers jh ON jh.id = jd.journal_id
            JOIN coa ON coa.id = jd.account_id

            WHERE jh.company_id=?
              AND DATE(jh.created_at)=CURDATE()
              AND jh.status='posted'
              AND coa.parent_id IS NOT NULL
        ", [$companyId])->getRowArray();


        /*
        ==============================
        ORDER SUMMARY (BUSINESS)
        ==============================
        */
        $orderSummary = $db->query("
            SELECT
                SUM(COALESCE(total_amount,0)) as estimated,
                SUM(COALESCE(deposit,0)) as actual,
                SUM(COALESCE(total_amount,0) - COALESCE(deposit,0)) as outstanding
            FROM orders
            WHERE branch_id = ?
              AND MONTH(created_at) = ?
              AND YEAR(created_at) = ?
        ", [$branchId, $month, $year])->getRowArray();


        /*
        ==============================
        PENDING APPROVAL
        ==============================
        */
        $pending = $db->table('journal_headers')
            ->where('company_id', $companyId)
            ->where('status', 'waiting')
            ->countAllResults();


        /*
        ==============================
        ACCOUNTING RESULT
        ==============================
        */
        $revenue = $summary['revenue'] ?? 0;
        $cogs    = $summary['cogs'] ?? 0;
        $expense = $summary['expense'] ?? 0;

        $profit = ($revenue - $cogs) - $expense;


        /*
        ==============================
        TODAY RESULT
        ==============================
        */
        $todayRevenue = $todaySummary['revenue'] ?? 0;
        $todayCogs    = $todaySummary['cogs'] ?? 0;
        $todayExpense = $todaySummary['expense'] ?? 0;

        $todayProfit = ($todayRevenue - $todayCogs) - $todayExpense;


        /*
        ==============================
        ORDER RESULT
        ==============================
        */
        $estimated   = $orderSummary['estimated'] ?? 0;
        $actualCash  = $orderSummary['actual'] ?? 0;
        $outstanding = $orderSummary['outstanding'] ?? 0;


        /*
        ==============================
        KPI
        ==============================
        */
        $collectionRate = $estimated > 0 
            ? ($actualCash / $estimated) * 100 
            : 0;


        /*
        ==============================
        BRANCH ANALYTICS (CHART)
        ==============================
        */
        $branchData = $db->query("
            SELECT 
                b.branch_name,

                SUM(CASE 
                    WHEN coa.account_type='revenue' 
                    THEN COALESCE(jd.credit,0) - COALESCE(jd.debit,0)
                    ELSE 0 
                END) AS revenue,

                SUM(CASE 
                    WHEN coa.account_type='expense' 
                    THEN COALESCE(jd.debit,0) - COALESCE(jd.credit,0)
                    ELSE 0 
                END) AS expense

            FROM branches b

            LEFT JOIN journal_headers jh 
                ON jh.branch_id = b.id
                AND jh.company_id = ?
                AND jh.period_month = ?
                AND jh.period_year = ?
                AND jh.status = 'posted'

            LEFT JOIN journal_details jd 
                ON jd.journal_id = jh.id

            LEFT JOIN coa 
                ON coa.id = jd.account_id

            WHERE b.company_id = ?

            GROUP BY b.id, b.branch_name
        ", [$companyId, $month, $year, $companyId])->getResultArray();

        /*
        ==============================
        FORMAT CHART DATA
        ==============================
        */
        $branchLabels  = [];
        $branchRevenue = [];
        $branchExpense = [];

        foreach ($branchData as $row) {
            $branchLabels[]  = $row['branch_name'];
            $branchRevenue[] = (float)$row['revenue'];
            $branchExpense[] = (float)$row['expense'];
        }


        /*
        ==============================
        RETURN VIEW
        ==============================
        */
        return view('dashboard/index', [
            'title'   => 'Dashboard',
            'month'   => $month,
            'year'    => $year,

            // ACCOUNTING
            'revenue' => $revenue,
            'expense' => $expense,
            'profit'  => $profit,

            // TODAY
            'todayRevenue' => $todayRevenue,
            'todayProfit'  => $todayProfit,

            // ORDER
            'estimated'   => $estimated,
            'actualCash'  => $actualCash,
            'outstanding' => $outstanding,
            'collectionRate' => $collectionRate,

            // OPERATIONAL
            'pending' => $pending,

            // BRANCH CHART
            'branchLabels'  => $branchLabels,
            'branchRevenue' => $branchRevenue,
            'branchExpense' => $branchExpense
        ]);
    }
}