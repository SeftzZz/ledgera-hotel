<?php

namespace App\Models;

class MembershipHistoryModel extends BaseModel
{
    protected $table = 'membership_history';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'old_tier_id',
        'new_tier_id',
        'reason'
    ];
}