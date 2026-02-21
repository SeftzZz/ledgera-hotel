<?php

namespace App\Services;

use App\Models\TaxCodeModel;
use App\Services\JournalService;

class TaxEngineService
{
    protected $db;
    protected $taxModel;
    protected $journal;

    public function __construct()
    {
        $this->db = db_connect();
        $this->taxModel = new \App\Models\TaxCodeModel();
        $this->journal = new \App\Services\JournalService();
    }

    public function apply(int $transactionId, float $baseAmount, int $taxCodeId, string $transactionType)
    {
        $tax = $this->taxModel->find($taxCodeId);

        if (!$tax) return 0;

        $taxAmount = round($baseAmount * ($tax['tax_rate'] / 100), 2);

        // Simpan detail pajak
        $this->db->table('transaction_taxes')->insert([
            'transaction_id' => $transactionId,
            'tax_code_id'    => $taxCodeId,
            'tax_base'       => $baseAmount,
            'tax_amount'     => $taxAmount
        ]);

        // ===== GENERATE JOURNAL =====

        if ($tax['tax_type'] === 'ppn') {

            if ($tax['tax_direction'] === 'output') {
                // PPN Keluaran → Liability
                $this->journal->credit($tax['coa_account_id'], $taxAmount);
            }

            if ($tax['tax_direction'] === 'input') {
                // PPN Masukan → Asset
                $this->journal->debit($tax['coa_account_id'], $taxAmount);
            }
        }

        if ($tax['tax_type'] === 'withholding') {
            // PPh 23 dll → selalu credit (hutang pajak)
            $this->journal->credit($tax['coa_account_id'], $taxAmount);
        }

        return $taxAmount;
    }
}