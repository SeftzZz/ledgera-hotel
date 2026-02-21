<?php

namespace App\Services;

use App\Services\JournalService;

class ApprovalService
{
    protected $db;

    public function __construct()
    {
        $this->db = db_connect();
    }

    // ==========================================
    // INIT APPROVAL
    // ==========================================
    public function init(string $module, int $journalId, float $amount): void
    {
        $this->db->transStart();

        $journal = $this->db->table('journal_headers')
            ->where('id', $journalId)
            ->get()
            ->getRow();

        if (!$journal) {
            throw new \Exception('Journal not found');
        }

        if ($journal->status !== 'draft') {
            throw new \Exception('Journal already submitted');
        }

        $flow = $this->db->table('approval_flows')
            ->where('module', $module)
            ->where('is_active', 1)
            ->get()
            ->getRow();

        if (!$flow) {
            $this->autoApprove($module, $journalId);
            $this->db->transComplete();
            return;
        }

        $rule = $this->db->table('approval_rules')
            ->where('approval_flow_id', $flow->id)
            ->where('min_amount <=', $amount)
            ->where("(max_amount IS NULL OR max_amount >= {$amount})")
            ->orderBy('min_amount', 'DESC')
            ->get()
            ->getRow();

        if (!$rule || $rule->auto_approve) {
            $this->autoApprove($module, $journalId);
            $this->db->transComplete();
            return;
        }

        $steps = $this->db->table('approval_steps')
            ->where('approval_rule_id', $rule->id)
            ->orderBy('step_order')
            ->get()
            ->getResult();

        if (!$steps) {
            throw new \Exception('Approval steps not configured');
        }

        foreach ($steps as $step) {
            $this->db->table('approval_logs')->insert([
                'module'     => $module,
                'journal_id' => $journalId,
                'step_order' => $step->step_order,
                'role_id'    => $step->role_id,
                'status'     => 'pending'
            ]);
        }

        $this->db->table('journal_headers')
            ->where('id', $journalId)
            ->update(['status' => 'waiting']);

        $this->db->transComplete();
    }

    // ==========================================
    // APPROVE STEP
    // ==========================================
    public function approve(string $module, int $journalId, int $userId): void
    {
        $this->db->transStart();

        // Lock journal row
        $journal = $this->db->query(
            "SELECT * FROM journal_headers WHERE id = ? FOR UPDATE",
            [$journalId]
        )->getRow();

        if (!$journal) {
            throw new \Exception('Journal not found');
        }

        if ($journal->status !== 'waiting') {
            throw new \Exception('Journal not in waiting status');
        }

        // Ambil step paling kecil yg masih pending
        $step = $this->db->table('approval_logs')
            ->where('module', $module)
            ->where('journal_id', $journalId)
            ->where('status', 'pending')
            ->orderBy('step_order', 'ASC')
            ->get()
            ->getRow();

        if (!$step) {
            throw new \Exception('No pending approval step');
        }

        if (!userHasRole($userId, $step->role_id)) {
            throw new \Exception('Not authorized');
        }

        // Approve current step
        $this->db->table('approval_logs')
            ->where('id', $step->id)
            ->update([
                'status'      => 'approved',
                'approved_by' => $userId,
                'approved_at' => date('Y-m-d H:i:s')
            ]);

        // Cek apakah masih ada pending step
        $remaining = $this->db->table('approval_logs')
            ->where('journal_id', $journalId)
            ->where('status', 'pending')
            ->countAllResults();

        if ($remaining == 0) {

            $this->db->table('journal_headers')
                ->where('id', $journalId)
                ->update(['status' => 'approved']);

            (new JournalService())->post($journalId);
        }

        $this->db->transComplete();
    }

    // ==========================================
    // REJECT
    // ==========================================
    public function reject(string $module, int $journalId, int $userId, string $reason): void
    {
        $this->db->transStart();

        $step = $this->db->table('approval_logs')
            ->where('module', $module)
            ->where('journal_id', $journalId)
            ->where('status', 'pending')
            ->orderBy('step_order')
            ->get()
            ->getRow();

        if (!$step) {
            throw new \Exception('No pending step to reject');
        }

        if (!userHasRole($userId, $step->role_id)) {
            throw new \Exception('Not authorized');
        }

        $this->db->table('approval_logs')
            ->where('id', $step->id)
            ->update([
                'status'      => 'rejected',
                'note'        => $reason,
                'approved_by' => $userId,
                'approved_at' => date('Y-m-d H:i:s')
            ]);

        $this->db->table('journal_headers')
            ->where('id', $journalId)
            ->update(['status' => 'rejected']);

        $this->db->transComplete();
    }

    // ==========================================
    // AUTO APPROVE
    // ==========================================
    private function autoApprove(string $module, int $journalId): void
    {
        $this->db->table('approval_logs')->insert([
            'module'     => $module,
            'journal_id' => $journalId,
            'status'     => 'approved',
            'note'       => 'Auto approved',
            'approved_at'=> date('Y-m-d H:i:s')
        ]);

        $this->db->table('journal_headers')
            ->where('id', $journalId)
            ->update(['status' => 'approved']);

        (new JournalService())->post($journalId);
    }
}