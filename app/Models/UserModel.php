<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'name','company_id','branch_id','category_id','role','email','phone','password','photo','is_active','last_login_at','created_at',
        'created_by','updated_at','updated_by','deleted_at','deleted_by'
    ];

    protected $useTimestamps = true;
}
