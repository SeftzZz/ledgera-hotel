<?php

namespace App\Controllers;

use App\Services\DashboardService;

class DashboardController extends BaseController
{
    public function index()
    {
        $service = new DashboardService();

        $data = $service->getDashboardData([
            'company_id'  => session('company_id'),
            'branch_id'   => session('branch_id'),
            'category_id' => session('category_id'),
        ]);

        return view('dashboard/index', $data);
    }

    public function data()
    {
        $service = new \App\Services\DashboardService();

        $data = $service->getDashboardData([
            'company_id'  => session('company_id'),
            'branch_id'   => session('branch_id'),
            'category_id' => session('category_id'),
        ]);

        return $this->response->setJSON($data);
    }
}