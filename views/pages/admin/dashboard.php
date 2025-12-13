<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Quản Lý Tours</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#"> ADMIN PANEL</a>
            <div class="d-flex align-items-center">
                <span class="navbar-text me-3 text-white">
                    Xin chào, <strong><?= $_SESSION['user']['ho_ten'] ?? 'Admin' ?></strong>
                </span>
                <a href="<?= BASE_URL ?>routes/index.php?action=logout" class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php
                $msg = $_GET['msg'];
                if ($msg == 'success') echo "Thêm mới thành công!";
                elseif ($msg == 'updated') echo "Cập nhật thành công!";
                elseif ($msg == 'deleted') echo "Đã xóa dữ liệu!";
                elseif ($msg == 'assigned') echo "Phân bổ nhân sự thành công!";
                elseif ($msg == 'created') echo "Tạo lịch trình thành công!";
                else echo htmlspecialchars($msg);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                Lỗi: <?= htmlspecialchars($_GET['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary mb-3 shadow h-100">
                    <div class="card-header fw-bold"><i class="fas fa-plane-departure"></i> LỊCH KHỞI HÀNH</div>
                    <div class="card-body">
                        <h1 class="card-title display-4 fw-bold"><?= $totalTours ?? 0 ?></h1>
                        <p class="card-text">Chuyến đi đang vận hành.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-dark bg-warning mb-3 shadow h-100">
                    <div class="card-header fw-bold"><i class="fas fa-file-invoice-dollar"></i> QUẢN LÝ BOOKING</div>
                    <div class="card-body">
                        <h1 class="card-title display-6 fw-bold">Đơn Hàng</h1>
                        <a href="<?= BASE_URL ?>routes/index.php?action=admin-bookings" class="btn btn-dark mt-2 btn-sm">
                            Xem chi tiết đơn <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-white bg-success mb-3 shadow h-100">
                    <div class="card-header fw-bold"><i class="fas fa-box-open"></i> KHO TOUR & SẢN PHẨM</div>
                    <div class="card-body">
                        <h5 class="card-title">Quản lý Tour Gốc</h5>
                        <p class="card-text small mb-2">Thêm mới, cập nhật giá & lịch trình tour.</p>
                        <a href="<?= BASE_URL ?>routes/index.php?action=admin-tours" class="btn btn-light text-success fw-bold btn-sm">
                            Truy cập Kho Tour <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="text-primary mb-0 fw-bold"><i class="fas fa-calendar-alt"></i> Danh Sách Lịch Khởi Hành</h5>
                <div>
                    <a href="<?= BASE_URL ?>routes/index.php?action=admin-guides" class="btn btn-sm btn-outline-info me-2">
                        <i class="fas fa-id-badge"></i> DS Nhân sự
                    </a>
                    <a href="<?= BASE_URL ?>routes/index.php?action=admin-create-lich" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus-circle"></i> Thêm Lịch Mới
                    </a>
                    <a href="<?= BASE_URL ?>routes/index.php?action=admin-statistics" class="btn btn-sm btn-success">
                        <i class="fas fa-chart-bar"></i> Báo Cáo & Thống Kê
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>ID</th>
                                <th class="text-start">Tên Tour</th>
                                <th>Thời Gian</th>
                                <th>HDV Chính</th>
                                <th>Số Chỗ</th>
                                <th>Trạng Thái</th>
                                <th width="200">Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($listTours)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        Chưa có chuyến đi nào được tạo.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($listTours as $tour): ?>
                                    <?php
                                    // --- 1. TÍNH TOÁN THỜI GIAN ---
                                    $currentTime = time();
                                    $startTime   = strtotime($tour['ngay_khoi_hanh']);
                                    $endTime     = strtotime($tour['ngay_ket_thuc']);

                                    // --- 2. CÁC CỜ KIỂM TRA TRẠNG THÁI ---

                                    // Check 1: Đã huỷ (Dựa vào DB)
                                    $isCancelled = ($tour['trang_thai'] === 'Huy');

                                    // Check 2: Đang đi (Hiện tại nằm giữa Khởi hành và Kết thúc)
                                    $isOngoing   = ($currentTime >= $startTime && $currentTime <= $endTime);

                                    // Check 3: Hoàn thành (Hiện tại lớn hơn ngày Kết thúc)
                                    $isFinished  = ($currentTime > $endTime);

                                    // Check 4: Đã đầy (Số chỗ đặt >= tối đa)
                                    $isFull      = ($tour['so_cho_da_dat'] >= $tour['so_cho_toi_da']);

                                    // --- 3. QUYẾT ĐỊNH HIỂN THỊ TRẠNG THÁI (Theo thứ tự ưu tiên) ---
                                    if ($isCancelled) {
                                        $badgeConfig = ['bg' => 'secondary', 'label' => 'Đã hủy'];
                                    } elseif ($isOngoing) {
                                        // Logic mới bạn yêu cầu
                                        $badgeConfig = ['bg' => 'primary', 'label' => 'Đang đi'];
                                        // Có thể thêm hiệu ứng nhấp nháy (class spinner) nếu muốn
                                    } elseif ($isFinished) {
                                        $badgeConfig = ['bg' => 'dark', 'label' => 'Hoàn thành'];
                                    } elseif ($isFull) {
                                        $badgeConfig = ['bg' => 'danger', 'label' => 'Đã đầy'];
                                    } else {
                                        // Trạng thái mặc định (thường là "Đang nhận khách")
                                        $badgeConfig = ['bg' => 'success', 'label' => 'Đang nhận khách'];
                                    }

                                    // --- 4. PHÂN QUYỀN HÀNH ĐỘNG ---

                                    // Được Sửa/Hủy khi: Chưa bắt đầu VÀ Chưa hủy
                                    // (Lưu ý: Đang đi ($isOngoing) cũng không được sửa/hủy để bảo toàn dữ liệu)
                                    $canEditOrCancel = ($currentTime < $startTime && !$isCancelled);

                                    // Được Xóa khi: Đã hoàn thành (xong tour) HOẶC Đã hủy
                                    // (Lưu ý: Đang đi không được xóa)
                                    $canDelete = ($isFinished || $isCancelled);
                                    ?>
                                    <tr>
                                        <td class="text-center fw-bold text-muted">#<?= $tour['id'] ?></td>

                                        <td>
                                            <strong><?php echo $tour['ten_tour']; ?></strong>
                                            <br>
                                            <span class="badge bg-info text-dark">
                                                <i class="far fa-clock"></i> <?php echo $tour['so_ngay']; ?>N <?php echo $tour['so_dem']; ?>Đ
                                            </span>
                                        </td>

                                        <td class="text-center small">
                                            <div><i class="fas fa-plane-departure text-primary"></i> <?php echo date('d/m/Y H:i', $startTime); ?></div>
                                            <div><i class="fas fa-plane-arrival text-success"></i> <?php echo date('d/m/Y H:i', $endTime); ?></div>
                                        </td>

                                        <td class="text-center">
                                            <?php if (!empty($tour['ten_hdv'])): ?>
                                                <span class="badge rounded-pill bg-success">
                                                    <i class="fas fa-user-tie"></i> <?= $tour['ten_hdv'] ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge rounded-pill bg-secondary">Chưa phân công</span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="text-center">
                                            <div class="fw-bold"><?= $tour['so_cho_da_dat'] ?> / <?= $tour['so_cho_toi_da'] ?></div>
                                            <div class="progress mt-1" style="height: 5px; width: 80px; margin: 0 auto;">
                                                <?php
                                                $percent = ($tour['so_cho_toi_da'] > 0) ? ($tour['so_cho_da_dat'] / $tour['so_cho_toi_da']) * 100 : 0;
                                                $colorBar = $percent >= 100 ? 'bg-danger' : 'bg-success';
                                                ?>
                                                <div class="progress-bar <?= $colorBar ?>" role="progressbar" style="width: <?= $percent ?>%"></div>
                                            </div>
                                        </td>

                                        <td class="text-center">
                                            <span class="badge bg-<?= $badgeConfig['bg'] ?>">
                                                <?php if ($isOngoing): ?> <i class="fas fa-plane"></i> <?php endif; ?>
                                                <?= $badgeConfig['label'] ?>
                                            </span>
                                        </td>

                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="<?= BASE_URL ?>routes/index.php?action=admin-lich-detail&id=<?= $tour['id'] ?>"
                                                    class="btn btn-sm btn-outline-info" title="Danh sách khách">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <?php if ($canEditOrCancel): ?>
                                                    <a href="<?= BASE_URL ?>routes/index.php?action=admin-schedule-staff&id=<?= $tour['id'] ?>"
                                                        class="btn btn-sm btn-outline-primary" title="Sửa & Phân bổ">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                    <a href="<?= BASE_URL ?>routes/index.php?action=admin-cancel-tour&id=<?= $tour['id'] ?>"
                                                        class="btn btn-sm btn-outline-warning text-dark" title="Hủy chuyến đi này"
                                                        onclick="return confirm('Bạn có chắc chắn muốn HỦY chuyến đi này?')">
                                                        <i class="fas fa-ban"></i> Hủy
                                                    </a>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-light text-muted" disabled style="cursor: not-allowed;" title="Không thể sửa/hủy khi tour đang đi hoặc đã kết thúc">
                                                        <i class="fas fa-lock"></i>
                                                    </button>
                                                <?php endif; ?>

                                                <?php if ($canDelete): ?>
                                                    <a href="<?= BASE_URL ?>routes/index.php?action=admin-delete-lich&id=<?= $tour['id'] ?>"
                                                        class="btn btn-sm btn-outline-danger" title="Xóa lịch sử dụng"
                                                        onclick="return confirm('CẢNH BÁO: Bạn đang xóa một lịch trình ĐÃ KẾT THÚC. Dữ liệu booking sẽ mất. Tiếp tục?')">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-secondary" disabled title="Chỉ xóa được tour đã kết thúc hoặc đã hủy">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white text-end">
                <small class="text-muted">Hệ thống quản lý tour du lịch.</small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>