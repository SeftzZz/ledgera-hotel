<?php

namespace App\Models;

use CodeIgniter\Model;

class BaseModel extends Model
{

    protected $returnType = 'array';
    protected $useTimestamps = false;

}