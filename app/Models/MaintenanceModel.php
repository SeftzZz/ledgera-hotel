<?php

namespace App\Models;

use CodeIgniter\Model;

class MaintenanceModel extends Model
{
    protected $table = 'maintenance';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'room_id','company_id','branch_id','location','issue','description','status','started_at','completed_at',
        'created_at','created_by','updated_at','updated_by','deleted_at','deleted_by'
    ];
}
