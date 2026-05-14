<?php

namespace App\Models;

use CodeIgniter\Model;

class TaxCodeModel extends Model
{
    protected $table = 'tax_codes';

    protected $primaryKey = 'id';

    protected $allowedFields = [
        'company_id',
        'tax_code',
        'tax_name',
        'tax_type',
        'tax_rate',
        'tax_direction',
        'coa_account_id',
        'is_included',
        'is_creditable',
        'is_active'
    ];
}