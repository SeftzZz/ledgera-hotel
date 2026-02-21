<?php

namespace App\Services;

class ReportService
{
    public function profitLoss(int $companyId, int $month, int $year)
    {
        return db_connect()->query("
            SELECT
                coa.account_name,
                coa.account_type,
                SUM(jd.debit - jd.credit) AS balance
            FROM journal_details jd
            JOIN journal_headers jh ON jh.id = jd.journal_id
            JOIN coa ON coa.id = jd.account_id
            WHERE jh.company_id = ?
              AND jh.period_month = ?
              AND jh.period_year = ?
              AND jh.status = 'posted'
            GROUP BY coa.id
            ORDER BY coa.account_type
        ", [$companyId, $month, $year])->getResultArray();
    }

    public function balanceSheet(int $companyId, int $month, int $year)
    {
        return db_connect()->query("
            SELECT 
                coa.account_type,
                coa.account_code,
                coa.account_name,
                SUM(jd.debit - jd.credit) as balance
            FROM coa
            LEFT JOIN journal_details jd ON jd.account_id = coa.id
            LEFT JOIN journal_headers jh 
                ON jh.id = jd.journal_id
                AND jh.status = 'posted'
                AND jh.company_id = ?
                AND (
                    jh.period_year < ? 
                    OR (jh.period_year = ? AND jh.period_month <= ?)
                )
            WHERE coa.company_id = ?
            GROUP BY coa.id
            ORDER BY coa.account_code
        ", [$companyId,$year,$year,$month,$companyId])->getResultArray();
    }

    public function trialBalance(int $companyId, int $month, int $year)
    {
        return db_connect()->query("
            SELECT 
                coa.account_code,
                coa.account_name,
                SUM(jd.debit) as total_debit,
                SUM(jd.credit) as total_credit
            FROM coa
            LEFT JOIN journal_details jd ON jd.account_id = coa.id
            LEFT JOIN journal_headers jh 
                ON jh.id = jd.journal_id
                AND jh.status='posted'
                AND jh.company_id=?
                AND jh.period_month=?
                AND jh.period_year=?
            WHERE coa.company_id=?
            GROUP BY coa.id
            ORDER BY coa.account_code
        ", [$companyId,$month,$year,$companyId])->getResultArray();
    }
}
