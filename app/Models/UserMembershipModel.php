<?php

namespace App\Models;

class UserMembershipModel extends BaseModel
{
    protected $table = 'user_memberships';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'tier_id',
        'total_spending',
        'total_points',
        'start_date',
        'expire_date',
        'status'
    ];
}