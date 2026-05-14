<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// =========================
// GLOBAL
// =========================
$routes->options('(:any)', fn () => response()->setStatusCode(200));

$routes->get('/', 'Home::index');

$routes->post('ping', fn () =>
    response()->setJSON([
        'ok' => true,
        'method' => service('request')->getMethod(),
    ])
);



// =========================
// AUTH (WEB)
// =========================
$routes->get('login', 'Auth\Login::index');
$routes->post('login', 'Auth\Login::auth');
$routes->get('logout', 'Auth\Login::logout');


// DASHBOARD (WEB)
$routes->get('dashboard', 'DashboardController::index', ['filter' => 'auth']);
$routes->get('dashboard/data', 'DashboardController::data', ['filter' => 'auth']);
$routes->get('department-expense', 'DashboardController::department_expense', ['filter' => 'auth']);

// =========================
// MASTER DATA
// =========================
$routes->group('', ['filter' => 'auth'], function ($routes) {

    // COMPANY
    $routes->group('company', function ($routes) {
        $routes->get('/', 'CompanyController::index');
        $routes->post('datatable', 'CompanyController::datatable');
        $routes->post('store', 'CompanyController::store');
        $routes->post('loan', 'CompanyController::loan');
    });

    // BRANCH
    $routes->group('branch', function ($routes) {
        $routes->get('/', 'BranchController::index');
        $routes->post('datatable', 'BranchController::datatable');
        $routes->post('store', 'BranchController::store');
        $routes->get('ratio/(:num)', 'BranchController::ratio/$1');
        $routes->get('(:num)/ratio/(:num)', 'BranchController::ratio/$1/$2');
    });

    // PARTNER
    $routes->group('partner', ['filter' => 'auth'], function ($routes) {
        $routes->get('/', 'BusinessPartnerController::index');
        $routes->post('datatable', 'BusinessPartnerController::datatable');
        $routes->post('store', 'BusinessPartnerController::store');
        $routes->get('detail/(:num)', 'BusinessPartnerController::detail/$1');
    });

    // TAX CODE
    $routes->group('tax', ['filter' => 'auth'], function ($routes) {
        $routes->get('/', 'TaxController::index');
        $routes->post('datatable', 'TaxController::datatable');
        $routes->post('store', 'TaxController::store');
    });

    $routes->post('switch-company', 'DashboardController::switchCompany');
});

// =========================
// ECOMMERCE
// =========================
$routes->get('api/branches','Api\Branches::index');
$routes->get('api/items/branch/(:num)','Api\Items::branch/$1');

$routes->get('api/promos','Api\Promos::index');
$routes->get('api/categories','Api\Categories::index');
$routes->get('api/items','Api\Items::index');

$routes->group('items', ['filter' => 'auth'], function($routes) {

    $routes->get('/', 'Items::index');
    $routes->get('item-add', 'Items::itemAdd');
    $routes->get('item-category', 'Items::itemCategory');

});

$routes->group('orders', ['filter' => 'auth'], function($routes) {

    $routes->get('/', 'Orders::index');
    $routes->get('detail/(:num)', 'Orders::detail/$1');
    $routes->get('order-add', 'Orders::add');

});

$routes->group('customers', ['filter' => 'auth'], function($routes) {

    $routes->get('/', 'Customers::index');
    $routes->get('detail/(:num)', 'Customers::detail/$1');

});

$routes->group('chats', ['filter' => 'auth'], function($routes) {

    $routes->get('/', 'Chats::index');
    $routes->get('detail/(:num)', 'Chats::detail/$1');

});

// PREVENTIVE & MAINTENANCE
$routes->group('maintenance', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'MaintenanceController::index');
    $routes->post('datatable', 'MaintenanceController::datatable');
    $routes->post('store', 'MaintenanceController::store');
    $routes->post('get', 'MaintenanceController::getById');
    $routes->get('get-inventori', 'MaintenanceController::getInventori');
    $routes->post('update', 'MaintenanceController::update');
    $routes->post('delete', 'MaintenanceController::delete');
    $routes->post('get-detail', 'MaintenanceController::getDetail');
    
    $routes->get('rooms', 'MaintenanceController::rooms');
    $routes->post('datatableroom', 'MaintenanceController::datatableroom');
    $routes->post('storeroom', 'MaintenanceController::storeroom');
    $routes->post('getroom', 'MaintenanceController::getByIdRoom');
    $routes->post('updateroom', 'MaintenanceController::updateroom');
    $routes->post('deleteroom', 'MaintenanceController::deleteroom');
});

// =========================
// STOCK & INVENTORY
// =========================
$routes->group('', ['filter' => 'auth'], function ($routes) {    
    $routes->group('inventory', function ($routes) {
        $routes->get('/', 'InventoryController::index');
        $routes->get('pengajuan', 'InventoryController::pengajuan');
        $routes->post('store', 'InventoryController::store'); // simpan pengajuan
        $routes->post('datatable', 'InventoryController::datatable'); // list pengajuan (optional)
        $routes->get('detail/(:num)', 'InventoryController::detail/$1');
        $routes->get('pengajuan-detail/(:num)', 'InventoryController::pengajuan_detail/$1');
    });

    // TAX CODE
    $routes->group('tax', function ($routes) {
        $routes->get('/', 'TaxController::index');
        $routes->post('datatable', 'TaxController::datatable');
        $routes->post('store', 'TaxController::store');
    });

});

// =========================
// PURCHASING
// =========================
$routes->group('', ['filter' => 'auth'], function ($routes) {    
    $routes->group('purchasing', function ($routes) {
        $routes->get('/', 'Purchasing::index');
        $routes->get('print/(:num)', 'Purchasing::print/$1');
    });
});

// =========================
// ACCOUNTING
// =========================
$routes->group('', ['filter' => 'auth'], function ($routes) {

    // JOURNAL (WEB)
    $routes->group('journal', ['filter' => 'auth'], function ($routes) {
        $routes->get('/', 'JournalController::index');
        $routes->post('datatable', 'JournalController::datatable');
        $routes->post('store', 'JournalController::store');
        $routes->post('submit/(:num)', 'JournalController::submit/$1');
        $routes->post('post/(:num)', 'JournalController::post/$1');
        $routes->post('reverse/(:num)', 'JournalController::reverse/$1');
        $routes->get('detail/(:num)', 'JournalController::detail/$1');
    });
});


// USER (WEB)
$routes->group('', ['filter' => 'auth'], function ($routes) {
    $routes->get('users', 'UserController::index');
    $routes->post('users/datatable', 'UserController::datatable');
    $routes->post('users/store', 'UserController::store');
    $routes->post('users/get', 'UserController::getById');
    $routes->post('users/update', 'UserController::update');
    $routes->post('users/delete', 'UserController::delete');
});

// COA (WEB)
$routes->group('coa', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'CoaController::index');
    $routes->post('datatable', 'CoaController::datatable');
    $routes->post('store', 'CoaController::store');
    $routes->post('get', 'CoaController::getById');
    $routes->post('update', 'CoaController::update');
    $routes->post('delete', 'CoaController::delete');
});

// equity
$routes->group('equity', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'EquityController::index');
    $routes->post('datatable', 'EquityController::datatable');
    $routes->post('store', 'EquityController::store');
    $routes->post('update', 'EquityController::update');
    $routes->post('delete', 'EquityController::delete');
    $routes->post('get', 'EquityController::get');
    $routes->get('opening-balance', 'EquityController::openingBalance');
    $routes->post('opening-balance/save', 'EquityController::saveOpeningBalance');

});

// Transaction
$routes->group('transaction', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'TransactionController::index');
    $routes->post('datatable', 'TransactionController::datatable');
    $routes->post('store', 'TransactionController::store');
});

// Approval
$routes->group('approval', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'ApprovalController::index');
    $routes->post('datatable', 'ApprovalController::datatable');
    $routes->post('approve/(:num)', 'ApprovalController::approve/$1');
    $routes->post('reject/(:num)', 'ApprovalController::reject/$1');
    $routes->post('history/(:num)', 'ApprovalController::history/$1');
});

// Closing periode
$routes->group('closing', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'ClosingController::index');
    $routes->post('datatable', 'ClosingController::datatable');
    $routes->post('close/(:num)', 'ClosingController::close/$1');
    $routes->post('open/(:num)', 'ClosingController::open/$1');
});

// Financial Reports
$routes->group('report', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'ReportController::index');
    $routes->get('profit-loss', 'ReportController::profitLoss');
});

$routes->get('export/journals', 'ExportController::journals', ['filter' => 'auth']);

$routes->get('opening-balance', 'OpeningBalanceController::index');
$routes->post('opening-balance/save', 'OpeningBalanceController::save');
$routes->get('trial-balance', 'TrialBalanceController::index');
$routes->get('balance-sheet', 'BalanceSheetController::index');
$routes->get('income-statement', 'IncomeStatementController::index');

$routes->get('branches',              'Api\BranchController::index');
$routes->post('branches',             'Api\BranchController::store');
$routes->get('branches/(:num)',       'Api\BranchController::show/$1');

// =========================
// API – PUBLIC
// =========================
$routes->group('api', function ($routes) {
    $routes->post('auth/login',    'Api\AuthController::login');
    $routes->post('auth/register', 'Api\AuthController::register');
    $routes->post('auth/refresh',  'Api\AuthController::refresh');
});

// =========================
// API – PROTECTED (JWT)
// =========================
$routes->group('api', ['filter' => 'jwt'], function ($routes) {
    // Login & Logout
    $routes->post('loginrefresh',   'Api\AuthController::refresh');
    $routes->post('logout',         'Api\AuthController::logout');
    $routes->post('logout-all',     'Api\AuthController::logoutAll');

    // Companies
    $routes->get('companies',            'Api\CompanyController::index');
    $routes->post('companies',           'Api\CompanyController::store');
    $routes->get('companies/(:num)',     'Api\CompanyController::show/$1');
    $routes->put('companies/(:num)',     'Api\CompanyController::update/$1');
    $routes->delete('companies/(:num)',  'Api\CompanyController::delete/$1');

    // Branches
    $routes->get('branches',                    'Api\BranchController::index');
    $routes->post('branches',                   'Api\BranchController::store');
    $routes->get('branches/(:num)',             'Api\BranchController::show/$1');
    $routes->post('branches/update',            'Api\BranchController::update');
    $routes->get('branches/ratio/(:num)',       'Api\BranchController::ratio/$1');
    $routes->get('branches/target/(:num)',      'Api\BranchController::target/$1');
    $routes->get('branches/target-list/(:num)', 'Api\BranchController::targetList/$1');
    $routes->post('branches/ratio-spend',       'Api\BranchController::storeSpend');
    $routes->post('branches/ratio-worker',      'Api\BranchController::storeWorker');
    $routes->post('branches/ratio-dw',          'Api\BranchController::storeDw');
    $routes->get('branches-name/(:any)',        'Api\BranchController::showByName/$1');

    // Fiscal Years
    $routes->get('fiscal-years',                   'Api\FiscalYearController::index');
    $routes->post('fiscal-years',                  'Api\FiscalYearController::store');
    $routes->post('fiscal-years/(:num)/activate',  'Api\FiscalYearController::activate/$1');

    // Accounting Periods
    $routes->get('periods',                 'Api\AccountingPeriodController::index');
    $routes->post('periods',                'Api\AccountingPeriodController::store');
    $routes->post('periods/(:num)/close',   'Api\AccountingPeriodController::close/$1');
    $routes->post('periods/(:num)/open',    'Api\AccountingPeriodController::open/$1');

    // Accounts
    $routes->get('accounts',            'Api\AccountController::index');
    $routes->post('accounts',           'Api\AccountController::store');
    $routes->get('accounts/tree',       'Api\AccountController::tree');
    $routes->get('accounts/(:num)',     'Api\AccountController::show/$1');
    $routes->put('accounts/(:num)',     'Api\AccountController::update/$1');

    // ======================
    // PARTNERS (VENDOR)
    // ======================
    $routes->get('partners',              'Api\BusinessPartnerController::index');
    $routes->post('partners',             'Api\BusinessPartnerController::store');
    $routes->get('partners/(:num)',       'Api\BusinessPartnerController::show/$1');
    $routes->put('partners/(:num)',       'Api\BusinessPartnerController::update/$1');
    $routes->delete('partners/(:num)',    'Api\BusinessPartnerController::delete/$1');

    // ======================
    // PARTNER ITEMS 🔥
    // ======================
    $routes->get('partners/(:num)/items',        'Api\BusinessPartnerController::items/$1');
    $routes->post('partners/items',              'Api\BusinessPartnerController::storeItem');
    $routes->put('partners/items/(:num)',        'Api\BusinessPartnerController::updateItem/$1');
    $routes->delete('partners/items/(:num)',     'Api\BusinessPartnerController::deleteItem/$1');
    $routes->get('partners/items/(:num)',        'Api\BusinessPartnerController::showItem/$1');
    $routes->get('partners/items',               'Api\BusinessPartnerController::allItems');

    // Transactions
    $routes->get('transactions',        'Api\TransactionController::index');
    $routes->post('transactions',       'Api\TransactionController::store');
    $routes->get('transactions/(:num)', 'Api\TransactionController::show/$1');

    // Journals
    $routes->get('journals',                    'Api\JournalController::index');
    $routes->post('journals',                    'Api\JournalController::create');
    // $routes->post('journals',                   'Api\JournalController::store');
    $routes->get('journals/(:num)',             'Api\JournalController::show/$1');
    $routes->post('journals/(:num)/submit',     'Api\JournalController::submit/$1');
    $routes->post('journals/(:num)/approve',    'Api\JournalApprovalController::approve/$1');
    $routes->post('journals/(:num)/reject',     'Api\JournalApprovalController::reject/$1');
    $routes->post('journals/(:num)/post',       'Api\JournalController::post/$1');
    $routes->post('journals/(:num)/reverse',    'Api\JournalController::reverse/$1');

    // Taxes
    $routes->get('taxes',   'Api\TaxController::index');
    $routes->post('taxes',  'Api\TaxController::store');

    // Journal Taxes
    $routes->post('journal-taxes', 'Api\JournalTaxController::store');

    // Sub Ledgers
    $routes->get('sub-ledgers/ar',    'Api\SubLedgerController::accountsReceivable');
    $routes->get('sub-ledgers/ap',    'Api\SubLedgerController::accountsPayable');
    $routes->get('sub-ledgers/aging', 'Api\SubLedgerController::aging');

    // Reports
    $routes->get('reports/profit-loss',     'Api\ReportController::profitLoss');
    $routes->get('reports/balance-sheet',   'Api\ReportController::balanceSheet');
    $routes->get('reports/cash-flow',       'Api\ReportController::cashFlow');
    $routes->get('reports/branch/(:num)',   'Api\ReportController::byBranch/$1');
    $routes->get('reports/consolidated',    'Api\ReportController::consolidated');

    // Audit
    $routes->get('audit',            'Api\AuditController::index');
    $routes->get('audit/(:num)',     'Api\AuditController::show/$1');

    // Export
    $routes->get('export/journals',        'Api\ExportController::journals');
    $routes->get('export/profit-loss',     'Api\ExportController::profitLoss');
    $routes->get('export/balance-sheet',   'Api\ExportController::balanceSheet');

    // System
    $routes->get('system/health',       'Api\SystemController::health');
    $routes->get('system/permissions',  'Api\SystemController::permissions');

    // ECOMMERCE
    $routes->get('branches/(:num)/menu','Api\Branches::menu/$1');
    $routes->get('branches/(:num)/hours','Api\Branches::hours/$1');
    $routes->post('branches/(:num)/hours','Api\Branches::saveHours/$1');
    $routes->post('branches/(:num)','Api\Branches::update/$1');

    // CATEGORY
    $routes->post('categories/create','Api\Categories::create');

    // ITEMS
    $routes->get('items/category/(:num)','Api\Items::category/$1');
    $routes->get('items/branch/(:num)/category/(:num)','Api\Items::branchCategory/$1/$2');
    $routes->post('items/create', 'Api\Items::create');
    $routes->post('upload/product', 'Api\Upload::uploadProduct');

    // CART
    $routes->post('cart/create','Api\Cart::create');
    $routes->post('cart/add','Api\Cart::add');
    $routes->post('cart/remove','Api\Cart::remove');
    $routes->post('cart/update','Api\Cart::update');
    $routes->get('cart/(:num)','Api\Cart::get/$1');
    $routes->get('cart/all/(:num)','Api\Cart::getAll/$1');

    // ORDER
    $routes->get('orders','Api\Orders::orders');
    $routes->post('orders/checkout','Api\Orders::checkout');
    $routes->get('orders/(:num)','Api\Orders::list/$1');
    $routes->get('orders/detail/(:num)','Api\Orders::detail/$1');
    $routes->post('orders/pay','Api\Orders::pay');
    $routes->get('orders/summary','Api\Orders::summary');

    // CUSTOMERS
    $routes->get('customers','Api\Customers::index');
    $routes->get('customers/(:num)','Api\Customers::show/$1');
    $routes->get('customers/(:num)/orders','Api\Customers::orders/$1');
    $routes->get('customers/(:num)/wallet','Api\Customers::wallet/$1');
    $routes->get('customers/(:num)/points','Api\Customers::points/$1');
    $routes->get('customers/(:num)/membership','Api\Customers::membership/$1');
    $routes->get('customers/(:num)/referrals','Api\Customers::referrals/$1');
    $routes->get('customers/(:num)/promos','Api\Customers::promos/$1');

    // PAYMENT
    $routes->post('payments/pay','Api\Payments::pay');

    // WALLET
    $routes->get('wallet/(:num)','Api\Wallet::balance/$1');
    $routes->get('wallet/transactions','Api\Wallet::transactions');
    $routes->post('wallet/topup','Api\Wallet::topup');

    // LOYALTY
    $routes->get('loyalty/rules','Api\Loyalty::rules');
    $routes->get('loyalty/tiers','Api\Loyalty::tiers');
    $routes->get('loyalty/(:num)','Api\Loyalty::status/$1');
    $routes->get('loyalty/benefits/(:num)','Api\Loyalty::benefits/$1');
    $routes->get('loyalty/rewards/(:num)','Api\Loyalty::rewards/$1');

    // MEMBERSHIP
    $routes->get('membership/history/(:num)','Api\Membership::history/$1');

    // POINT
    $routes->get('points/(:num)','Api\Points::balance/$1');
    $routes->get('points/history/(:num)','Api\Points::history/$1');

    // POINT RULES
    $routes->get('point-rules/(:num)', 'Api\Points::pointRule/$1');
    $routes->post('point-rules/(:num)', 'Api\Points::savePointRule/$1');

    // VOUCHER
    $routes->get('vouchers','Api\Vouchers::index');
    $routes->post('vouchers/apply','Api\Vouchers::apply');
    $routes->get('vouchers/list','Api\Vouchers::list');
    $routes->post('vouchers/store','Api\Vouchers::store');
    $routes->post('vouchers/update/(:num)','Api\Vouchers::update/$1');
    $routes->delete('vouchers/delete/(:num)','Api\Vouchers::delete/$1');

    // CHAT
    $routes->post('chat/create','Api\Chat::create');
    $routes->get('chat/(:num)','Api\Chat::list/$1');
    $routes->get('chat/messages/(:num)','Api\Chat::messages/$1');
    $routes->post('chat/send','Api\Chat::send');
    $routes->get('chat/admin','Api\Chat::adminChats');
    $routes->post('chat/create-admin','Api\Chat::adminCreate');

    // MAINTENANCE
    $routes->post('maintenance/create', 'Api\Maintenance::create');

    // INVENTORY
    $routes->post('pengajuan', 'Api\InventoryController::store');
    $routes->get('pengajuan', 'Api\InventoryController::index');
    $routes->get('pengajuan/(:num)', 'Api\InventoryController::show/$1');
    $routes->get('pengajuan/stats', 'Api\InventoryController::stats');
    $routes->get('inventory/list', 'Api\InventoryController::inventoryList');
    $routes->get('inventory/stats', 'Api\InventoryController::inventoryStats');

    // PURCHASING
    $routes->get('purchasing', 'Api\PurchasingController::index');
    $routes->post('purchasing/generate/(:num)', 'Api\PurchasingController::generateFromPengajuan/$1');
    $routes->get('purchasing/stats', 'Api\PurchasingController::stats');
    $routes->post('purchasing/save', 'Api\PurchasingController::save');

    // BUDGET LIMIT
    $routes->post('budget-limit', 'Api\BudgetController::limit');
});