<?php

namespace App\Models;

class LoyaltyRuleModel extends BaseModel
{
    protected $table = 'loyalty_rules';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'name',
        'point_per_amount',
        'minimum_order',
        'status'
    ];
}