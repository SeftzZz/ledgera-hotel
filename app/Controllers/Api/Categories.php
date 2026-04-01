<?php

namespace App\Controllers\Api;

use App\Models\CategoryModel;

class Categories extends BaseApiController
{

  protected $categoryModel;

  public function __construct()
  {
    $this->categoryModel = new CategoryModel();
  }

  /*
  ============================
  LIST CATEGORIES
  ============================
  */
  public function index()
  {

    $data = $this->categoryModel->getCategoriesWithTotalItems();

    return $this->success($data);

  }

  /*
  ============================
  CREATE CATEGORY
  ============================
  */
  public function create()
  {

    $data = $this->request->getRawInput();

    $name   = $data['name']   ?? null;
    $icon   = $data['icon']   ?? null;
    $status = $data['status'] ?? 'active';

    if (!$name) {
      return $this->error('Category name is required');
    }

    $insert = [
      'name'   => $name,
      'icon'   => $icon ?: null,
      'status' => $status
    ];

    $this->categoryModel->insert($insert);

    return $this->success([
      'id' => $this->categoryModel->getInsertID()
    ], 'Category created successfully');

  }

}