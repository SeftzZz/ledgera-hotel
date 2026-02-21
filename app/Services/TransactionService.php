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
        $taxModel = new \App\Models\TaxCodeModel();

        // ==============================
        // TAX CALCULATION
        // ==============================
        $baseAmount = (float) $trx['amount'];
        $taxAmount  = 0;
        $tax        = null;

        if (!empty($trx['tax_code_id'])) {

            $tax = $taxModel->find($trx['tax_code_id']);

            if (!$tax) {
                throw new \Exception('Invalid tax selected.');
            }

            if (($trx['tax_mode'] ?? 'exclusive') === 'inclusive') {
                $base = $baseAmount / (1 + ($tax['tax_rate'] / 100));
                $taxAmount = $baseAmount - $base;
                $baseAmount = $base;
            } else {
                $taxAmount = $baseAmount * ($tax['tax_rate'] / 100);
            }

            $taxAmount = round($taxAmount, 2);
        }

        // ==============================
        // INSERT TRANSACTION (BASE ONLY)
        // ==============================
        $trxId = $trxModel->insert([
            ...$trx,
            'amount' => $baseAmount
        ], true);

        // ==============================
        // SAVE TAX TO transaction_taxes
        // ==============================
        if ($taxAmount > 0 && $tax) {

            $db->table('transaction_taxes')->insert([
                'transaction_id' => $trxId,
                'tax_code_id'    => $tax['id'],
                'tax_base'       => $baseAmount,
                'tax_amount'     => $taxAmount
            ]);
        }

        // ==============================
        // ACCOUNT MAPPING
        // ==============================
        $map = $mapModel
            ->where('trx_type', $trx['trx_type'])
            ->first();

        if (!$map) {
            throw new \Exception("Mapping not found for trx_type: {$trx['trx_type']}");
        }

        if (empty($trx['payment_account_id'])) {
            throw new \Exception('Payment account not defined.');
        }

        $journalLines = [];

        // =====================================
        // EXPENSE
        // =====================================
        $type = strtolower($trx['trx_type']);

        if (str_starts_with($type, 'expense')) {

            // Debit Expense
            $journalLines[] = [
                'account_id' => $map['debit_account_id'],
                'debit'      => $baseAmount,
                'credit'     => 0
            ];

            // =========================
            // PPN INPUT
            // =========================
            if ($taxAmount > 0 && $tax && $tax['tax_type'] === 'ppn' && $tax['tax_direction'] === 'input') {

                $journalLines[] = [
                    'account_id' => $tax['coa_account_id'],
                    'debit'      => $taxAmount,
                    'credit'     => 0
                ];

                $bankCredit = $baseAmount + $taxAmount;
            }

            // =========================
            // WITHHOLDING (PPh21, PPh23)
            // =========================
            elseif ($taxAmount > 0 && $tax && $tax['tax_type'] === 'withholding') {

                // Credit Utang Pajak
                $journalLines[] = [
                    'account_id' => $tax['coa_account_id'],
                    'debit'      => 0,
                    'credit'     => $taxAmount
                ];

                $bankCredit = $baseAmount - $taxAmount;
            }

            else {
                $bankCredit = $baseAmount;
            }

            // Credit Bank
            $journalLines[] = [
                'account_id' => $trx['payment_account_id'],
                'debit'      => 0,
                'credit'     => $bankCredit
            ];
        }
        elseif (str_starts_with($type, 'revenue')) {

            // Debit Bank total
            $journalLines[] = [
                'account_id' => $trx['payment_account_id'],
                'debit'      => $baseAmount + $taxAmount,
                'credit'     => 0
            ];

            // Credit Revenue
            $journalLines[] = [
                'account_id' => $map['debit_account_id'],
                'debit'      => 0,
                'credit'     => $baseAmount
            ];

            // Credit PPN Keluaran
            if ($taxAmount > 0 && $tax && $tax['tax_type'] === 'ppn' && $tax['tax_direction'] === 'output') {
                $journalLines[] = [
                    'account_id' => $tax['coa_account_id'],
                    'debit'      => 0,
                    'credit'     => $taxAmount
                ];
            }
        }
        // =====================================
        // WITHHOLDING TAX (PPh23 dll)
        // =====================================
        elseif ($taxAmount > 0 && $tax && $tax['tax_type'] === 'withholding') {

            // Debit Expense
            $journalLines[] = [
                'account_id' => $map['debit_account_id'],
                'debit'      => $baseAmount,
                'credit'     => 0
            ];

            // Credit Utang Pajak
            $journalLines[] = [
                'account_id' => $tax['coa_account_id'],
                'debit'      => 0,
                'credit'     => $taxAmount
            ];

            // Credit Bank (net dibayar)
            $journalLines[] = [
                'account_id' => $trx['payment_account_id'],
                'debit'      => 0,
                'credit'     => $baseAmount - $taxAmount
            ];
        }

        // =====================================
        // DEFAULT (capital, draw, dll)
        // =====================================
        else {

            $journalLines[] = [
                'account_id' => $map['debit_account_id'],
                'debit'      => $baseAmount,
                'credit'     => 0
            ];

            $journalLines[] = [
                'account_id' => $trx['payment_account_id'],
                'debit'      => 0,
                'credit'     => $baseAmount
            ];
        }
        // ==============================
        // CREATE JOURNAL
        // ==============================
        $journalId = $journal->create([
            'company_id'   => $trx['company_id'],
            'branch_id'    => $trx['branch_id'],
            'journal_no'   => 'AUTO-' . $trxId,
            'journal_date' => $trx['trx_date'],
            'period_month' => (int) date('m', strtotime($trx['trx_date'])),
            'period_year'  => (int) date('Y', strtotime($trx['trx_date']))
        ], $journalLines);

        $trxModel->update($trxId, [
            'journal_id' => $journalId
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new \Exception('Transaction failed');
        }

        return $trxId;
    }
    
    public function applyTax($amount, $taxId, $transactionType)
    {
        $tax = $this->taxModel->find($taxId);

        $taxAmount = $amount * ($tax->tax_rate / 100);

        if ($tax->tax_type === 'ppn') {

            if ($tax->tax_direction === 'output') {
                $this->journal->credit($tax->coa_account_id, $taxAmount);
            }

            if ($tax->tax_direction === 'input') {
                $this->journal->debit($tax->coa_account_id, $taxAmount);
            }
        }

        if ($tax->tax_type === 'withholding') {
            $this->journal->credit($tax->coa_account_id, $taxAmount);
        }

        return $taxAmount;
    }
}