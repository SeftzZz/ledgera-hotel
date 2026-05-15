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
          return $this->error('Department name is required');
      }

      // =========================================
      // GET BRANCH IDS BY COMPANY
      // =========================================
      $branches = db_connect()
          ->table('branches')
          ->select('id, company_id')
          ->where('company_id', session('company_id'))
          ->get()
          ->getResultArray();

      // =========================================
      // IF NO BRANCH
      // =========================================
      if (empty($branches)) {

          return $this->error('No branches found');
      }

      $insertedIds = [];

      // =========================================
      // INSERT DEPARTMENT TO EACH BRANCH
      // =========================================
      foreach ($branches as $branch) {
          $insert = [
              'company_id'=> (int)$branch['company_id'],
              'branch_id' => (int)$branch['id'],
              'name'      => $name,
              'icon'      => $icon ?: null,
              'status'    => $status
          ];

          $this->categoryModel->insert($insert);

          $insertedIds[] = [
              'branch_id'   => $branch['id'],
              'category_id' => $this->categoryModel->getInsertID()
          ];
      }

      return $this->success([
          'data' => $insertedIds
      ], 'Category created successfully');
  }

}