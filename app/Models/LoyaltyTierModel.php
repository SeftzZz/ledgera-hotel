<?php

namespace App\Models;

class LoyaltyTierModel extends BaseModel
{
    protected $table = 'loyalty_tiers';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'name',
        'min_points',
        'min_spending',
        'cashback_percent',
        'point_multiplier',
        'free_drink_per_month'
    ];
}