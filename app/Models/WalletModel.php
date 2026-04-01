<?php

namespace App\Models;

class WalletModel extends BaseModel
{
    protected $table = 'wallets';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'balance'
    ];
}