<?php

namespace App\Models;

class CartModel extends BaseModel
{
    protected $table = 'carts';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'branch_id',
        'status'
    ];
}