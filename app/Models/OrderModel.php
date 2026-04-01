<?php

namespace App\Models;

class OrderModel extends BaseModel
{
    protected $table = 'orders';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'order_number',
        'user_id',
        'branch_id',
        'cart_id',
        'subtotal',
        'discount',
        'wallet_used',
        'deposit',
        'total_amount',
        'status'
    ];
}