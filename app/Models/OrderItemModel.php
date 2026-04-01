<?php

namespace App\Models;

class OrderItemModel extends BaseModel
{
    protected $table = 'order_items';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'order_id',
        'item_id',
        'quantity',
        'price',
        'created_at',
    ];
}