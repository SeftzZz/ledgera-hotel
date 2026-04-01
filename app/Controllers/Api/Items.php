<?php

namespace App\Controllers\Api;

use App\Controllers\Api\BaseApiController;
use App\Models\ItemModel;

class Items extends BaseApiController
{

    protected $itemModel;

    public function __construct()
    {
        $this->itemModel = new ItemModel();
    }

    /*
    |--------------------------------------------------------------------------
    | GET /api/v1/items
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $rows = $this->itemModel->getItems();

        $result = [];

        foreach ($rows as $row) {

            $itemId = $row['id'];
            $branch = $row['branch_name'];

            // =========================
            // INIT ITEM
            // =========================
            if (!isset($result[$itemId])) {
                $result[$itemId] = [
                    'id'            => $row['id'],
                    'name'          => $row['name'],
                    'category_id'   => $row['category_id'],
                    'category_name' => $row['category_name'],
                    'description'   => $row['description'],
                    'image'         => $row['image'],
                    'branches'      => []
                ];
            }

            // =========================
            // INIT BRANCH
            // =========================
            if (!isset($result[$itemId]['branches'][$branch])) {
                $result[$itemId]['branches'][$branch] = [
                    'branch_name' => $branch,
                    'price'       => $row['price'] ?? 0,
                    'stock'       => $row['stock'] ?? 0
                ];
            }
        }

        // =========================
        // CLEAN INDEX
        // =========================
        $final = [];

        foreach ($result as $item) {
            $item['branches'] = array_values($item['branches']);
            $final[] = $item;
        }

        return $this->success($final);
    }

    /*
    |--------------------------------------------------------------------------
    | GET /api/v1/items/category/{id}
    |--------------------------------------------------------------------------
    */

    public function category($categoryId = null)
    {
        if (!$categoryId) {
            return $this->error('Category ID required');
        }

        $items = $this->itemModel->getByCategory($categoryId);

        return $this->success($items);
    }

    /*
    |--------------------------------------------------------------------------
    | GET /api/v1/items/branch/{id}
    |--------------------------------------------------------------------------
    */

    public function branch($branchId = null)
    {
        if (!$branchId) {
            return $this->error('Branch ID required');
        }

        $rows = $this->itemModel->getByBranch($branchId);

        $result = [];

        foreach ($rows as $row) {

            $itemId = $row['id'];
            $branch = $row['branch_name'];

            // =========================
            // INIT ITEM
            // =========================
            if (!isset($result[$itemId])) {
                $result[$itemId] = [
                    'id'            => $row['id'],
                    'name'          => $row['name'],
                    'category_id'   => $row['category_id'],
                    'category_name' => $row['category_name'],
                    'description'   => $row['description'],
                    'image'         => $row['image'],
                    'status'        => $row['status'],
                    'branches'      => []
                ];
            }

            // =========================
            // INIT BRANCH
            // =========================
            if (!isset($result[$itemId]['branches'][$branch])) {
                $result[$itemId]['branches'][$branch] = [
                    'branch_name' => $branch,
                    'variants'    => []
                ];
            }

            // =========================
            // PUSH VARIANT
            // =========================
            $result[$itemId]['branches'][$branch]['variants'][] = [
                'name'  => $row['variant'],
                'price' => (float)$row['price'],
                'stock' => (int)$row['stock']
            ];
        }

        // =========================
        // RESET INDEX
        // =========================
        $final = [];

        foreach ($result as $item) {
            $item['branches'] = array_values($item['branches']);
            $final[] = $item;
        }

        return $this->success($final);
    }

    /*
    |--------------------------------------------------------------------------
    | GET /api/v1/items/branch/{branch}/category/{category}
    |--------------------------------------------------------------------------
    */

    public function branchCategory($branchId = null, $categoryId = null)
    {
        if (!$branchId || !$categoryId) {
            return $this->error('Branch ID and Category ID required');
        }

        $items = $this->itemModel->getBranchItemsByCategory($branchId, $categoryId);

        return $this->success($items);
    }

    public function create()
    {
        $db = \Config\Database::connect();

        $data = $this->request->getPost();

        $db->transStart();

        // =========================
        // INSERT ITEM
        // =========================
        $itemData = [
            'name'        => $data['name'],
            'category_id' => $data['category_id'],
            'description' => $data['description'] ?? null,
            'image'       => $data['image'] ?? null,
            'status'      => $data['status']
        ];

        $this->itemModel->insert($itemData);
        $itemId = $this->itemModel->getInsertID();

        // =========================
        // INSERT BRANCH ITEMS
        // =========================
        $branchIds = $data['branch_id'] ?? [];
        $prices    = $data['price'] ?? [];
        $stocks    = $data['stock'] ?? [];

        foreach ($branchIds as $i => $branchId) {

            if (!isset($prices[$i]) || $prices[$i] === '' || $prices[$i] <= 0) {
                continue;
            }

            $stock  = isset($stocks[$i]) ? (int)$stocks[$i] : 0;
            $status = $stock > 0 ? 'available' : 'out_of_stock';

            $db->table('branch_items')->insert([
                'branch_id' => $branchId,
                'item_id'   => $itemId, // 🔥 langsung ke item
                'price'     => $prices[$i],
                'stock'     => $stock,
                'status'    => $status
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->error('Failed to create product');
        }

        return $this->success([], 'Product created successfully');
    }

    public function uploadProduct()
    {
      $file = $this->request->getFile('image');

      $name = $file->getRandomName();

      $file->move('uploads/products', $name);

      return $this->response->setJSON([
        'path' => 'uploads/products/' . $name
      ]);
    }
}