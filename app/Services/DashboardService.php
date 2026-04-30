<?php

namespace App\Services;

class DashboardService
{
    protected $db;

    public function __construct()
    {
        $this->db = db_connect();
    }

    public function getDashboardData($params)
    {
        $companyId  = $params['company_id'];
        $branchId   = $params['branch_id'];
        $categoryId = $params['category_id'] ?? null;

        $month = date('n');
        $year  = date('Y');

        $filters = $this->buildFilter($branchId, $categoryId);

        $summary   = $this->getAccountingSummary($companyId, $month, $year, $filters);
        $today     = $this->getTodaySummary($companyId, $filters);
        $order     = $this->getOrderSummary($month, $year, $branchId, $categoryId);
        $kpi       = $this->buildKPI($order);
        $branch    = $this->getBranchAnalyticsFull($companyId, $month, $year, $categoryId, $branchId);
        $history   = $this->getHistoryFull($companyId, $month, $year, $branchId, $categoryId);
        $approval  = $this->getApprovalStats($companyId);
        $department= $this->getDepartmentSummary($companyId, $branchId, $order['estimated'], $categoryId);

        return [
            // ACCOUNTING
            'revenue' => $summary['revenue'],
            'expense' => $summary['expense'],
            'cogs'    => $summary['cogs'],
            'profit'  => $summary['profit'],

            // TODAY
            'todayRevenue' => $today['todayRevenue'],
            'todayProfit'  => $today['todayProfit'],

            // ORDER
            'estimated'   => $order['estimated'],
            'actualCash'  => $order['actualCash'],
            'outstanding' => $order['outstanding'],

            // KPI
            'collectionRate' => $kpi['collectionRate'],

            // APPROVAL
            'pending' => $approval['pending'],
            'posted'  => $approval['posted'],

            // BRANCH
            'branches'  => $branch['branches'],

            // DEPARTMENT
            'departmentSummary' => $department,

            // HISTORY
            'historyLabels' => $history['historyLabels'],
            'historyRevenue'=> $history['historyRevenue'],
            'historyCash'   => $history['historyCash'],
            'historyOutstanding' => $history['historyOutstanding'],

            // META
            'title' => 'Dashboard',
            'month' => $month,
            'year'  => $year,
        ];
    }

    /*
    ==============================
    FILTER BUILDER
    ==============================
    */
    private function buildFilter($branchId, $categoryId)
    {
        $baseQuery = '';
        $baseParams = [];

        $trxQuery = '';
        $trxParams = [];

        $useTrx = false;

        // ==============================
        // CASE 1: CATEGORY ADA
        // ==============================
        if (!empty($categoryId) && $categoryId != 0) {

            $trxTypes = $this->getTrxByCategory($categoryId);

            if (!empty($trxTypes)) {
                $useTrx = true;

                $in = implode(',', array_fill(0, count($trxTypes), '?'));
                $trxQuery = " AND t.trx_type IN ($in)";
                $trxParams = $trxTypes;
            }

            // ❗ TIDAK pakai branch filter
        }

        // ==============================
        // CASE 2: CATEGORY TIDAK ADA
        // ==============================
        else {

            if (!empty($branchId)) {
                $baseQuery .= " AND jh.branch_id = ?";
                $baseParams[] = $branchId;
            }
        }

        return [
            'baseQuery'  => $baseQuery,
            'baseParams' => $baseParams,
            'trxQuery'   => $trxQuery,
            'trxParams'  => $trxParams,
            'useTrx'     => $useTrx
        ];
    }

    private function getTrxByCategory($categoryId)
    {
        $rows = $this->db->query("
            SELECT trx_type FROM category_trx_map WHERE category_id = ?
        ", [$categoryId])->getResultArray();

        return array_column($rows, 'trx_type');
    }

    /*
    ==============================
    ACCOUNTING
    ==============================
    */
    private function getAccountingSummary($companyId, $month, $year, $filters)
    {
        $isOwner = session('is_owner');

        // ======================
        // PARAMETER
        // ======================
        $params = [$companyId, $month, $year];

        // ======================
        // FILTER HANDLING
        // ======================
        $baseQuery = '';
        $trxQuery  = '';

        if (!$isOwner) {

            // branch filter
            $params = array_merge($params, $filters['baseParams']);
            $baseQuery = $filters['baseQuery'];

            // trx filter
            if ($filters['useTrx']) {
                $params = array_merge($params, $filters['trxParams']);
                $trxQuery = $filters['trxQuery'];
            }
        }

        // ======================
        // JOIN
        // ======================
        $joinTransaction = (!$isOwner && $filters['useTrx'])
            ? "JOIN transactions t ON t.journal_id = jh.id"
            : "";

        // ======================
        // QUERY
        // ======================
        $row = $this->db->query("
            SELECT
                SUM(CASE 
                    WHEN coa.account_type='revenue' 
                    THEN jd.credit - jd.debit ELSE 0 END) revenue,

                SUM(CASE 
                    WHEN coa.account_type='cogs' 
                    THEN jd.debit - jd.credit ELSE 0 END) cogs,

                SUM(CASE 
                    WHEN coa.account_type IN ('expense','cogs')
                    THEN jd.debit - jd.credit ELSE 0 END) expense

            FROM journal_details jd
            JOIN journal_headers jh ON jh.id = jd.journal_id
            $joinTransaction
            JOIN coa ON coa.id = jd.account_id

            WHERE jh.company_id = ?
              AND jh.period_month = ?
              AND jh.period_year = ?
              AND jh.status = 'posted'
              AND coa.parent_id IS NOT NULL

              $baseQuery
              $trxQuery

        ", $params)->getRowArray();

        return [
            'revenue' => (float)($row['revenue'] ?? 0),
            'expense' => (float)($row['expense'] ?? 0),
            'cogs'    => (float)($row['cogs'] ?? 0),
            'profit'  => ((float)$row['revenue'] - (float)$row['cogs']) - (float)$row['expense'],
        ];
    }

    /*
    ==============================
    TODAY
    ==============================
    */
    private function getTodaySummary($companyId, $filters)
    {
        $isOwner = session('is_owner');

        // ======================
        // PARAMETER
        // ======================
        $params = [$companyId];

        $baseQuery = '';
        $trxQuery  = '';

        // ======================
        // FILTER (NON OWNER ONLY)
        // ======================
        if (!$isOwner) {

            // branch filter
            $params = array_merge($params, $filters['baseParams']);
            $baseQuery = $filters['baseQuery'];

            // trx filter (category)
            if ($filters['useTrx']) {
                $params = array_merge($params, $filters['trxParams']);
                $trxQuery = $filters['trxQuery'];
            }
        }

        // ======================
        // JOIN TRANSACTION
        // ======================
        $joinTransaction = (!$isOwner && $filters['useTrx'])
            ? "JOIN transactions t ON t.journal_id = jh.id"
            : "";

        // ======================
        // QUERY
        // ======================
        $row = $this->db->query("
            SELECT
                SUM(CASE 
                    WHEN coa.account_type='revenue' 
                    THEN jd.credit - jd.debit ELSE 0 END) revenue,

                SUM(CASE 
                    WHEN coa.account_type='expense' 
                    THEN jd.debit - jd.credit ELSE 0 END) expense

            FROM journal_details jd
            JOIN journal_headers jh ON jh.id = jd.journal_id
            $joinTransaction
            JOIN coa ON coa.id = jd.account_id

            WHERE jh.company_id = ?
              AND DATE(jh.created_at)=CURDATE()
              AND jh.status='posted'

              $baseQuery
              $trxQuery

        ", $params)->getRowArray();

        $revenue = (float)($row['revenue'] ?? 0);
        $expense = (float)($row['expense'] ?? 0);

        return [
            'todayRevenue' => $revenue,
            'todayProfit'  => $revenue - $expense,
        ];
    }

    /*
    ==============================
    ORDER
    ==============================
    */
    private function getOrderSummary($month, $year, $branchId, $categoryId)
    {
        $isOwner = session('is_owner');

        $where = '';
        $params = [$month, $year];

        // ======================
        // FILTER
        // ======================
        if (!$isOwner) {

            if (!empty($branchId)) {
                $where .= " AND branch_id = ?";
                $params[] = $branchId;
            }

            // ❗ orders tidak pakai category filter
            // jadi dept tetap ikut branch
        }

        // ======================
        // QUERY
        // ======================
        $row = $this->db->query("
            SELECT
                SUM(total_amount) estimated,
                SUM(deposit) actual,
                SUM(total_amount - deposit) outstanding
            FROM orders
            WHERE MONTH(created_at)=?
              AND YEAR(created_at)=?
              $where
        ", $params)->getRowArray();

        return [
            'estimated'   => (float)($row['estimated'] ?? 0),
            'actualCash'  => (float)($row['actual'] ?? 0),
            'outstanding' => (float)($row['outstanding'] ?? 0),
        ];
    }

    private function buildKPI($order)
    {
        return [
            'collectionRate' => $order['estimated'] > 0
                ? ($order['actualCash'] / $order['estimated']) * 100
                : 0
        ];
    }

    /*
    ==============================
    BRANCH ANALYTICS
    ==============================
    */
    private function getBranchAnalyticsFull($companyId, $month, $year, $categoryId, $branchId)
    {
        $db = $this->db;
        $isOwner = session('is_owner');

        // ==============================
        // CATEGORY FILTER
        // ==============================
        $useCategory = !empty($categoryId) && $categoryId != 0;
        $whereCategory = '';
        $paramsCategory = [];

        if ($useCategory) {
            $trxTypes = $this->getTrxByCategory($categoryId);

            if (!empty($trxTypes)) {
                $in = implode(',', array_fill(0, count($trxTypes), '?'));

                $whereCategory = "
                    AND EXISTS (
                        SELECT 1 
                        FROM transactions t
                        WHERE t.journal_id = jh.id
                        AND t.trx_type IN ($in)
                    )
                ";

                $paramsCategory = $trxTypes;
            }
        }

        // ==============================
        // BRANCH FILTER
        // ==============================
        $whereBranch = '';
        $params = [
            $companyId,
            $month,
            $year,
            $companyId
        ];

        if (!$isOwner && !empty($branchId)) {
            $whereBranch = "AND b.id = ?";
            $params[] = $branchId;
        }

        $params = array_merge($params, $paramsCategory);

        // ==============================
        // MAIN QUERY
        // ==============================
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
                    WHEN coa.account_type IN ('expense','cogs') 
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

            LEFT JOIN journal_details jd ON jd.journal_id = jh.id
            LEFT JOIN coa ON coa.id = jd.account_id

            WHERE b.company_id = ?
            $whereBranch
            $whereCategory

            GROUP BY b.id, b.branch_name, bt.target
        ", $params)->getResultArray();


        // ==============================
        // TARGET MAP
        // ==============================
        $targetMap = [];
        foreach ($branchData as $row) {
            $targetMap[(int)$row['id']] = (float)$row['target'];
        }

        // ==============================
        // RATIO
        // ==============================
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
            $bid = (int)$r['hotel_id'];

            $totalRatio = (float)$r['total_spend'] + (float)$r['total_worker'];
            $target = $targetMap[$bid] ?? 0;

            $swMap[$bid] = $target > 0
                ? ($target * $totalRatio / 100)
                : 0;
        }

        // ==============================
        // FINAL ARRAY (GROUPED)
        // ==============================
        $branches = [];

        foreach ($branchData as $row) {

            $id = (int)$row['id'];

            // init branch kalau belum ada
            if (!isset($branches[$id])) {
                $branches[$id] = [
                    'branch_id'   => $id,
                    'branch_name' => $row['branch_name'],
                    'items'       => []
                ];
            }

            // push target sebagai item
            $branches[$id]['items'][] = [
                'target'  => (float)$row['target'],
                'revenue' => (float)$row['revenue'],
                'expense' => (float)$row['expense'],
                'sw'      => (float)($swMap[$id] ?? 0)
            ];
        }

        // reset index
        $branches = array_values($branches);

        return [
            'branches' => $branches
        ];
    }

    /*
    ==============================
    HISTORY
    ==============================
    */
    private function getHistoryFull($companyId, $month, $year, $branchId, $categoryId)
    {
        $isOwner = session('is_owner');

        // ==============================
        // BRANCH FILTER
        // ==============================
        $whereBranch = '';
        $paramsRevenue = [$companyId, $month, $year];

        if (!$isOwner && !empty($branchId)) {
            $whereBranch = "AND jh.branch_id = ?";
            $paramsRevenue[] = $branchId;
        }

        // ==============================
        // CATEGORY FILTER
        // ==============================
        $whereCategory = '';
        $paramsCategory = [];

        if (!empty($categoryId) && $categoryId != 0) {
            $trxTypes = $this->getTrxByCategory($categoryId);

            if (!empty($trxTypes)) {
                $in = implode(',', array_fill(0, count($trxTypes), '?'));

                $whereCategory = "
                    AND EXISTS (
                        SELECT 1
                        FROM transactions t
                        WHERE t.journal_id = jh.id
                        AND t.trx_type IN ($in)
                    )
                ";

                $paramsCategory = $trxTypes;
            }
        }

        $paramsRevenue = array_merge($paramsRevenue, $paramsCategory);

        // ==============================
        // REVENUE HISTORY
        // ==============================
        $revenueHistory = $this->db->query("
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
              $whereBranch
              $whereCategory
              AND jh.status = 'posted'
            GROUP BY DATE(jh.created_at)
            ORDER BY tanggal ASC
        ", $paramsRevenue)->getResultArray();


        // ==============================
        // ORDER HISTORY
        // ==============================
        $paramsOrder = [$month, $year];
        $whereOrderBranch = '';

        if (!$isOwner && !empty($branchId)) {
            $whereOrderBranch = "AND branch_id = ?";
            $paramsOrder[] = $branchId;
        }

        $orderHistory = $this->db->query("
            SELECT 
                DATE(created_at) as tanggal,
                SUM(COALESCE(total_amount,0)) as estimated,
                SUM(COALESCE(deposit,0)) as actual,
                SUM(COALESCE(total_amount,0) - COALESCE(deposit,0)) as outstanding
            FROM orders
            WHERE MONTH(created_at) = ?
              AND YEAR(created_at) = ?
              $whereOrderBranch
            GROUP BY DATE(created_at)
            ORDER BY tanggal ASC
        ", $paramsOrder)->getResultArray();


        // ==============================
        // FORMAT
        // ==============================
        $historyLabels = [];
        $historyRevenue = [];
        $historyCash = [];
        $historyOutstanding = [];

        $orderMap = [];
        foreach ($orderHistory as $row) {
            $orderMap[$row['tanggal']] = $row;
        }

        foreach ($revenueHistory as $row) {
            $tgl = $row['tanggal'];

            $historyLabels[] = date('d M', strtotime($tgl));
            $historyRevenue[] = (float)($row['revenue'] ?? 0);

            $historyCash[] = isset($orderMap[$tgl])
                ? (float)($orderMap[$tgl]['actual'] ?? 0)
                : 0;

            $historyOutstanding[] = isset($orderMap[$tgl])
                ? (float)($orderMap[$tgl]['outstanding'] ?? 0)
                : 0;
        }

        return [
            'historyLabels' => $historyLabels,
            'historyRevenue'=> $historyRevenue,
            'historyCash'   => $historyCash,
            'historyOutstanding' => $historyOutstanding,
        ];
    }

    private function getApprovalStats($companyId, $branchId = null, $categoryId = null)
    {
        $isOwner = session('is_owner');

        $builderPending = $this->db->table('journal_headers')
            ->where('company_id', $companyId)
            ->where('status', 'waiting');

        $builderPosted = $this->db->table('journal_headers')
            ->where('company_id', $companyId)
            ->where('status', 'posted');

        // ==============================
        // BRANCH FILTER
        // ==============================
        if (!$isOwner && !empty($branchId)) {
            $builderPending->where('branch_id', $branchId);
            $builderPosted->where('branch_id', $branchId);
        }

        // ==============================
        // CATEGORY FILTER
        // ==============================
        if (!$isOwner && !empty($categoryId) && $categoryId != 0) {

            $trxTypes = $this->getTrxByCategory($categoryId);

            if (!empty($trxTypes)) {
                $builderPending->whereIn('id', function($sub) use ($trxTypes) {
                    $sub->select('journal_id')
                        ->from('transactions')
                        ->whereIn('trx_type', $trxTypes);
                });

                $builderPosted->whereIn('id', function($sub) use ($trxTypes) {
                    $sub->select('journal_id')
                        ->from('transactions')
                        ->whereIn('trx_type', $trxTypes);
                });
            }
        }

        return [
            'pending' => $builderPending->countAllResults(),
            'posted'  => $builderPosted->countAllResults(),
        ];
    }

    private function getDepartmentSummary($companyId, $branchId, $estimated, $categoryId)
    {
        $db = $this->db;
        $isOwner = session('is_owner');

        // ==============================
        // GET BRANCHES
        // ==============================
        if ($isOwner) {
            $branches = $db->table('branches')
                ->where('company_id', $companyId)
                ->get()
                ->getResultArray();
        } else {
            $branches = $db->table('branches')
                ->where('id', $branchId)
                ->get()
                ->getResultArray();
        }

        // ==============================
        // RESULT
        // ==============================
        $result = [];

        foreach ($branches as $b) {

            $bId = $b['id'];

            // ==============================
            // GET CATEGORY PER BRANCH (FIXED)
            // ==============================
            $where = "WHERE c.status='active' AND c.branch_id = ?";
            $params = [$bId];

            if (!empty($categoryId) && $categoryId != 0) {
                $where .= " AND c.id = ?";
                $params[] = $categoryId;
            }

            $categories = $db->query("
                SELECT c.id, c.name
                FROM categories c
                $where
            ", $params)->getResultArray();

            // ==============================
            // TARGET PER BRANCH
            // ==============================
            $rowTarget = $db->table('branches_target')
                ->select('target')
                ->where('branch_id', $bId)
                ->get()
                ->getRowArray();

            $target = (float)($rowTarget['target'] ?? 0);

            // ==============================
            // RATIO PER BRANCH
            // ==============================
            $ratioSpend = $db->query("
                SELECT department_category, MAX(max_value) as max_value
                FROM ratio_spend
                WHERE hotel_id = ?
                GROUP BY department_category
            ", [$bId])->getResultArray();

            $ratioWorker = $db->query("
                SELECT department_category, MAX(min_value) as min_value
                FROM ratio_worker
                WHERE hotel_id = ?
                GROUP BY department_category
            ", [$bId])->getResultArray();

            $spendMap  = array_column($ratioSpend, 'max_value', 'department_category');
            $workerMap = array_column($ratioWorker, 'min_value', 'department_category');

            foreach ($categories as $cat) {

                $catName = $cat['name'];

                $spendRatio  = (float)($spendMap[$catName] ?? 0);
                $workerRatio = (float)($workerMap[$catName] ?? 0);

                $trxTypes = $this->getTrxByCategory($cat['id']);

                // ==============================
                // EXPENSE (PER BRANCH)
                // ==============================
                $expense = 0;

                if (!empty($trxTypes)) {

                    $in = implode(',', array_fill(0, count($trxTypes), '?'));

                    $paramsExp = array_merge([$companyId, $bId], $trxTypes);

                    $rowExp = $db->query("
                        SELECT
                            SUM(
                                CASE 
                                    WHEN coa.account_type IN ('expense','cogs')
                                    THEN COALESCE(jd.debit,0) - COALESCE(jd.credit,0)
                                    ELSE 0
                                END
                            ) as expense
                        FROM journal_details jd
                        JOIN journal_headers jh ON jh.id = jd.journal_id
                        JOIN coa ON coa.id = jd.account_id
                        WHERE jh.company_id = ?
                          AND jh.branch_id = ?
                          AND jh.status = 'posted'
                          AND EXISTS (
                              SELECT 1
                              FROM transactions t
                              WHERE t.journal_id = jh.id
                              AND t.trx_type IN ($in)
                              AND t.trx_type != 'expense_payroll'
                          )
                    ", $paramsExp)->getRowArray();

                    $expense = (float)($rowExp['expense'] ?? 0);
                }

                // ==============================
                // WORKFORCE (PER BRANCH + CATEGORY)
                // ==============================
                $workforce = 0;

                if ($workerRatio > 0) {

                    $rowWorker = $db->query("
                        SELECT
                            SUM(
                                CASE 
                                    WHEN coa.account_type = 'expense'
                                    THEN COALESCE(jd.debit,0) - COALESCE(jd.credit,0)
                                    ELSE 0
                                END
                            ) as workforce
                        FROM journal_details jd
                        JOIN journal_headers jh ON jh.id = jd.journal_id
                        JOIN transactions t ON t.journal_id = jh.id
                        JOIN coa ON coa.id = jd.account_id
                        WHERE jh.company_id = ?
                          AND jh.branch_id = ?
                          AND jh.status = 'posted'
                          AND t.category_id = ?
                          AND t.trx_type IN ('expense_payroll','expense_salary')
                    ", [$companyId, $bId, $cat['id']])->getRowArray();

                    $workforce = (float)($rowWorker['workforce'] ?? 0);
                }

                // ==============================
                // LIMIT
                // ==============================
                $limitSpend  = $target * ($spendRatio / 100);
                $limitWorker = $estimated * ($workerRatio / 100);

                // ==============================
                // PERCENT
                // ==============================
                $actualSpendPercent = $limitSpend > 0
                    ? ($expense / $limitSpend) * 100
                    : 0;

                $actualWorkerPercent = $limitWorker > 0
                    ? ($workforce / $limitWorker) * 100
                    : 0;

                // ==============================
                // STATUS
                // ==============================
                $statusSpend  = $expense > $limitSpend ? 'OVER' : 'SAFE';
                $statusWorker = $workforce > $limitWorker ? 'OVER' : 'SAFE';

                $result[] = [
                    'branch_id'   => $bId,
                    'branch_name' => $b['branch_name'],

                    'id'   => $cat['id'],
                    'name' => $catName,

                    'target'    => $target,
                    'estimated' => $estimated,

                    'spend_ratio'  => $spendRatio,
                    'worker_ratio' => $workerRatio,

                    'limit_spend'  => $limitSpend,
                    'limit_worker' => $limitWorker,

                    'expense'   => $expense,
                    'workforce' => $workforce,

                    'actual_spend_percent'  => $actualSpendPercent,
                    'actual_worker_percent' => $actualWorkerPercent,

                    'status_spend'  => $statusSpend,
                    'status_worker' => $statusWorker
                ];
            }
        }

        return $result;
    }
}