<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Config\Database;

class Orders extends BaseController
{

  public function index()
  {
    return $this->render('ecommerce/order-list', [
      'title' => 'Order List'
    ]);
  }

  public function detail($id)
  {
    return $this->render('ecommerce/order-detail', [
      'title' => 'Order Detail',
      'order_id' => $id
    ]);
  }

  public function add()
  {
    $db = Database::connect();

    $orderNumber = 'ORD'.date('YmdHis').strtoupper(substr(uniqid(),-6));

    $users = $db->table('users')
        ->select('
            users.id,
            users.name,
            users.email,
            users.phone,
            users.photo,
            users.created_at,

            COUNT(orders.id) as total_orders,
            COALESCE(SUM(orders.total_amount),0) as total_spent
        ')
        ->join('orders','orders.user_id = users.id','left')
        ->groupBy('users.id')
        ->orderBy('users.id','DESC')
        ->get()
        ->getResultArray();

    // cast numeric values
    foreach ($users as &$u) {
        $u['total_orders'] = (int) $u['total_orders'];
        $u['total_spent']  = (float) $u['total_spent'];
    }

    return $this->render('ecommerce/order-add', [
      'title'        => 'Order Add',
      'order_number' => $orderNumber,
      'customers'    => $users
    ]);
  }
}