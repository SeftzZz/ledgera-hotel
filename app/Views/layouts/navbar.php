                    <nav
                        class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
                        id="layout-navbar">
                        <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                            <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                                <i class="ti ti-menu-2 ti-sm"></i>
                            </a>
                        </div>

                        <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                            <!-- Info Company -->
                            <?php
                                use App\Models\CompanyModel;
                                $companyId      = session()->get('company_id') ?? 0;

                                // Default
                                if ($companyId == 0) {
                                    $companyName    = 'Ledgera Apps';
                                    $companyLoc     = 'Jakarta, Indonesia';
                                    $companyWebsite = 'www.ledgera.com';
                                    $companyLogo    = 'uploads/logos/Logo-48.png';
                                }

                                if ($companyId != 0) {
                                    $companyModel = new CompanyModel();
                                    $company = $companyModel
                                        ->select('company_name, company_addr, company_web, company_logo')
                                        ->where('id', $companyId)
                                        ->first();

                                    if ($company) {
                                        $companyName    = $company['company_name'];
                                        $companyLoc     = $company['company_addr'];
                                        $companyWebsite = $company['company_web'];
                                        $companyLogo    = $company['company_logo'];
                                    }
                                }

                                // Inisial Company
                                $companyInitials = '';
                                foreach (explode(' ', $companyName) as $w) {
                                    if ($w !== '') {
                                        $companyInitials .= strtoupper(substr($w, 0, 1));
                                    }
                                }
                                $companyInitials = substr($companyInitials, 0, 2);
                            ?>

                            <div class="navbar-nav align-items-center">
                                <div class="dropdown">
                                    <div class="d-flex align-items-center gap-2 cursor-pointer"
                                         data-bs-toggle="dropdown">

                                        <!-- Logo -->
                                        <div class="avatar avatarNav-sm">
                                            <?php if (!empty($companyLogo) && file_exists(FCPATH . $companyLogo)): ?>
                                                <img src="<?= base_url($companyLogo) ?>" class="rounded-circleColor" />
                                            <?php else: ?>
                                                <span class="avatar-initial rounded-circle bg-label-primary">
                                                    <?= esc($companyInitials) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Info -->
                                        <div class="d-none d-md-flex flex-column lh-sm">
                                            <span class="fw-medium text-body"><?= esc($companyName) ?></span>
                                            <small class="text-muted"><?= esc($companyLoc) ?></small>
                                        </div>

                                        <i class="ti ti-chevron-down"></i>
                                    </div>

                                    <!-- DROPDOWN -->
                                    <ul class="dropdown-menu">
                                        <?php foreach ($companies as $c): ?>
                                            <li>
                                                <a class="dropdown-item switch-company <?= $c['id'] == $companyId ? 'active' : '' ?>"
                                                   href="javascript:void(0)"
                                                   data-id="<?= $c['id'] ?>">
                                                    <?= esc($c['company_name']) ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                            <!-- /Info hotel -->

                            <ul class="navbar-nav flex-row align-items-center ms-auto">
                                <!-- Language -->
                                <!-- <li class="nav-item dropdown-language dropdown me-2 me-xl-0">
                                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <i class="ti ti-language rounded-circle ti-md"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0);" data-language="en" data-text-direction="ltr">
                                                <span class="align-middle">English</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0);" data-language="fr" data-text-direction="ltr">
                                                <span class="align-middle">French</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li> -->
                                <!--/ Language -->

                                <!-- Notification -->
                                <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-1">
                                    <a
                                        class="nav-link dropdown-toggle hide-arrow"
                                        href="javascript:void(0);"
                                        data-bs-toggle="dropdown"
                                        data-bs-auto-close="outside"
                                        aria-expanded="false">
                                            <i class="ti ti-bell ti-md"></i>
                                            <span class="badge bg-danger rounded-pill badge-notifications">2</span>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end py-0">
                                        <li class="dropdown-menu-header border-bottom">
                                          <div class="dropdown-header d-flex align-items-center py-3">
                                            <h5 class="text-body mb-0 me-auto">Notification</h5>
                                            <a
                                              href="javascript:void(0)"
                                              class="dropdown-notifications-all text-body"
                                              data-bs-toggle="tooltip"
                                              data-bs-placement="top"
                                              title="Mark all as read"
                                              ><i class="ti ti-mail-opened fs-4"></i
                                            ></a>
                                          </div>
                                        </li>
                                        <li class="dropdown-notifications-list scrollable-container">
                                          <ul class="list-group list-group-flush">
                                            <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                              <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                  <div class="avatar">
                                                    <img src="<?= base_url('assets/img/avatars/1.png') ?>" alt class="h-auto rounded-circle" />
                                                  </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                  <h6 class="mb-1">Congratulation Lettie 🎉</h6>
                                                  <p class="mb-0">Won the monthly best seller gold badge</p>
                                                  <small class="text-muted">1h ago</small>
                                                </div>
                                                <div class="flex-shrink-0 dropdown-notifications-actions">
                                                  <a href="javascript:void(0)" class="dropdown-notifications-read"
                                                    ><span class="badge badge-dot"></span
                                                  ></a>
                                                  <a href="javascript:void(0)" class="dropdown-notifications-archive"
                                                    ><span class="ti ti-x"></span
                                                  ></a>
                                                </div>
                                              </div>
                                            </li>
                                            <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                              <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                  <div class="avatar">
                                                    <span class="avatar-initial rounded-circle bg-label-danger">CF</span>
                                                  </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                  <h6 class="mb-1">Charles Franklin</h6>
                                                  <p class="mb-0">Accepted your connection</p>
                                                  <small class="text-muted">12hr ago</small>
                                                </div>
                                                <div class="flex-shrink-0 dropdown-notifications-actions">
                                                  <a href="javascript:void(0)" class="dropdown-notifications-read"
                                                    ><span class="badge badge-dot"></span
                                                  ></a>
                                                  <a href="javascript:void(0)" class="dropdown-notifications-archive"
                                                    ><span class="ti ti-x"></span
                                                  ></a>
                                                </div>
                                              </div>
                                            </li>
                                            <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                                              <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                  <div class="avatar">
                                                    <img src="<?= base_url('assets/img/avatars/2.png') ?>" alt class="h-auto rounded-circle" />
                                                  </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                  <h6 class="mb-1">New Message ✉️</h6>
                                                  <p class="mb-0">You have new message from Natalie</p>
                                                  <small class="text-muted">1h ago</small>
                                                </div>
                                                <div class="flex-shrink-0 dropdown-notifications-actions">
                                                  <a href="javascript:void(0)" class="dropdown-notifications-read"
                                                    ><span class="badge badge-dot"></span
                                                  ></a>
                                                  <a href="javascript:void(0)" class="dropdown-notifications-archive"
                                                    ><span class="ti ti-x"></span
                                                  ></a>
                                                </div>
                                              </div>
                                            </li>
                                          </ul>
                                        </li>
                                        <li class="dropdown-menu-footer border-top">
                                          <a
                                            href="javascript:void(0);"
                                            class="dropdown-item d-flex justify-content-center text-primary p-2 h-px-40 mb-1 align-items-center">
                                            View all notifications
                                          </a>
                                        </li>
                                    </ul>
                                </li>
                                <!--/ Notification -->

                                <!-- User -->
                                <?php
                                  $userName  = session()->get('user_name') ?? 'User';
                                  $userRole  = session()->get('user_role') ?? '';
                                  $userPhoto = session()->get('user_photo');

                                  // Ambil inisial nama
                                  $initials = '';
                                  $names = explode(' ', trim($userName));
                                  foreach ($names as $n) {
                                      if ($n !== '') {
                                          $initials .= strtoupper(substr($n, 0, 1));
                                      }
                                  }
                                  $initials = substr($initials, 0, 2);

                                  // Mapping role
                                  $roleMap = [
                                      'admin'                => 'Admin HW',
                                      'worker'               => 'Mitra',
                                      'hotel_hr'             => 'User HR',
                                      'hotel_fo'             => 'User FO',
                                      'hotel_hk'             => 'User HK',
                                      'hotel_fnb_service'    => 'User FnBS',
                                      'hotel_fnb_production' => 'User FnBP',
                                  ];

                                  $roleLabel = $roleMap[$userRole] ?? ucfirst($userRole);
                                ?>

                                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                                        <div class="avatar avatar-online">
                                            <?php if (!empty($userPhoto) && file_exists(FCPATH . $userPhoto)): ?>
                                                <img src="<?= base_url($userPhoto) ?>" class="h-auto rounded-circleColor" />
                                            <?php else: ?>
                                                <span
                                                    class="avatar-initial rounded-circle bg-label-primary">
                                                    <?= esc($initials) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="#">
                                                <div class="d-flex">
                                                    <div class="flex-shrink-0 me-3">
                                                        <div class="avatar avatar-online">
                                                            <?php if (!empty($userPhoto) && file_exists(FCPATH . $userPhoto)): ?>
                                                                <img src="<?= base_url($userPhoto) ?>" class="h-auto rounded-circleColor" />
                                                            <?php else: ?>
                                                                <span
                                                                    class="avatar-initial rounded-circle bg-label-primary">
                                                                    <?= esc($initials) ?>
                                                                </span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <span class="fw-medium d-block">
                                                            <?= esc($userName) ?>
                                                        </span>
                                                        <small class="text-muted">
                                                            <?= esc($roleLabel) ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <div class="dropdown-divider"></div>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="<?= base_url('logout') ?>">
                                                <i class="ti ti-logout me-2 ti-sm"></i>
                                                <span class="align-middle">Log Out</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <!--/ User -->
                            </ul>
                        </div>
                    </nav>