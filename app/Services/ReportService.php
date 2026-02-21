<?php

namespace App\Services;

class ReportService
{
    public function profitLoss(int $companyId, int $month, int $year)
    {
        return db_connect()->query("
            SELECT
                coa.account_code,
                coa.account_name,
                coa.account_type,

                CASE
                    WHEN coa.account_type = 'revenue'
                        THEN COALESCE(SUM(jd.credit),0) - COALESCE(SUM(jd.debit),0)
                    ELSE
                        COALESCE(SUM(jd.debit),0) - COALESCE(SUM(jd.credit),0)
                END AS balance

            FROM coa
            LEFT JOIN journal_details jd 
                ON jd.account_id = coa.id
            LEFT JOIN journal_headers jh 
                ON jh.id = jd.journal_id
                AND jh.company_id = ?
                AND jh.period_month = ?
                AND jh.period_year = ?
                AND jh.status = 'posted'

            WHERE coa.company_id = ?
              AND coa.parent_id IS NOT NULL
              AND coa.account_type IN ('revenue','expense','cogs')

            GROUP BY coa.id
            ORDER BY coa.account_code
        ", [$companyId, $month, $year, $companyId])->getResultArray();
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
