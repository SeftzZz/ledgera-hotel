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
        'rate',
        'is_active'
    ];
}
