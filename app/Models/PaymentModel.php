<?php

namespace App\Models;

class PaymentModel extends BaseModel
{
    protected $table = 'payments';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'order_id',
        'payment_method',
        'amount',
        'status',
        'transaction_ref',
        'paid_at'
    ];
}