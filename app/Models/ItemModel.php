<?php

namespace App\Models;

use CodeIgniter\Model;

class ItemModel extends Model
{

    protected $table      = 'items';
    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = [
        'category_id',
        'name',
        'description',
        'image',
        'price',
        'status'
    ];

    /*
    |--------------------------------------------------------------------------
    | Get All Items + Category
    |--------------------------------------------------------------------------
    */

    public function getItems()
    {
        $branchId = session('branch_id');

        return $this->db->table('branch_items')
            ->select('
                items.id,
                items.category_id,
                items.name,
                items.description,
                items.image,
                branches.branch_name,
                branch_items.price,
                branch_items.stock,
                items.status,
                categories.name as category_name
            ')
            ->join('items', 'items.id = branch_items.item_id', 'left')
            ->join('branches', 'branches.id = branch_items.branch_id', 'left')
            ->join('categories', 'categories.id = items.category_id', 'left')

            ->where('items.status', 'available')
            ->where('branch_items.branch_id', $branchId) // 🔥 INI YANG DITAMBAH

            ->orderBy('items.id', 'ASC')
            ->get()
            ->getResultArray();
    }

    /*
    |--------------------------------------------------------------------------
    | Items by Category
    |--------------------------------------------------------------------------
    */

    public function getByCategory($categoryId)
    {

        return $this->where('category_id', $categoryId)
            ->where('status', 'available')
            ->findAll();

    }

    /*
    |--------------------------------------------------------------------------
    | Items by Branch
    |--------------------------------------------------------------------------
    */

    public function getByBranch($branchId)
    {
      return $this->db->table('branch_items')
            ->select('
                items.id,
                items.category_id,
                items.name,
                items.description,
                items.image,
                variants.name as variant,
                branches.branch_name,
                branch_items.price,
                branch_items.stock,
                items.status,
                categories.name as category_name
            ')
            ->join('variants', 'variants.id = branch_items.variant_id')
            ->join('items', 'items.id = variants.item_id')
            ->join('branches', 'branches.id = branch_items.branch_id')
            ->join('categories', 'categories.id = items.category_id')
            ->where('items.status', 'available')
            ->where('branch_items.branch_id', $branchId)
            ->orderBy('items.id', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getBranchItemsByCategory($branchId, $categoryId)
    {
      return $this->db->table('branch_items')
        ->select('
          items.*,
          branch_items.price,
          branch_items.stock
        ')
        ->join('items', 'items.id = branch_items.item_id', 'left')
        ->where('branch_items.branch_id', $branchId)
        ->where('items.category_id', $categoryId)
        ->where('branch_items.status', 'available')
        ->get()
        ->getResultArray();
    }
}