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
            WHERE MONTH(created_at) = ?
              AND YEAR(created_at) = ?
        ", [$month, $year])->getRowArray();


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
        POSTED JOURNAL
        ==============================
        */
        $posted = $db->table('journal_headers')
            ->where('company_id', $companyId)
            ->where('status', 'posted')
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
                b.id,
                b.branch_name,
                COALESCE(bt.target,0) as target,

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

            LEFT JOIN branches_target bt ON bt.branch_id = b.id

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

            GROUP BY b.id, b.branch_name, bt.target
        ", [$companyId, $month, $year, $companyId])->getResultArray();

        /*
        ==============================
        MAP TARGET PER BRANCH
        ==============================
        */
        $targetMap = [];

        foreach ($branchData as $row) {
            $targetMap[(int)$row['id']] = (float) $row['target'];
        }

        /*
        ==============================
        GET RATIO PER BRANCH
        ==============================
        */
        $ratioData = $db->query("
            SELECT 
                s.hotel_id,
                SUM(s.max_value) AS total_spend,
                SUM(COALESCE(w.min_value,0)) AS total_worker
            FROM (
                SELECT hotel_id, department_category, MAX(max_value) AS max_value
                FROM ratio_spend
                GROUP BY hotel_id, department_category
            ) s
            LEFT JOIN (
                SELECT hotel_id, department_category, MAX(min_value) AS min_value
                FROM ratio_worker
                GROUP BY hotel_id, department_category
            ) w 
            ON w.hotel_id = s.hotel_id 
            AND w.department_category = s.department_category
            GROUP BY s.hotel_id
        ")->getResultArray();

        $swMap = [];

        foreach ($ratioData as $r) {
            $branchId = (int) $r['hotel_id'];

            $totalSpend  = (float) $r['total_spend'];
            $totalWorker = (float) $r['total_worker'];

            $totalRatio = $totalSpend + $totalWorker;

            $target = $targetMap[$branchId] ?? 0;

            $valueSW = $target > 0
                ? ($target * $totalRatio / 100)
                : 0;

            $swMap[$branchId] = $valueSW;
        }

        /*
        ==============================
        FORMAT CHART DATA
        ==============================
        */
        $branchLabels  = [];
        $branchRevenue = [];
        $branchExpense = [];
        $branchTargets = [];
        $branchSW = [];

        foreach ($branchData as $row) {
            $branchLabels[]  = $row['branch_name'];
            $branchRevenue[] = (float)$row['revenue'];
            $branchExpense[] = (float)$row['expense'];
            $branchTargets[] = (float)$row['target'];
            $id = $row['id'];
            $branchSW[] = $swMap[$id] ?? 0;
        }

        /*
        ==============================
        DEPARTMENT SUMMARY (FIXED)
        ==============================
        */
        $departmentSummary = $db->query("
            SELECT 
                c.name,

                COALESCE(rs.max_value,0) as spend_ratio,
                COALESCE(rw.min_value,0) as worker_ratio

            FROM categories c

            LEFT JOIN (
                SELECT department_category, MAX(max_value) as max_value
                FROM ratio_spend
                WHERE hotel_id = ?
                GROUP BY department_category
            ) rs 
                ON rs.department_category = c.name

            LEFT JOIN (
                SELECT department_category, MAX(min_value) as min_value
                FROM ratio_worker
                WHERE hotel_id = ?
                GROUP BY department_category
            ) rw 
                ON rw.department_category = c.name

            WHERE c.status = 'active'
        ", [$branchId, $branchId])->getResultArray();

        $branchTarget = $targetMap[$branchId] ?? 0;

        $departmentFinal = [];

        foreach ($departmentSummary as $row) {
            $departmentFinal[] = [
                'name' => $row['name'],
                'target' => $branchTarget,
                'estimated' => $estimated,
                'spend_ratio' => (float)$row['spend_ratio'],
                'worker_ratio' => (float)$row['worker_ratio']
            ];
        }

        /*
        ==============================
        MONTHLY FINANCE
        ==============================
        */
        $revenueHistory = $db->query("
            SELECT 
                DATE(jh.created_at) as tanggal,
                SUM(
                    CASE 
                        WHEN coa.account_type='revenue'
                        THEN COALESCE(jd.credit,0) - COALESCE(jd.debit,0)
                        ELSE 0
                    END
                ) as revenue
            FROM journal_details jd
            JOIN journal_headers jh ON jh.id = jd.journal_id
            JOIN coa ON coa.id = jd.account_id
            WHERE jh.company_id = ?
              AND MONTH(jh.created_at) = ?
              AND YEAR(jh.created_at) = ?
              AND jh.status = 'posted'
            GROUP BY DATE(jh.created_at)
            ORDER BY tanggal ASC
        ", [$companyId, $month, $year])->getResultArray();

        $orderHistory = $db->query("
            SELECT 
                DATE(created_at) as tanggal,
                SUM(COALESCE(total_amount,0)) as estimated,
                SUM(COALESCE(deposit,0)) as actual,
                SUM(COALESCE(total_amount,0) - COALESCE(deposit,0)) as outstanding
            FROM orders
            WHERE MONTH(created_at) = ?
              AND YEAR(created_at) = ?
            GROUP BY DATE(created_at)
            ORDER BY tanggal ASC
        ", [$month, $year])->getResultArray();
        
        $historyLabels = [];
        $historyRevenue = [];
        $historyCash = [];
        $historyOutstanding = [];

        foreach ($revenueHistory as $row) {
            $historyLabels[] = date('d M', strtotime($row['tanggal']));
            $historyRevenue[] = (float) $row['revenue'];
        }

        // map order ke tanggal
        $orderMap = [];
        foreach ($orderHistory as $row) {
            $orderMap[$row['tanggal']] = $row;
        }

        foreach ($revenueHistory as $row) {
            $tgl = $row['tanggal'];

            $historyCash[] = isset($orderMap[$tgl]) ? (float)$orderMap[$tgl]['actual'] : 0;
            $historyOutstanding[] = isset($orderMap[$tgl]) ? (float)$orderMap[$tgl]['outstanding'] : 0;
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
            'posted' => $posted,

            // BRANCH CHART
            'branchLabels'  => $branchLabels,
            'branchRevenue' => $branchRevenue,
            'branchExpense' => $branchExpense,
            'branchTargets' => $branchTargets,
            'branchSW' => $branchSW,

            'departmentSummary' => $departmentFinal,

            'historyLabels' => $historyLabels,
            'historyRevenue' => $historyRevenue,
            'historyCash' => $historyCash,
            'historyOutstanding' => $historyOutstanding,
        ]);
    }

    public function data()
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
            WHERE MONTH(created_at) = ?
              AND YEAR(created_at) = ?
        ", [$month, $year])->getRowArray();


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
        POSTED JOURNAL
        ==============================
        */
        $posted = $db->table('journal_headers')
            ->where('company_id', $companyId)
            ->where('status', 'posted')
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
                b.id,
                b.branch_name,
                COALESCE(bt.target,0) as target,

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

            LEFT JOIN branches_target bt ON bt.branch_id = b.id

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

            GROUP BY b.id, b.branch_name, bt.target
        ", [$companyId, $month, $year, $companyId])->getResultArray();

        /*
        ==============================
        MAP TARGET PER BRANCH
        ==============================
        */
        $targetMap = [];

        foreach ($branchData as $row) {
            $targetMap[(int)$row['id']] = (float) $row['target'];
        }

        /*
        ==============================
        GET RATIO PER BRANCH
        ==============================
        */
        $ratioData = $db->query("
            SELECT 
                s.hotel_id,
                SUM(s.max_value) AS total_spend,
                SUM(COALESCE(w.min_value,0)) AS total_worker
            FROM (
                SELECT hotel_id, department_category, MAX(max_value) AS max_value
                FROM ratio_spend
                GROUP BY hotel_id, department_category
            ) s
            LEFT JOIN (
                SELECT hotel_id, department_category, MAX(min_value) AS min_value
                FROM ratio_worker
                GROUP BY hotel_id, department_category
            ) w 
            ON w.hotel_id = s.hotel_id 
            AND w.department_category = s.department_category
            GROUP BY s.hotel_id
        ")->getResultArray();

        $swMap = [];

        foreach ($ratioData as $r) {
            $branchId = (int) $r['hotel_id'];

            $totalSpend  = (float) $r['total_spend'];
            $totalWorker = (float) $r['total_worker'];

            $totalRatio = $totalSpend + $totalWorker;

            $target = $targetMap[$branchId] ?? 0;

            $valueSW = $target > 0
                ? ($target * $totalRatio / 100)
                : 0;

            $swMap[$branchId] = $valueSW;
        }

        /*
        ==============================
        FORMAT CHART DATA
        ==============================
        */
        $branchLabels  = [];
        $branchRevenue = [];
        $branchExpense = [];
        $branchTargets = [];
        $branchSW = [];

        foreach ($branchData as $row) {
            $branchLabels[]  = $row['branch_name'];
            $branchRevenue[] = (float)$row['revenue'];
            $branchExpense[] = (float)$row['expense'];
            $branchTargets[] = (float)$row['target'];
            $id = $row['id'];
            $branchSW[] = $swMap[$id] ?? 0;
        }

        /*
        ==============================
        DEPARTMENT SUMMARY (FIXED)
        ==============================
        */
        $departmentSummary = $db->query("
            SELECT 
                c.name,

                COALESCE(rs.max_value,0) as spend_ratio,
                COALESCE(rw.min_value,0) as worker_ratio

            FROM categories c

            LEFT JOIN (
                SELECT department_category, MAX(max_value) as max_value
                FROM ratio_spend
                WHERE hotel_id = ?
                GROUP BY department_category
            ) rs 
                ON rs.department_category = c.name

            LEFT JOIN (
                SELECT department_category, MAX(min_value) as min_value
                FROM ratio_worker
                WHERE hotel_id = ?
                GROUP BY department_category
            ) rw 
                ON rw.department_category = c.name

            WHERE c.status = 'active'
        ", [$branchId, $branchId])->getResultArray();

        $branchTarget = $targetMap[$branchId] ?? 0;

        $departmentFinal = [];

        foreach ($departmentSummary as $row) {
            $departmentFinal[] = [
                'name' => $row['name'],
                'target' => $branchTarget,
                'estimated' => $estimated,
                'spend_ratio' => (float)$row['spend_ratio'],
                'worker_ratio' => (float)$row['worker_ratio']
            ];
        }

        /*
        ==============================
        MONTHLY FINANCE
        ==============================
        */
        $revenueHistory = $db->query("
            SELECT 
                DATE(jh.created_at) as tanggal,
                SUM(
                    CASE 
                        WHEN coa.account_type='revenue'
                        THEN COALESCE(jd.credit,0) - COALESCE(jd.debit,0)
                        ELSE 0
                    END
                ) as revenue
            FROM journal_details jd
            JOIN journal_headers jh ON jh.id = jd.journal_id
            JOIN coa ON coa.id = jd.account_id
            WHERE jh.company_id = ?
              AND MONTH(jh.created_at) = ?
              AND YEAR(jh.created_at) = ?
              AND jh.status = 'posted'
            GROUP BY DATE(jh.created_at)
            ORDER BY tanggal ASC
        ", [$companyId, $month, $year])->getResultArray();

        $orderHistory = $db->query("
            SELECT 
                DATE(created_at) as tanggal,
                SUM(COALESCE(total_amount,0)) as estimated,
                SUM(COALESCE(deposit,0)) as actual,
                SUM(COALESCE(total_amount,0) - COALESCE(deposit,0)) as outstanding
            FROM orders
            WHERE MONTH(created_at) = ?
              AND YEAR(created_at) = ?
            GROUP BY DATE(created_at)
            ORDER BY tanggal ASC
        ", [$month, $year])->getResultArray();
        
        $historyLabels = [];
        $historyRevenue = [];
        $historyCash = [];
        $historyOutstanding = [];

        foreach ($revenueHistory as $row) {
            $historyLabels[] = date('d M', strtotime($row['tanggal']));
            $historyRevenue[] = (float) $row['revenue'];
        }

        // map order ke tanggal
        $orderMap = [];
        foreach ($orderHistory as $row) {
            $orderMap[$row['tanggal']] = $row;
        }

        foreach ($revenueHistory as $row) {
            $tgl = $row['tanggal'];

            $historyCash[] = isset($orderMap[$tgl]) ? (float)$orderMap[$tgl]['actual'] : 0;
            $historyOutstanding[] = isset($orderMap[$tgl]) ? (float)$orderMap[$tgl]['outstanding'] : 0;
        }

        return $this->response->setJSON([
            'revenue' => $revenue,
            'expense' => $expense,
            'profit' => $profit,

            'todayProfit' => $todayProfit,

            'estimated' => $estimated,
            'actualCash' => $actualCash,
            'outstanding' => $outstanding,

            'pending' => $pending,
            'posted' => $posted,

            // BRANCH CHART
            'branchLabels'  => $branchLabels,
            'branchRevenue' => $branchRevenue,
            'branchExpense' => $branchExpense,
            'branchTargets' => $branchTargets,
            'branchSW' => $branchSW,

            'departmentSummary' => $departmentFinal,

            'historyLabels' => $historyLabels,
            'historyRevenue' => $historyRevenue,
            'historyCash' => $historyCash,
            'historyOutstanding' => $historyOutstanding,
        ]);
    }
}