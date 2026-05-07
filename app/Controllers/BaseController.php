<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\CompanyModel;
/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Global data untuk semua view
     */
    protected array $globalData = [];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // ======================
        // LOAD GLOBAL DATA
        // ======================
        $this->loadGlobalData();
    }

    protected function loadGlobalData()
    {
        $companyModel = new CompanyModel();

        $this->globalData['companies'] = $companyModel->findAll();
    }

    /**
     * Helper render agar otomatis inject global data
     */
    protected function render(string $view, array $data = [])
    {
        return view($view, array_merge($this->globalData, $data));
    }
}
