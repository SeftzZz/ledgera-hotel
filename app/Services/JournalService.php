<?php

namespace App\Services;

use App\Models\JournalHeaderModel;
use App\Models\JournalDetailModel;
use App\Models\FiscalYearModel;
use App\Services\AccountingService;
use CodeIgniter\Database\BaseConnection;

class JournalService
{
    protected $db;
    protected $header;
    protected $detail;

    public function __construct()
    {
        $this->db     = db_connect();
        $this->header = new JournalHeaderModel();
        $this->detail = new JournalDetailModel();
    }

    public function create(array $data, array $lines): int
    {
        AccountingService::assertPeriodOpen(
            $data['company_id'],
            $data['period_month'],
            $data['period_year']
        );

        // resolve fiscal year
        $fy = (new FiscalYearModel())
            ->where('company_id', $data['company_id'])
            ->where("'{$data['journal_date']}' BETWEEN start_date AND end_date")
            ->first();

        $data['fiscal_year_id'] = $fy['id'] ?? null;
        $data['status'] = 'draft';

        $journalId = $this->header->insert($data, true);

        foreach ($lines as $line) {
            $line['journal_id'] = $journalId;
            $this->detail->insert($line);
        }

        return $journalId;
    }

    public function post(int $journalId): void
    {
        $this->db->transStart();

        // Lock row
        $journal = $this->db->query(
            "SELECT * FROM journal_headers WHERE id = ? FOR UPDATE",
            [$journalId]
        )->getRowArray();

        if (!$journal) {
            throw new \Exception('Journal not found');
        }

        if ($journal['status'] !== 'approved') {
            throw new \Exception('Journal not approved');
        }

        if ($journal['is_locked']) {
            throw new \Exception('Journal already posted');
        }

        AccountingService::assertPeriodOpen(
            $journal['company_id'],
            $journal['period_month'],
            $journal['period_year']
        );

        $this->header->update($journalId, [
            'status'    => 'posted',
            'is_locked' => 1
        ]);

        $this->db->transComplete();
    }
    
    public function reverse(int $journalId, string $reverseDate): int
    {
        $this->db->transStart();

        // Lock original journal
        $original = $this->db->query(
            "SELECT * FROM journal_headers WHERE id = ? FOR UPDATE",
            [$journalId]
        )->getRowArray();

        if (!$original) {
            throw new \Exception('Journal not found');
        }

        if ($original['status'] !== 'posted') {
            throw new \Exception('Only posted journal can be reversed');
        }

        if ($original['reversal_of'] !== null) {
            throw new \Exception('Journal is already a reversal entry');
        }

        // Check if already reversed
        $alreadyReversed = $this->header
            ->where('reversal_of', $journalId)
            ->first();

        if ($alreadyReversed) {
            throw new \Exception('Journal already reversed');
        }

        $reverseMonth = date('m', strtotime($reverseDate));
        $reverseYear  = date('Y', strtotime($reverseDate));

        AccountingService::assertPeriodOpen(
            $original['company_id'],
            $reverseMonth,
            $reverseYear
        );

        // Insert reversal header
        $newId = $this->header->insert([
            'company_id'   => $original['company_id'],
            'branch_id'    => $original['branch_id'],
            'journal_no'   => $original['journal_no'] . '-REV',
            'journal_date' => $reverseDate,
            'period_month' => $reverseMonth,
            'period_year'  => $reverseYear,
            'status'       => $original['status'],
            'is_locked'    => 1,
            'reversal_of'  => $journalId
        ], true);

        // Reverse details
        $details = $this->detail
            ->where('journal_id', $journalId)
            ->findAll();

        foreach ($details as $d) {
            $this->detail->insert([
                'journal_id' => $newId,
                'account_id' => $d['account_id'],
                'debit'      => $d['credit'],
                'credit'     => $d['debit']
            ]);
        }

        $this->db->transComplete();

        return $newId;
    }
}
