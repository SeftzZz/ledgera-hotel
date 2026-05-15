<?php

namespace App\Models;

class CategoryModel extends BaseModel
{
    protected $table = 'categories';

    protected $allowedFields = [
        'company_id',
        'branch_id',
        'name',
        'icon',
        'status'
    ];

    public function getCategoriesWithTotalItems()
    {
        // =========================================
        // GET BRANCH IDS BY COMPANY
        // =========================================
        $branchIds = db_connect()
            ->table('branches')
            ->select('id')
            ->where('company_id', session('company_id'))
            ->get()
            ->getResultArray();

        $branchIds = array_column($branchIds, 'id');

        // =========================================
        // IF NO BRANCH
        // =========================================
        if (empty($branchIds)) {
            return [];
        }

        // =========================================
        // GET CATEGORIES
        // =========================================
        return $this->select('
                categories.id,
                categories.name,
                categories.icon,
                categories.status,
                COUNT(items.id) AS total_items
            ')
            ->join(
                'items',
                'items.category_id = categories.id',
                'left'
            )
            ->whereIn('categories.branch_id', $branchIds)
            ->where('categories.status', 'active')
            ->groupBy('categories.id')
            ->findAll();
    }
}