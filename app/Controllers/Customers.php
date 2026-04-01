<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use Config\Database;

class Customers extends BaseController
{

  public function index()
  {
    return view('ecommerce/customer-list');
  }

  public function detail($id)
  {
    return view('ecommerce/customer-detail', [
      'user_id' => $id
    ]);
  }
}