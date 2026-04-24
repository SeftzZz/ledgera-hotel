<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table = 'transactions';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'company_id',
        'branch_id',
        'trx_date',
        'trx_type',
        'reference_no',
        'amount',
        'journal_id',
        'debit_account_id',
        'credit_account_id',
        'payment_account_id',
        'category_id',
        'status'
    ];

    protected $useTimestamps = true;

}
