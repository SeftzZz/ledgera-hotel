<!doctype html>
<html
    lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact"
    dir="ltr" data-theme="theme-default"
    data-assets-path="assets/" data-template="vertical-menu-template-no-customizer">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

        <title><?= $title ?> | Heycorp System</title>

        <meta name="description" content="hey work connects hospitality professionals with trusted hotels for flexible daily and casual job opportunities." />

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="<?= base_url('assets/img/favicon/favicon.png') ?>" />

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link
            href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap"
            rel="stylesheet" />

        <!-- Icons -->
        <link rel="stylesheet" href="<?= base_url('assets/vendor/fonts/fontawesome.css') ?>" />
        <link rel="stylesheet" href="<?= base_url('assets/vendor/fonts/tabler-icons.css') ?>" />
        <link rel="stylesheet" href="<?= base_url('assets/vendor/fonts/flag-icons.css') ?>" />

        <!-- Core CSS -->
        <link rel="stylesheet" href="<?= base_url('assets/vendor/css/rtl/core.css') ?>" />
        <link rel="stylesheet" href="<?= base_url('assets/vendor/css/rtl/theme-default.css') ?>" />
        <link rel="stylesheet" href="<?= base_url('assets/css/demo.css') ?>" />

        <!-- Vendors CSS -->
        <link rel="stylesheet" href="<?= base_url('assets/vendor/libs/node-waves/node-waves.css') ?>" />
        <link rel="stylesheet" href="<?= base_url('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') ?>" />
        <link rel="stylesheet" href="<?= base_url('assets/vendor/libs/typeahead-js/typeahead.css') ?>" />
        <link rel="stylesheet" href="<?= base_url('assets/vendor/libs/apex-charts/apex-charts.css') ?>" />
        <link rel="stylesheet" href="<?= base_url('assets/vendor/libs/swiper/swiper.css') ?>" />
        <link rel="stylesheet" href="<?= base_url('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') ?>" />
        <link rel="stylesheet" href="<?= base_url('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') ?>" />
        <link rel="stylesheet" href="<?= base_url('assets/vendor/libs/bootstrap-select/bootstrap-select.css') ?>" />
        <link rel="stylesheet" href="<?= base_url('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css') ?>" />

        <link rel="stylesheet" href="<?= base_url('assets/vendor/libs/node-waves/node-waves.css') ?>" />
        <link rel="stylesheet" href="<?= base_url('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') ?>" />
        <link rel="stylesheet" href="<?= base_url('assets/vendor/libs/typeahead-js/typeahead.css') ?>" />
        <link rel="stylesheet" href="<?= base_url('assets/vendor/libs/sweetalert2/sweetalert2.css') ?>" />
        <link rel="stylesheet" href="<?= base_url('assets/vendor/libs/quill/typography.css') ?>" />
        <link rel="stylesheet" href="<?= base_url('assets/vendor/libs/quill/katex.css') ?>" />
        <link rel="stylesheet" href="<?= base_url('assets/vendor/libs/quill/editor.css') ?>" />
        <link rel="stylesheet" href="<?= base_url('assets/vendor/libs/select2/select2.css') ?>" />
        <link rel="stylesheet" href="<?= base_url('assets/vendor/libs/dropzone/dropzone.css') ?>" />
        <link rel="stylesheet" href="<?= base_url('assets/vendor/libs/flatpickr/flatpickr.css') ?>" />
        <link rel="stylesheet" href="<?= base_url('assets/vendor/libs/tagify/tagify.css') ?>" />
        <link rel="stylesheet" href="<?= base_url('assets/vendor/libs/@form-validation/form-validation.css') ?>" />

        <!-- Page CSS -->
        <link rel="stylesheet" href="<?= base_url('assets/vendor/css/pages/cards-advance.css') ?>" />

        <link rel="stylesheet" href="<?= base_url('assets/vendor/css/pages/app-chat.css') ?>" />

        <!-- Helpers -->
        <script src="<?= base_url('assets/vendor/js/helpers.js') ?>"></script>
        <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
        <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
        <script src="<?= base_url('assets/js/config.js') ?>"></script>
    </head>

    <body>
        <!-- Layout wrapper -->
        <div class="layout-wrapper layout-content-navbar">
            <div class="layout-container">
                <!-- Menu -->
                <?= $this->include('layouts/sidebar') ?>
                <!-- / Menu -->

                <!-- Layout page -->
                <div class="layout-page">
                    <!-- Navbar -->
                    <?= $this->include('layouts/navbar') ?>
                    <!-- / Navbar -->

                    <!-- Content wrapper -->
                    <div class="content-wrapper">
                        <!-- Content -->
                        <?= $this->renderSection('content') ?>
                        <!-- / Content -->

                        <!-- Footer -->
                        <?= $this->include('layouts/footer') ?>
                        <!-- / Footer -->

                        <div class="content-backdrop fade"></div>
                    </div>
                    <!-- Content wrapper -->
                </div>
                <!-- / Layout page -->
            </div>

            <!-- Overlay -->
            <div class="layout-overlay layout-menu-toggle"></div>

            <!-- Drag Target Area To SlideIn Menu On Small Screens -->
            <div class="drag-target"></div>
        </div>
        <!-- / Layout wrapper -->

        <!-- Core JS -->
        <!-- build:js assets/vendor/js/core.js -->

        <script src="<?= base_url('assets/vendor/libs/jquery/jquery.js') ?>"></script>
        <script src="<?= base_url('assets/vendor/libs/popper/popper.js') ?>"></script>
        <script src="<?= base_url('assets/vendor/js/bootstrap.js') ?>"></script>
        <script src="<?= base_url('assets/vendor/libs/node-waves/node-waves.js') ?>"></script>
        <script src="<?= base_url('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') ?>"></script>
        <script src="<?= base_url('assets/vendor/libs/hammer/hammer.js') ?>"></script>
        <script src="<?= base_url('assets/vendor/libs/i18n/i18n.js') ?>"></script>
        <script src="<?= base_url('assets/vendor/libs/typeahead-js/typeahead.js') ?>"></script>
        <script src="<?= base_url('assets/vendor/js/menu.js') ?>"></script>
        <script src="<?= base_url('assets/vendor/libs/bootstrap-select/bootstrap-select.js') ?>"></script>
        <script src="<?= base_url('assets/vendor/libs/select2/select2.js') ?>"></script>
        <script src="<?= base_url('assets/vendor/libs/quill/katex.js') ?>"></script>
        <script src="<?= base_url('assets/vendor/libs/quill/quill.js') ?>"></script>
        <script src="<?= base_url('assets/vendor/libs/dropzone/dropzone.js') ?>"></script>
        <script src="<?= base_url('assets/vendor/libs/jquery-repeater/jquery-repeater.js') ?>"></script>
        <script src="<?= base_url('assets/vendor/libs/flatpickr/flatpickr.js') ?>"></script>
        <script src="<?= base_url('assets/vendor/libs/tagify/tagify.js') ?>"></script>

        <!-- endbuild -->

        <!-- Vendors JS -->
        <script src="<?= base_url('assets/vendor/libs/apex-charts/apexcharts.js') ?>"></script>
        <script src="<?= base_url('assets/vendor/libs/swiper/swiper.js') ?>"></script>
        <script src="<?= base_url('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') ?>"></script>
        <script src="<?= base_url('assets/vendor/libs/bootstrap-maxlength/bootstrap-maxlength.js') ?>"></script>
        <script src="<?= base_url('assets/vendor/libs/sweetalert2/sweetalert2new.js') ?>"></script>
        
        <!-- Main JS -->
        <script src="<?= base_url('assets/js/main.js') ?>"></script>

        <script src="<?= base_url('assets/js/forms-selects.js') ?>"></script>
        <?= $this->renderSection('scripts') ?>
        
        <!-- Page JS -->
        <script>
            window.jwtToken = "<?= session('jwt_token') ?>";
            window.userId = "<?= session('user_id') ?>";
            window.userEmail = "<?= session('user_email') ?>";
            window.companyId = "<?= session('company_id') ?>";
            window.companyName = "<?= session('company_name') ?>";
            window.branchId = "<?= session('branch_id') ?>";
            window.branchName = "<?= session('branch_name') ?>";
            window.branchAddress = "<?= session('branch_address') ?>";
            window.categoryName = "<?= session('category_name') ?>";
            $.ajaxSetup({
                headers: {
                    Authorization: 'Bearer ' + window.jwtToken
                }
            });
        </script>
        <script>
            $(document).on('click', '.switch-company', function () {

                const id = $(this).data('id');

                $.post('/switch-company', {
                    company_id: id
                }, function (res) {

                    if (res.status) {
                        location.reload();
                    }

                });

            });
        </script>
    </body>
</html>