<?php

namespace App\Models;

use CodeIgniter\Model;

class OpeningBalanceModel extends Model
{
    protected $table = 'coa_opening_balances';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'company_id',
        'coa_id',
        'opening_balance',
        'period_year',
        'created_by',
        'updated_by'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}