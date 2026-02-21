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

        // ==============================
        // INSERT TRANSACTION
        // ==============================
        $trxId = $trxModel->insert($trx, true);

        // ==============================
        // GET ACCOUNT MAPPING
        // ==============================
        $map = $mapModel
            ->where('trx_type', $trx['trx_type'])
            ->first();

        if (!$map) {
            throw new \Exception(
                "Transaction account mapping not found for trx_type: {$trx['trx_type']}"
            );
        }

        // ==============================
        // DETERMINE ACCOUNTS
        // ==============================
        $debitAccountId  = $map['debit_account_id'];

        // Jika ada payment_account_id → pakai itu
        // Kalau tidak → fallback ke mapping default
        $creditAccountId = $trx['payment_account_id']
            ?? $map['credit_account_id'];

        if (!$creditAccountId) {
            throw new \Exception('Payment account not defined.');
        }

        // ==============================
        // CREATE JOURNAL (DRAFT)
        // ==============================
        $journalId = $journal->create([
            'company_id'   => $trx['company_id'],
            'branch_id'    => $trx['branch_id'],
            'journal_no'   => 'AUTO-' . $trxId,
            'journal_date' => $trx['trx_date'],
            'period_month' => (int) date('m', strtotime($trx['trx_date'])),
            'period_year'  => (int) date('Y', strtotime($trx['trx_date']))
        ], [
            [
                'account_id' => $debitAccountId,
                'debit'      => $trx['amount'],
                'credit'     => 0
            ],
            [
                'account_id' => $creditAccountId,
                'debit'      => 0,
                'credit'     => $trx['amount']
            ]
        ]);

        // ==============================
        // LINK JOURNAL TO TRANSACTION
        // ==============================
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