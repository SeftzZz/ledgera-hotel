<?php

namespace App\Models;

class PointTransactionModel extends BaseModel
{
    protected $table = 'point_transactions';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'points',
        'type',
        'reference_type',
        'reference_id',
        'description'
    ];
}