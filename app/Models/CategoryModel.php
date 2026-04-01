<?php

namespace App\Models;

class CategoryModel extends BaseModel
{

  protected $table = 'categories';

  protected $allowedFields = [
    'name',
    'icon',
    'status'
  ];

  public function getCategoriesWithTotalItems()
  {

    return $this->select('
        categories.id,
        categories.name,
        categories.icon,
        categories.status,
        COUNT(items.id) AS total_items
      ')
      ->join('items', 'items.category_id = categories.id', 'left')
      ->where('categories.status', 'active')
      ->groupBy('categories.id')
      ->findAll();

  }

}