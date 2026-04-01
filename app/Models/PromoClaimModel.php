<?php

namespace App\Models;

class PromoClaimModel extends BaseModel
{
    protected $table = 'promo_claims';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'promo_id',
        'user_id',
        'used'
    ];
}