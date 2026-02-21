<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CoaModel;
use App\Models\OpeningBalanceModel;

class TrialBalanceController extends BaseController
{
    protected $db;
    protected $coaModel;
    protected $openingModel;

    public function __construct()
    {
        $this->db           = \Config\Database::connect();
        $this->coaModel     = new CoaModel();
        $this->openingModel = new OpeningBalanceModel();
    }

    public function index()
    {
        $companyId = session()->get('company_id') ?? 1;
        $year      = date('Y');

        $builder = $this->db->table('coa c');

        $builder->select("
            c.id,
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

        $trialData = [];
        $grandDebit = 0;
        $grandCredit = 0;

        foreach ($rows as $row) {

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

            $debit = 0;
            $credit = 0;

            if ($ending > 0) {
                if ($isDebitNormal) {
                    $debit = $ending;
                } else {
                    $credit = $ending;
                }
            } else {
                if ($isDebitNormal) {
                    $credit = abs($ending);
                } else {
                    $debit = abs($ending);
                }
            }

            $grandDebit += $debit;
            $grandCredit += $credit;

            $trialData[] = [
                'code' => $row['account_code'],
                'name' => $row['account_name'],
                'type' => $row['account_type'],
                'debit' => $debit,
                'credit' => $credit
            ];
        }

        return view('accounting/trial_balance/index', [
            'title' => 'Trial Balance',
            'data' => $trialData,
            'grandDebit' => $grandDebit,
            'grandCredit' => $grandCredit,
            'year' => $year
        ]);
    }
}