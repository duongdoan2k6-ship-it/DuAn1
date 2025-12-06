<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>
        <?php echo $pageTitle ?? 'Trang Quản Trị'; ?>
    </title>

    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />

    <link href="<?php echo asset_url('css/nucleo-icons.css'); ?>" rel="stylesheet" />
    <link href="<?php echo asset_url('css/nucleo-svg.css'); ?>" rel="stylesheet" />

    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

    <link id="pagestyle" href="<?php echo asset_url('css/material-dashboard.css?v=3.0.0'); ?>" rel="stylesheet" />
</head>

<body class="g-sidenav-show  bg-gray-200">

    <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-gradient-dark"
        id="sidenav-main">
        <div class="sidenav-header">
            <a class="navbar-brand m-0" href="#">
                <span class="ms-1 font-weight-bold text-white">Danh mục</span>
            </a>
        </div>

        <hr class="horizontal light mt-0 mb-2">

        <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">

            <!-- ADMIN -->
            <span class="ms-1 text-white">Admin</span>
            <ul class="navbar-nav">
                <!-- Quản lý Tour -->
                <li class="nav-item">
                    <a class="nav-link text-white active bg-gradient-primary"
                        href="<?php echo base_url('public/index.php?action=list-tours'); ?>">
                        <span class="nav-link-text ms-1">Quản lý tour</span>
                    </a>
                </li>

                <!-- Nhân sự -->
                <li class="nav-item">
                    <a class="nav-link text-white active bg-gradient-primary"
                        href="<?php echo base_url('public/index.php?action=person'); ?>">
                        <span class="nav-link-text ms-1">Danh sách nhân sự</span>
                    </a>
                </li>

                <!-- Báo cáo tài chính -->
                <li class="nav-item">
                    <a class="nav-link text-white active bg-gradient-primary"
                        href="<?php echo base_url('public/index.php?action=list-baocao'); ?>">
                        <span class="nav-link-text ms-1">Báo cáo tài chính</span>
</a>
                </li>

                <!-- Quản lý Booking -->
                <li class="nav-item">
                    <a class="nav-link text-white active bg-gradient-primary"
                        href="<?php echo base_url('public/index.php?action=list-booking'); ?>">
                        <span class="nav-link-text ms-1">Quản lý Booking</span>
                    </a>
                </li>

                <!-- Lịch khởi hành (THÊM) -->
                <li class="nav-item">
                    <a class="nav-link text-white active bg-gradient-primary"
                        href="<?php echo base_url('public/index.php?action=list-lichkhoihanh'); ?>">
                        <span class="nav-link-text ms-1">Quản lý lịch khởi hành</span>
                    </a>
                </li>
            </ul>

            <hr class="horizontal light mt-0 mb-2">

            <!-- HƯỚNG DẪN VIÊN -->
            <span class="ms-1 text-white">Hướng Dẫn Viên</span>
            <ul class="navbar-nav">

                <!-- DS khách trong đoàn (sửa link bị thiếu) -->
                <li class="nav-item">
                    <a class="nav-link text-white active bg-gradient-primary"
                        href="<?php echo base_url('public/index.php?action=guide-list-guests'); ?>">
                        <span class="nav-link-text ms-1">Danh sách khách trong đoàn</span>
                    </a>
                </li>

            </ul>

        </div>
    </aside>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur"
            data-scroll="true">
            <div class="container-fluid py-1 px-3">
                <nav aria-label="breadcrumb">
                    <h6 class="font-weight-bolder mb-0"><?php echo $pageTitle ?? 'Dashboard'; ?></h6>
                </nav>
            </div>
        </nav>

        <div class="container-fluid py-4">
            <?php
            if (isset($viewPage) && is_readable($viewPage)) {
                require_once $viewPage;
            } else {
                echo "<h1>Lỗi: Không tìm thấy nội dung trang.</h1>";
            }
            ?>
        </div>
    </main>

    <script src="<?php echo asset_url('js/core/popper.min.js'); ?>"></script>
    <script src="<?php echo asset_url('js/core/bootstrap.min.js'); ?>"></script>
    <script src="<?php echo asset_url('js/plugins/perfect-scrollbar.min.js'); ?>"></script>
    <script src="<?php echo asset_url('js/plugins/smooth-scrollbar.min.js'); ?>"></script>
    <script src="<?php echo asset_url('js/material-dashboard.min.js?v=3.0.0'); ?>"></script>

</body>

</html>
