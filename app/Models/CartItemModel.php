<?php

namespace App\Models;

class CartItemModel extends BaseModel
{
    protected $table = 'cart_items';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'cart_id',
        'item_id',
        'quantity',
        'price'
    ];
}