<?php

namespace App\Services;

use App\Models\JournalHeaderModel;
use App\Models\JournalDetailModel;
use App\Models\RetainedEarningsModel;
use App\Models\AccountingPeriodModel;

class ClosingService
{
    public function close(int $companyId, int $month, int $year): void
    {
        $db = db_connect();
        $db->transStart();

        $profit = $db->query("
            SELECT
            SUM(CASE WHEN coa.account_type='revenue' THEN jd.credit-jd.debit ELSE 0 END) -
            SUM(CASE WHEN coa.account_type IN('expense','cogs') THEN jd.debit-jd.credit ELSE 0 END) AS profit
            FROM journal_details jd
            JOIN journal_headers jh ON jh.id=jd.journal_id
            JOIN coa ON coa.id=jd.account_id
            WHERE jh.company_id=? 
            AND jh.period_month=? 
            AND jh.period_year=? 
            AND jh.status='posted'
        ",[$companyId,$month,$year])->getRow()->profit;

        // create closing journal
        $journalId = $db->table('journal_headers')->insert([
            'company_id'=>$companyId,
            'journal_no'=>"CLOSE-$month-$year",
            'journal_date'=>date('Y-m-t',strtotime("$year-$month-01")),
            'period_month'=>$month,
            'period_year'=>$year,
            'status'=>'posted',
            'description'=>'Auto Closing Journal'
        ],true);

        // laba tahun berjalan id = 25
        $db->table('journal_details')->insert([
            'journal_id'=>$journalId,
            'account_id'=>25,
            'debit'=>$profit < 0 ? abs($profit) : 0,
            'credit'=>$profit > 0 ? $profit : 0
        ]);

        // lock period
        $db->table('accounting_periods')
            ->where([
                'company_id'=>$companyId,
                'period_month'=>$month,
                'period_year'=>$year
            ])
            ->update(['is_closed'=>1]);

        $db->transComplete();
    }

    public function carryForward(int $companyId, int $prevYear)
    {
        $db = db_connect();

        $profit = $db->query("
            SELECT SUM(amount) as total
            FROM retained_earnings
            WHERE company_id=? AND period_year=?
        ",[$companyId,$prevYear])->getRow()->total;

        // pindahkan ke laba ditahan (account 3200)
    }
}
