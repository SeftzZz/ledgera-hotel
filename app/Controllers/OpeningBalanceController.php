<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\OpeningBalanceModel;
use App\Models\CoaModel;

class OpeningBalanceController extends BaseController
{
    protected $openingModel;
    protected $coaModel;
    protected $db;

    public function __construct()
    {
        $this->openingModel = new OpeningBalanceModel();
        $this->coaModel     = new CoaModel();
        $this->db           = \Config\Database::connect();
    }

    public function index()
    {
        $companyId = session()->get('company_id') ?? 1;
        $year      = date('Y');

        $accounts = $this->coaModel
            ->where('company_id', $companyId)
            ->where('parent_id IS NOT NULL')
            ->where('is_active', 1)
            ->orderBy('account_code', 'ASC')
            ->findAll();

        $existing = $this->openingModel
            ->where('company_id', $companyId)
            ->where('period_year', $year)
            ->findAll();

        $openingData = [];
        foreach ($existing as $row) {
            $openingData[$row['coa_id']] = $row['opening_balance'];
        }

        return view('accounting/opening_balance/index', [
            'title'         => 'Opening Balance',
            'accounts'      => $accounts,
            'year'          => $year,
            'openingData'   => $openingData
        ]);
    }

    public function save()
    {
        $companyId = session()->get('company_id') ?? 1;
        $year      = $this->request->getPost('year');
        $accounts  = $this->request->getPost('accounts');

        if (!$accounts) {
            return redirect()->back()->with('error', 'No data submitted.');
        }

        $this->db->transStart();

        $totalDebit  = 0;
        $totalCredit = 0;

        foreach ($accounts as $coaId => $amount) {

            $amount = str_replace(',', '', $amount);
            $amount = (float)$amount;

            if ($amount == 0) continue;

            $account = $this->coaModel->find($coaId);

            if (!$account) continue;

            if (in_array($account['account_type'], ['asset', 'expense'])) {
                $totalDebit += $amount;
            } else {
                $totalCredit += $amount;
            }

            $existing = $this->openingModel
                ->where('company_id', $companyId)
                ->where('coa_id', $coaId)
                ->where('period_year', $year)
                ->first();

            if ($existing) {
                $this->openingModel->update($existing['id'], [
                    'opening_balance' => $amount,
                    'updated_by'      => session()->get('user_id')
                ]);
            } else {
                $this->openingModel->insert([
                    'company_id'      => $companyId,
                    'coa_id'          => $coaId,
                    'opening_balance' => $amount,
                    'period_year'     => $year,
                    'created_by'      => session()->get('user_id')
                ]);
            }
        }

        if ($totalDebit != $totalCredit) {
            $this->db->transRollback();
            return redirect()->back()->with('error', 
                "Opening balance tidak balance! Debit: {$totalDebit} | Credit: {$totalCredit}"
            );
        }

        $this->db->transCommit();

        return redirect()->to('opening-balance')
            ->with('success', 'Opening balance saved successfully.');
    }
}