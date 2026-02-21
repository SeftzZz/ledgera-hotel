<?php

namespace App\Services;

use App\Models\TransactionModel;
use App\Models\TransactionAccountMapModel;
use App\Services\JournalService;

class TransactionService
{
    public function create(array $trx): int
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $trxModel = new TransactionModel();
        $mapModel = new TransactionAccountMapModel();
        $journal  = new JournalService();

        $trxId = $trxModel->insert($trx, true);

        $map = $mapModel
            ->where('trx_type', $trx['trx_type'])
            ->first();

        if (!$map) {
            throw new \Exception(
                "Transaction account mapping not found for trx_type: {$trx['trx_type']}"
            );
        }

        $journalId = $journal->create([
            'company_id'    => $trx['company_id'],
            'branch_id'     => $trx['branch_id'],
            'journal_no'    => 'AUTO-' . $trxId,
            'journal_date'  => $trx['trx_date'],
            'period_month'  => (int) date('m', strtotime($trx['trx_date'])),
            'period_year'   => (int) date('Y', strtotime($trx['trx_date'])),
            'status'        => 'posted', // 🔥 tambahkan ini
        ], [
            [
                'account_id' => $map['debit_account_id'],
                'debit'      => $trx['amount'],
                'credit'     => 0
            ],
            [
                'account_id' => $map['credit_account_id'],
                'debit'      => 0,
                'credit'     => $trx['amount']
            ]
        ]);

        $trxModel->update($trxId, [
            'journal_id' => $journalId
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new \Exception('Transaction failed');
        }

        return $trxId;
    }

}
