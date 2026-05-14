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

        // =========================
        // VALIDASI INPUT
        // =========================
        if (empty($trx['branch_name'])) {
            throw new \Exception('branch_name required');
        }

        // =========================
        // 🔥 RESOLVE BRANCH BY NAME
        // =========================
        $branch = $db->table('branches')
            ->where('LOWER(branch_name)', strtolower(trim($trx['branch_name'])))
            ->get()
            ->getRowArray();

        if (!$branch) {
            throw new \Exception("Branch tidak ditemukan: {$trx['branch_name']}");
        }

        $branchId = (int) $branch['id'];

        // 🔥 override
        $trx['branch_id'] = $branchId;
        // =========================
        // 🔥 RESOLVE CATEGORY (DEPARTMENT)
        // =========================
        $categoryId = null;

        if (!empty($trx['department'])) {

            $category = $db->table('categories')
                ->where('branch_id', $trx['branch_id'])
                ->where('LOWER(name)', strtolower(trim($trx['department'])))
                ->get()
                ->getRowArray();

            if (!$category) {
                throw new \Exception("Category tidak ditemukan: {$trx['department']}");
            }

            $categoryId = (int) $category['id'];
        }

        // inject ke trx
        $trx['category_id'] = $categoryId;
        
        // ==============================
        // VALIDATION
        // ==============================
        if (empty($trx['payment_account_id']) || $trx['payment_account_id'] == 0) {
            throw new \Exception('Payment account wajib diisi dan tidak boleh 0');
        }

        if (empty($trx['trx_type'])) {
            throw new \Exception('trx_type wajib diisi');
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

        // ==============================
        // BASE VARIABLES
        // ==============================
        $grossAmount = (float) ($trx['gross_amount'] ?? $trx['amount']);
        $baseAmount     = $grossAmount;
        $taxAmount      = 0;
        $serviceAmount  = 0;
        $tax            = null;

        // ==============================
        // TAX CALCULATION (SINGLE SOURCE)
        // ==============================
        if (!empty($trx['tax_code_id'])) {

            $tax = $taxModel->find($trx['tax_code_id']);

            if (!$tax) {
                throw new \Exception('Invalid tax selected.');
            }

            $rate = $tax['tax_rate'] / 100;

            // =========================
            // PPN
            // =========================
            if ($tax['tax_type'] === 'ppn') {

                if (($trx['tax_mode'] ?? 'exclusive') === 'inclusive') {
                    $baseAmount = $grossAmount / (1 + $rate);
                    $taxAmount  = $grossAmount - $baseAmount;
                } else {
                    $taxAmount  = $baseAmount * $rate;
                }
            }

            // =========================
            // PB1 (HOTEL)
            // =========================
            elseif ($tax['tax_type'] === 'pb1') {

                $serviceRate = 0.10;

                if (($trx['tax_mode'] ?? 'exclusive') === 'inclusive') {

                    $factor = 1 + $serviceRate + (1 + $serviceRate) * $rate;

                    $baseAmount     = $grossAmount / $factor;
                    $serviceAmount  = $baseAmount * $serviceRate;
                    $taxAmount      = ($baseAmount + $serviceAmount) * $rate;

                } else {

                    $serviceAmount  = $baseAmount * $serviceRate;
                    $taxAmount      = ($baseAmount + $serviceAmount) * $rate;
                }
            }

            // =========================
            // WITHHOLDING
            // =========================
            elseif ($tax['tax_type'] === 'withholding') {
                $taxAmount = $baseAmount * $rate;
            }

            // =========================
            // PLATFORM FEE
            // =========================
            elseif ($tax['tax_type'] === 'fee') {
                $taxAmount = $baseAmount * $rate;
            }

            // =========================
            // ROUNDING LOCK
            // =========================
            $baseAmount     = round($baseAmount, 2);
            $serviceAmount  = round($serviceAmount, 2);
            $taxAmount      = round($taxAmount, 2);

            // =========================
            // ANTI SELISIH
            // =========================
            if ($tax['tax_type'] === 'pb1') {
                
                $diff = $grossAmount - ($baseAmount + $serviceAmount + $taxAmount);
                $taxAmount += $diff;

            } elseif (in_array($tax['tax_type'], ['ppn'])) {

                $diff = $grossAmount - ($baseAmount + $taxAmount);
                $taxAmount += $diff;

            }
        }

        // ==============================
        // INSERT TRANSACTION
        // ==============================
        $insertAmount = $baseAmount;
        $journalLines = [];
        $type = strtolower($trx['trx_type']);
        
        // 🔥 KHUSUS SALES → pakai GROSS
        if (in_array($type, ['sales', 'sales_partial'])) {
            $insertAmount = $grossAmount;
        }

        $trxId = $trxModel->insert([
            ...$trx,
            'amount'       => $insertAmount,
            'gross_amount' => $grossAmount
        ], true);

        // ==============================
        // SAVE TAX
        // ==============================
        if ($taxAmount > 0 && $tax) {
            $db->table('transaction_taxes')->insert([
                'transaction_id' => $trxId,
                'tax_code_id'    => $tax['id'],
                'tax_base'       => $baseAmount,
                'tax_amount'     => $taxAmount
            ]);
        }

        // =====================================
        // EXPENSE
        // =====================================
        if (str_starts_with($type, 'expense')) {

            $journalLines[] = [
                'account_id' => $map['debit_account_id'],
                'debit'      => $baseAmount,
                'credit'     => 0
            ];

            $bankCredit = $baseAmount;

            if ($tax && $tax['tax_type'] === 'ppn') {

                $journalLines[] = [
                    'account_id' => $tax['coa_account_id'],
                    'debit'      => $taxAmount,
                    'credit'     => 0
                ];

                $bankCredit += $taxAmount;
            }

            if ($tax && $tax['tax_type'] === 'withholding') {

                $journalLines[] = [
                    'account_id' => $tax['coa_account_id'],
                    'debit'      => 0,
                    'credit'     => $taxAmount
                ];

                $bankCredit = $baseAmount - $taxAmount;
            }

            if ($tax && $tax['tax_type'] === 'fee') {

                $journalLines[] = [
                    'account_id' => $tax['coa_account_id'],
                    'debit'      => $taxAmount,
                    'credit'     => 0
                ];

                $bankCredit = $baseAmount + $taxAmount;
            }

            $journalLines[] = [
                'account_id' => $trx['payment_account_id'],
                'debit'      => 0,
                'credit'     => $bankCredit
            ];
        }

        // =====================================
        // EXPENSE PARTIAL
        // =====================================
        elseif (
            str_contains($type, '_partial') &&
            (str_starts_with($type, 'purchase') || str_starts_with($type, 'expense'))
        ) {

            $total = (float) $baseAmount;
            $paid  = (float) ($trx['paid_amount'] ?? 0);
            $hutang = $total - $paid;

            // =========================
            // DR INVENTORY / EXPENSE
            // =========================
            $journalLines[] = [
                'account_id' => $map['debit_account_id'],
                'debit'      => $total,
                'credit'     => 0
            ];

            // =========================
            // CR CASH (DP)
            // =========================
            if ($paid > 0) {
                $journalLines[] = [
                    'account_id' => $trx['payment_account_id'],
                    'debit'      => 0,
                    'credit'     => $paid
                ];
            }

            // =========================
            // CR HUTANG
            // =========================
            if ($hutang > 0) {
                $journalLines[] = [
                    'account_id' => $map['credit_account_id'], // Utang Usaha
                    'debit'      => 0,
                    'credit'     => $hutang
                ];
            }
        }

        // =====================================
        // SALES / REVENUE
        // =====================================
        elseif (
            $type === 'sales' ||
            str_starts_with($type, 'sales_') ||
            str_starts_with($type, 'revenue')
        ) {

            // =========================
            // PB1
            // =========================
            if ($tax && $tax['tax_type'] === 'pb1') {

                // VALIDASI SERVICE ACCOUNT
                if (empty($map['service_account_id'])) {
                    throw new \Exception('service_account_id belum diset untuk trx_type sales');
                }

                $journalLines[] = [
                    'account_id' => $trx['payment_account_id'],
                    'debit'      => $grossAmount,
                    'credit'     => 0
                ];

                // ROOM (main revenue)
                $journalLines[] = [
                    'account_id' => $map['credit_account_id'],
                    'debit'      => 0,
                    'credit'     => $baseAmount
                ];

                // SERVICE
                if ($serviceAmount > 0) {
                    $journalLines[] = [
                        'account_id' => $map['service_account_id'],
                        'debit'      => 0,
                        'credit'     => $serviceAmount
                    ];
                }

                // PB1
                $journalLines[] = [
                    'account_id' => $tax['coa_account_id'],
                    'debit'      => 0,
                    'credit'     => $taxAmount
                ];
            }

            // =========================
            // PPN
            // =========================
            elseif ($tax && $tax['tax_type'] === 'ppn') {

                $journalLines[] = [
                    'account_id' => $trx['payment_account_id'],
                    'debit'      => $baseAmount + $taxAmount,
                    'credit'     => 0
                ];

                $journalLines[] = [
                    'account_id' => $map['credit_account_id'],
                    'debit'      => 0,
                    'credit'     => $baseAmount
                ];

                $journalLines[] = [
                    'account_id' => $tax['coa_account_id'],
                    'debit'      => 0,
                    'credit'     => $taxAmount
                ];
            }

            // =========================
            // NO TAX
            // =========================
            else {

                $journalLines[] = [
                    'account_id' => $trx['payment_account_id'],
                    'debit'      => $baseAmount,
                    'credit'     => 0
                ];

                $journalLines[] = [
                    'account_id' => $map['credit_account_id'],
                    'debit'      => 0,
                    'credit'     => $baseAmount
                ];
            }
        }

        // =====================================
        // DEPOSIT REVENUE
        // =====================================
        elseif (
            $type === 'sales_partial' ||
            str_contains($type, '_partial')
        ) {

            // =========================
            // PB1 LOGIC
            // =========================
            if ($tax && $tax['tax_type'] === 'pb1') {

                if (empty($map['service_account_id'])) {
                    throw new \Exception('service_account_id belum diset');
                }

                $total = (float) ($trx['gross_amount'] ?? $trx['amount']);
                $paid  = (float) ($trx['paid_amount'] ?? 0);
                $piutang = $total - $paid;

                // =========================
                // DEBIT CASH
                // =========================
                if ($paid > 0) {
                    $journalLines[] = [
                        'account_id' => $trx['payment_account_id'],
                        'debit'      => $paid,
                        'credit'     => 0
                    ];
                }

                // =========================
                // DEBIT PIUTANG
                // =========================
                if ($piutang > 0) {
                    $journalLines[] = [
                        'account_id' => $map['debit_account_id'], // piutang
                        'debit'      => $piutang,
                        'credit'     => 0
                    ];
                }

                // =========================
                // CREDIT ROOM
                // =========================
                $journalLines[] = [
                    'account_id' => $map['credit_account_id'],
                    'debit'      => 0,
                    'credit'     => $baseAmount
                ];

                // =========================
                // CREDIT SERVICE
                // =========================
                if ($serviceAmount > 0) {
                    $journalLines[] = [
                        'account_id' => $map['service_account_id'],
                        'debit'      => 0,
                        'credit'     => $serviceAmount
                    ];
                }

                // =========================
                // CREDIT PB1
                // =========================
                $journalLines[] = [
                    'account_id' => $tax['coa_account_id'],
                    'debit'      => 0,
                    'credit'     => $taxAmount
                ];
            }

            // =========================
            // NON PB1
            // =========================
            else {

                $total = (float) ($trx['gross_amount'] ?? $trx['amount']);
                $paid  = (float) ($trx['paid_amount'] ?? 0);
                $piutang = $total - $paid;

                if ($paid > 0) {
                    $journalLines[] = [
                        'account_id' => $trx['payment_account_id'],
                        'debit'      => $paid,
                        'credit'     => 0
                    ];
                }

                if ($piutang > 0) {
                    $journalLines[] = [
                        'account_id' => $map['debit_account_id'],
                        'debit'      => $piutang,
                        'credit'     => 0
                    ];
                }

                $journalLines[] = [
                    'account_id' => $map['credit_account_id'],
                    'debit'      => 0,
                    'credit'     => $baseAmount
                ];

                if ($taxAmount > 0 && $tax) {
                    $journalLines[] = [
                        'account_id' => $tax['coa_account_id'],
                        'debit'      => 0,
                        'credit'     => $taxAmount
                    ];
                }
            }
        }

        // =====================================
        // RECEIVE PAYMENT
        // =====================================
        elseif ($type === 'receive_payment') {

            $amount = (float) $trx['amount'];

            $journalLines[] = [
                'account_id' => $trx['payment_account_id'], // Kas
                'debit'      => $amount,
                'credit'     => 0
            ];

            $journalLines[] = [
                'account_id' => $map['credit_account_id'], // Piutang
                'debit'      => 0,
                'credit'     => $amount
            ];
        }

        // =====================================
        // LOAN INSTALLMENT
        // =====================================
        elseif ($type === 'loan_installment') {

            $principal = (float) $trx['principal'];
            $interest  = (float) ($trx['interest'] ?? 0);
            $fee       = (float) ($trx['fee'] ?? 0);

            $total = $principal + $interest + $fee;

            // =========================
            // DR UTANG BANK
            // =========================
            $journalLines[] = [
                'account_id' => $map['debit_account_id'], // utang bank
                'debit'      => $principal,
                'credit'     => 0
            ];

            // =========================
            // DR BEBAN KREDIT
            // =========================
            if ($interest > 0) {
                $journalLines[] = [
                    'account_id' => $map['interest_account_id'],
                    'debit'      => $interest,
                    'credit'     => 0
                ];
            }

            // =========================
            // DR PLATFORM FEE
            // =========================
            if ($fee > 0) {
                $journalLines[] = [
                    'account_id' => $map['fee_account_id'],
                    'debit'      => $fee,
                    'credit'     => 0
                ];
            }

            // =========================
            // CR KAS
            // =========================
            $journalLines[] = [
                'account_id' => $trx['payment_account_id'],
                'debit'      => 0,
                'credit'     => $total
            ];
        }

        // =====================================
        // DEFAULT
        // =====================================
        else {

            if ($taxAmount > 0) {
                throw new \Exception("trx_type {$trx['trx_type']} tidak boleh menggunakan tax");
            }

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
            'journal_no'   => 'JRN-' . round(microtime(true) * 1000),
            'description'  => $trx['trx_type'] . '-' . $trx['reference_no'],
            'journal_date' => $trx['trx_date'],
            'period_month' => (int) date('m', strtotime($trx['trx_date'])),
            'period_year'  => (int) date('Y', strtotime($trx['trx_date']))
        ], $journalLines);

        $trxModel->update($trxId, [
            'journal_id' => $journalId
        ]);

        $this->emitWS('transaction_created', $trx['branch_id']);

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new \Exception('Transaction failed');
        }

        return $trxId;
    }
    
    private function emitWS($type, $branchId)
    {
        $payload = [
            'type' => $type,
            'branch_id' => $branchId
        ];

        $ch = curl_init('http://localhost:4003/emit');

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode($payload),
        ]);

        curl_exec($ch);
        curl_close($ch);
    }
}