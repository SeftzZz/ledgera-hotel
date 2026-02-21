<?php

namespace App\Controllers;

use App\Services\ReportService;

class ReportController extends BaseController
{
    public function index()
    {
        return view('report/index', [
            'title' => 'Financial Reports'
        ]);
    }

    public function profitLoss()
    {
        $companyId = session('company_id');
        $month = $this->request->getGet('month');
        $year  = $this->request->getGet('year');

        if (!$month || !$year) {
            return redirect()->back();
        }

        $rows = (new ReportService())->profitLoss($companyId, $month, $year);

        return view('report/profit_loss', [
            'title' => 'Profit & Loss',
            'month' => $month,
            'year'  => $year,
            'rows'  => $rows
        ]);
    }
}