<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class BalanceSheetController extends BaseController
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
            c.account_code,
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
        $builder->where('c.parent_id IS NOT NULL');
        $builder->groupBy('c.id');
        $builder->orderBy('c.account_code', 'ASC');

        $rows = $builder->get()->getResultArray();

        $assets = [];
        $liabilities = [];
        $equity = [];

        $totalAsset = 0;
        $totalLiability = 0;
        $totalEquity = 0;

        $netProfit = 0;

        foreach ($rows as $row) {

            // 🔥 Normal balance logic
            $isDebitNormal = in_array($row['account_type'], ['asset','expense','cogs']);

            if ($isDebitNormal) {
                $ending = $row['opening_balance']
                        + $row['total_debit']
                        - $row['total_credit'];
            } else {
                $ending = $row['opening_balance']
                        + $row['total_credit']
                        - $row['total_debit'];
            }

            // =============================
            // ASSETS
            // =============================
            if ($row['account_type'] == 'asset') {
                $assets[] = $row + ['balance' => $ending];
                $totalAsset += $ending;
            }

            // =============================
            // LIABILITIES
            // =============================
            if ($row['account_type'] == 'liability') {
                $liabilities[] = $row + ['balance' => $ending];
                $totalLiability += $ending;
            }

            // =============================
            // EQUITY
            // =============================
            if ($row['account_type'] == 'equity') {
                $equity[] = $row + ['balance' => $ending];
                $totalEquity += $ending;
            }

            // =============================
            // CALCULATE NET PROFIT
            // =============================
            if ($row['account_type'] == 'revenue') {
                $netProfit += $ending;
            }

            if (in_array($row['account_type'], ['expense','cogs'])) {
                $netProfit -= $ending;
            }
        }

        // 🔥 Tambahkan laba berjalan ke equity
        if ($netProfit != 0) {
            $equity[] = [
                'account_code' => '',
                'account_name' => 'Laba Tahun Berjalan',
                'account_type' => 'equity',
                'balance' => $netProfit
            ];

            $totalEquity += $netProfit;
        }

        return view('accounting/balance_sheet/index', [
            'title' => 'Balance Sheet',
            'assets' => $assets,
            'liabilities' => $liabilities,
            'equity' => $equity,
            'totalAsset' => $totalAsset,
            'totalLiability' => $totalLiability,
            'totalEquity' => $totalEquity,
            'year' => $year
        ]);
    }
}