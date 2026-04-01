<?php

namespace App\Models;

class WalletTransactionModel extends BaseModel
{
    protected $table = 'wallet_transactions';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'wallet_id',
        'type',
        'amount',
        'reference_type',
        'reference_id',
        'description'
    ];
}