<?php
  $uri = service('uri');

  $segment1 = $uri->getSegment(1);
  $segment2 = $uri->getSegment(2);
?>
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="<?= base_url('dashboard') ?>" class="app-brand-link">
            <img src="<?= base_url('assets/img/logo-smb.png') ?>" width="50%" />
            <span class="app-brand-text demo menu-text fw-bold"></span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
            <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">

        <!-- DASHBOARD -->
        <li class="menu-item <?= ($uri=='dashboard')?'active':'' ?>">
            <a href="<?= base_url('dashboard') ?>" class="menu-link">
                <i class="menu-icon tf-icons ti ti-smart-home"></i>
                <div>Dashboard</div>
            </a>
        </li>

        <!-- MASTER DATA -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Master Data</span>
        </li>

        <li class="menu-item <?= ($uri=='company')?'active':'' ?>">
            <a href="<?= base_url('company') ?>" class="menu-link">
                <i class="menu-icon tf-icons ti ti-building"></i>
                <div>Company</div>
            </a>
        </li>

        <li class="menu-item <?= ($uri=='branch')?'active':'' ?>">
            <a href="<?= base_url('branch') ?>" class="menu-link">
                <i class="menu-icon tf-icons ti ti-building-store"></i>
                <div>Branch</div>
            </a>
        </li>

        <li class="menu-item <?= ($uri=='coa')?'active':'' ?>">
            <a href="<?= base_url('coa') ?>" class="menu-link">
                <i class="menu-icon tf-icons ti ti-list-numbers"></i>
                <div>Chart of Accounts</div>
            </a>
        </li>

        <li class="menu-item <?= ($uri=='partner')?'active':'' ?>">
            <a href="<?= base_url('partner') ?>" class="menu-link">
                <i class="menu-icon tf-icons ti ti-users-group"></i>
                <div>Business Partner</div>
            </a>
        </li>

        <li class="menu-item <?= ($uri=='tax')?'active':'' ?>">
            <a href="<?= base_url('tax') ?>" class="menu-link">
                <i class="menu-icon tf-icons ti ti-receipt-tax"></i>
                <div>Tax Codes</div>
            </a>
        </li>

        <!-- Units -->
        <li class="menu-header small text-uppercase">
          <span class="menu-header-text" data-i18n="Units">Units</span>
        </li>
        <li class="menu-item <?= ($segment1=='chats' && $segment2=='')?'active':'' ?>">
          <a href="<?= base_url('chats') ?>" class="menu-link">
            <i class="menu-icon tf-icons ti ti-messages"></i>
            <div data-i18n="Chats">Chats</div>
          </a>
        </li>
        <!-- reporting menu start -->
        <li class="menu-item <?= ($segment1=='items'||$segment1=='item-add'||$segment1=='item-category'||$segment1=='orders'||$segment1=='customers'||$segment1=='branches'||$segment1=='referrals'||$segment1=='vouchers')?'active open':'' ?>">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons ti ti-list"></i>
            <div data-i18n="Reporting">Reporting</div>
          </a>

          <ul class="menu-sub">

            <!-- PRODUCTS -->
            <li class="menu-item <?= ($segment1=='items')?'open active':'' ?>">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <div data-i18n="Products">Products</div>
              </a>

              <ul class="menu-sub">
                <li class="menu-item <?= ($segment1=='items' && $segment2=='')?'active':'' ?>">
                  <a href="<?= base_url('items') ?>" class="menu-link">
                    <div data-i18n="Product List">Product List</div>
                  </a>
                </li>

                <li class="menu-item <?= ($segment2=='item-add')?'active':'' ?>">
                  <a href="<?= base_url('items/item-add') ?>" class="menu-link">
                    <div data-i18n="Add Product">Add Product</div>
                  </a>
                </li>

                <li class="menu-item <?= ($segment2=='item-category')?'active':'' ?>">
                  <a href="<?= base_url('items/item-category') ?>" class="menu-link">
                    <div data-i18n="Category List">Category List</div>
                  </a>
                </li>
              </ul>
            </li>

            <!-- ORDERS -->
            <li class="menu-item <?= ($segment1=='orders')?'open active':'' ?>">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <div data-i18n="Income">Income</div>
              </a>

              <ul class="menu-sub">
                <li class="menu-item <?= ($segment1=='orders' && $segment2=='')?'active':'' ?>">
                  <a href="<?= base_url('orders') ?>" class="menu-link">
                    <div data-i18n="Income List">Income List</div>
                  </a>
                </li>
                <li class="menu-item <?= ($segment2=='order-add')?'active':'' ?>">
                  <a href="<?= base_url('orders/order-add') ?>" class="menu-link">
                    <div data-i18n="Income Add">Income Add</div>
                  </a>
                </li>
              </ul>
            </li>

            <!-- CUSTOMERS -->
            <li class="menu-item <?= ($segment1=='customers')?'open active':'' ?>">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <div data-i18n="Customers">Customers</div>
              </a>

              <ul class="menu-sub">
                <li class="menu-item <?= ($segment1=='customers')?'active':'' ?>">
                  <a href="<?= base_url('customers') ?>" class="menu-link">
                    <div data-i18n="Customer List">Customer List</div>
                  </a>
                </li>
              </ul>
            </li>

            <!-- SETTINGS -->
            <li class="menu-item <?= ($segment1=='branches')?'open active':'' ?>">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <div data-i18n="Settings">Settings</div>
              </a>

              <ul class="menu-sub">
                <li class="menu-item <?= ($segment1=='branches')?'active':'' ?>">
                  <a href="<?= base_url('branches') ?>" class="menu-link">
                    <div data-i18n="Store Details">Store Details</div>
                  </a>
                </li>
              </ul>
            </li>

          </ul>
        </li>
        <!-- reporting menu end -->

        <!-- Preventive & Maintenance -->
        <li class="menu-header small text-uppercase">
          <span class="menu-header-text" data-i18n="Preventive & Maintenance">Preventive & Maintenance</span>
        </li>
        <!-- preventive menu start -->
        <li class="menu-item <?= ($segment1=='items'||$segment1=='item-add'||$segment1=='item-category'||$segment1=='orders'||$segment1=='customers'||$segment1=='branches'||$segment1=='referrals'||$segment1=='vouchers')?'active open':'' ?>">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons ti ti-list"></i>
            <div data-i18n="Maintenance">Maintenance</div>
          </a>

          <ul class="menu-sub">

            <!-- ROOMS -->
            <li class="menu-item <?= ($segment1=='dashboard')?'open active':'' ?>">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <div data-i18n="Rooms">Rooms</div>
              </a>

              <ul class="menu-sub">
                <li class="menu-item <?= ($segment1=='dashboard' && $segment2=='')?'active':'' ?>">
                  <a href="<?= base_url('dashboard') ?>" class="menu-link">
                    <div data-i18n="Room List">Room List</div>
                  </a>
                </li>

                <li class="menu-item <?= ($segment2=='dashboard')?'active':'' ?>">
                  <a href="<?= base_url('dashboard') ?>" class="menu-link">
                    <div data-i18n="Add Room">Add Room</div>
                  </a>
                </li>

                <li class="menu-item <?= ($segment2=='dashboard')?'active':'' ?>">
                  <a href="<?= base_url('dashboard') ?>" class="menu-link">
                    <div data-i18n="Category Room List">Category Room List</div>
                  </a>
                </li>
              </ul>
            </li>

            <li class="menu-item <?= ($uri=='dashboard')?'active':'' ?>">
                <a href="<?= base_url('dashboard') ?>" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-list"></i>
                    <div>Add Maintenance</div>
                </a>
            </li>

          </ul>
        </li>
        <!-- preventive menu end -->

        <!-- Stock & Inventory -->
        <li class="menu-header small text-uppercase">
          <span class="menu-header-text" data-i18n="Stock & Inventory">Stock & Inventory</span>
        </li>
        <!-- stock & inventory menu start -->
        <li class="menu-item <?= ($segment1=='items'||$segment1=='item-add'||$segment1=='item-category'||$segment1=='orders'||$segment1=='customers'||$segment1=='branches'||$segment1=='referrals'||$segment1=='vouchers'||$segment1=='inventory'||$segment1=='pengajuan')?'active open':'' ?>">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons ti ti-list"></i>
            <div data-i18n="Inventory">Inventory</div>
          </a>

          <ul class="menu-sub">

            <!-- STOCKS -->
            <li class="menu-item <?= ($segment1=='inventory')?'open active':'' ?>">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <div data-i18n="Stocks">Stocks</div>
              </a>

              <ul class="menu-sub">
                <li class="menu-item <?= ($segment2=='pengajuan')?'active':'' ?>">
                  <a href="<?= base_url('inventory/pengajuan') ?>" class="menu-link">
                    <div data-i18n="Pengajuan Barang">Pengajuan Barang</div>
                  </a>
                </li>
                
                <li class="menu-item <?= ($segment1=='inventory' && $segment2=='')?'active':'' ?>">
                  <a href="<?= base_url('inventory') ?>" class="menu-link">
                    <div data-i18n="Inventory List">Inventory List</div>
                  </a>
                </li>

              </ul>
            </li>

          </ul>
        </li>
        <!-- stock & inventory menu end -->

        <!-- Purchasing -->
        <li class="menu-header small text-uppercase">
          <span class="menu-header-text" data-i18n="Purchasing">Purchasing</span>
        </li>
        
        <li class="menu-item <?= ($uri=='purchasing')?'active':'' ?>">
            <a href="<?= base_url('purchasing') ?>" class="menu-link">
                <i class="menu-icon tf-icons ti ti-list"></i>
                <div>Purchasing</div>
            </a>
        </li>

        <!-- ACCOUNTING -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Accounting</span>
        </li>

        <li class="menu-item <?= ($uri=='opening-balance')?'active':'' ?>">
            <a href="<?= base_url('opening-balance') ?>" class="menu-link">
                <i class="menu-icon tf-icons ti ti-scale"></i>
                <div>Opening Balance</div>
            </a>
        </li>

        <li class="menu-item <?= ($uri=='transaction')?'active':'' ?>">
            <a href="<?= base_url('transaction') ?>" class="menu-link">
                <i class="menu-icon tf-icons ti ti-credit-card"></i>
                <div>Transactions</div>
            </a>
        </li>

        <li class="menu-item <?= ($uri=='journal')?'active':'' ?>">
            <a href="<?= base_url('journal') ?>" class="menu-link">
                <i class="menu-icon tf-icons ti ti-file-text"></i>
                <div>Journal</div>
            </a>
        </li>

        <!-- NEW MENU -->
        <li class="menu-item <?= ($uri=='trial-balance')?'active':'' ?>">
            <a href="<?= base_url('trial-balance') ?>" class="menu-link">
                <i class="menu-icon tf-icons ti ti-report-analytics"></i>
                <div>Trial Balance</div>
            </a>
        </li>

        <li class="menu-item <?= ($uri=='balance-sheet')?'active':'' ?>">
            <a href="<?= base_url('balance-sheet') ?>" class="menu-link">
                <i class="menu-icon tf-icons ti ti-report-money"></i>
                <div>Balance Sheet</div>
            </a>
        </li>

        <li class="menu-item <?= ($uri=='income-statement')?'active':'' ?>">
            <a href="<?= base_url('income-statement') ?>" class="menu-link">
                <i class="menu-icon tf-icons ti ti-chart-line"></i>
                <div>Income Statement</div>
            </a>
        </li>

        <li class="menu-item <?= ($uri=='equity')?'active':'' ?>">
            <a href="<?= base_url('equity') ?>" class="menu-link">
                <i class="menu-icon tf-icons ti ti-wallet"></i>
                <div>Equity</div>
            </a>
        </li>

        <li class="menu-item <?= ($uri=='pnl')?'active':'' ?>">
            <a href="<?= base_url('pnl') ?>" class="menu-link">
                <i class="menu-icon tf-icons ti ti-plus-minus"></i>
                <div>P&L</div>
            </a>
        </li>

        <li class="menu-item <?= ($uri=='approval')?'active':'' ?>">
            <a href="<?= base_url('approval') ?>" class="menu-link">
                <i class="menu-icon tf-icons ti ti-checks"></i>
                <div>Approval</div>
            </a>
        </li>

        <li class="menu-item <?= ($uri=='closing')?'active':'' ?>">
            <a href="<?= base_url('closing') ?>" class="menu-link">
                <i class="menu-icon tf-icons ti ti-calendar-check"></i>
                <div>Closing Period</div>
            </a>
        </li>
        <!-- REPORT -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Reports</span>
        </li>

        <li class="menu-item <?= ($uri=='report')?'active':'' ?>">
            <a href="<?= base_url('report') ?>" class="menu-link">
                <i class="menu-icon tf-icons ti ti-chart-bar"></i>
                <div>Financial Reports</div>
            </a>
        </li>

        <!-- SYSTEM -->
        <?php if (hasPermission('users.view')): ?>
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">System</span>
        </li>

        <li class="menu-item <?= ($uri=='users')?'active':'' ?>">
            <a href="<?= base_url('users') ?>" class="menu-link">
                <i class="menu-icon tf-icons ti ti-users"></i>
                <div>Users</div>
            </a>
        </li>

        <li class="menu-item <?= ($uri=='roles')?'active':'' ?>">
            <a href="<?= base_url('roles') ?>" class="menu-link">
                <i class="menu-icon tf-icons ti ti-shield"></i>
                <div>Roles & Permissions</div>
            </a>
        </li>
        <?php endif; ?>

        <!-- LOGOUT -->
        <li class="menu-item">
            <a href="<?= base_url('logout') ?>" class="menu-link">
                <i class="menu-icon tf-icons ti ti-logout"></i>
                <div>Logout</div>
            </a>
        </li>

    </ul>
</aside>
