<?php

namespace App\Models;

use CodeIgniter\Model;

class VendorModel extends Model
{
    protected $table = 'vendors';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'company_id',
        'name',
        'kode',
        'no_po',
        'pic',
        'phone',
        'address',
        'status',
        'is_delete',
        'created_at',
        'updated_at'
    ];
}