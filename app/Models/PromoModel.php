<?php

namespace App\Models;

class PromoModel extends BaseModel
{
    protected $table = 'promos';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'title',
        'description',
        'discount_type',
        'discount_value',
        'min_purchase',
        'start_date',
        'end_date',
        'status'
    ];
}