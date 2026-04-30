<?php
	namespace App\Controllers\Api;

	use App\Controllers\BaseController;
	use App\Services\DashboardService;

	class BudgetController extends BaseController
	{
	    public function limit()
	    {
	        $service = new DashboardService();

	        $data = $service->getDashboardData([
	            'company_id'  => session('company_id'),
	            'branch_id'   => session('branch_id'),
	            'category_id' => session('category_id'),
	        ]);

	        return $this->response->setJSON([
	            'status' => 'success',
	            'data'   => $data['departmentSummary']
	        ]);
	    }
	}