<?php

namespace App\Models;

class VoucherModel extends BaseModel
{
    protected $table = 'vouchers';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'code',
        'discount_type',
        'discount_value',
        'max_usage',
        'used_count',
        'start_date',
        'end_date',
        'status'
    ];
}