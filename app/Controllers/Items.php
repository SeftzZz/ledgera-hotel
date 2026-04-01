<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Config\Database;

class Items extends BaseController
{

  public function index()
  {
    return view('ecommerce/item-list', [
      'title' => 'Items'
    ]);
  }

  public function itemAdd()
  {
    $db = Database::connect();

    $categories = $db->table('categories')
      ->where('status', 'active')
      ->get()
      ->getResultArray();

    $branches = $db->table('branches')
      ->get()
      ->getResultArray();

    // variants hot & ice
    $variants = [
      [
        'id'   => 1,
        'name' => 'hot'
      ],
      [
        'id'   => 2,
        'name' => 'ice'
      ]
    ];

    return view('ecommerce/item-add', [
      'title'      => 'Items Add',
      'categories' => $categories,
      'branches'   => $branches,
      'variants'   => $variants
    ]);
  }

  public function itemCategory()
  {
    return view('ecommerce/item-category', [
      'title' => 'Categories'
    ]);
  }
}