<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Đặt Tour</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="<?= BASE_URL ?>routes/index.php?action=admin-dashboard">ADMIN PANEL</a>
            <div class="d-flex">
                <span class="navbar-text me-3 text-white">Admin: <?= $_SESSION['user']['ho_ten'] ?? 'Quản trị viên' ?></span>
                <a href="<?= BASE_URL ?>routes/index.php?action=logout" class="btn btn-outline-light btn-sm">Đăng xuất</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="text-primary fw-bold">📦 Quản Lý Đơn Đặt Tour</h4>
            
            <div>
                <a href="<?= BASE_URL ?>routes/index.php?action=admin-booking-create" class="btn btn-success btn-sm me-2">
                    <i class="fas fa-plus"></i> Tạo Booking Mới
                </a>

                <a href="<?= BASE_URL ?>routes/index.php?action=admin-dashboard" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Về Dashboard
                </a>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-success">
                            <tr>
                                <th>Mã Đơn</th>
                                <th>Khách Hàng</th>
                                <th>Tour Đặt</th>
                                <th>Tổng Tiền</th>
                                <th>Ngày Đặt</th>
                                <th>Trạng Thái</th>
                                <th class="text-center">Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookings as $b): ?>
                            <tr>
                                <td class="fw-bold">#<?= $b['id'] ?></td>
                                
                                <td>
                                    <div class="fw-bold"><?= $b['ten_nguoi_dat'] ?></div>
                                    <small class="text-muted"><?= $b['sdt_lien_he'] ?></small>
                                </td>

                                <td style="max-width: 250px;">
                                    <small class="fw-bold text-primary"><?= $b['ten_tour'] ?></small><br>
                                    <small class="text-muted">KH: <?= date('d/m/Y', strtotime($b['ngay_khoi_hanh'])) ?></small>
                                </td>

                                <td class="fw-bold text-danger">
                                    <?= number_format($b['tong_tien'], 0, ',', '.') ?>đ
                                </td>

                                <td><?= date('d/m/Y H:i', strtotime($b['ngay_dat'])) ?></td>

                                <td>
                                    <?php 
                                        $colors = [
                                            'ChoXacNhan'  => ['warning', 'Chờ xác nhận'],
                                            'DaXacNhan'   => ['info', 'Đã xác nhận'],
                                            'DaThanhToan' => ['success', 'Đã thanh toán'],
                                            'Huy'         => ['secondary', 'Đã hủy']
                                        ];
                                        $stt = $colors[$b['trang_thai']] ?? ['dark', $b['trang_thai']];
                                    ?>
                                    <span class="badge bg-<?= $stt[0] ?>"><?= $stt[1] ?></span>
                                </td>

                                <td class="text-center">
                                    <a href="<?= BASE_URL ?>routes/index.php?action=admin-booking-detail&id=<?= $b['id'] ?>" 
                                       class="btn btn-sm btn-info text-white me-1" title="Xem chi tiết & Lịch sử">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <?php if ($b['trang_thai'] === 'ChoXacNhan'): ?>
                                        <a href="<?= BASE_URL ?>routes/index.php?action=booking-status&id=<?= $b['id'] ?>&status=DaXacNhan" 
                                           class="btn btn-sm btn-success me-1" title="Xác nhận">
                                           <i class="fas fa-check"></i>
                                        </a>
                                        <a href="<?= BASE_URL ?>routes/index.php?action=booking-status&id=<?= $b['id'] ?>&status=Huy" 
                                           class="btn btn-sm btn-danger" title="Hủy đơn"
                                           onclick="return confirm('Hủy đơn này?')">
                                           <i class="fas fa-times"></i>
                                        </a>

                                    <?php elseif ($b['trang_thai'] === 'DaXacNhan'): ?>
                                        <a href="<?= BASE_URL ?>routes/index.php?action=booking-status&id=<?= $b['id'] ?>&status=DaThanhToan" 
                                           class="btn btn-sm btn-primary me-1" title="Xác nhận thanh toán">
                                           <i class="fas fa-dollar-sign"></i> Thu tiền
                                        </a>
                                        <a href="<?= BASE_URL ?>routes/index.php?action=booking-status&id=<?= $b['id'] ?>&status=Huy" 
                                           class="btn btn-sm btn-danger" title="Hủy đơn"
                                           onclick="return confirm('Khách đã xác nhận nhưng muốn hủy? Hủy đơn này sẽ trả lại chỗ trống.')">
                                           <i class="fas fa-times"></i>
                                        </a>

                                    <?php elseif ($b['trang_thai'] === 'Huy'): ?>
                                        <a href="<?= BASE_URL ?>routes/index.php?action=booking-status&id=<?= $b['id'] ?>&status=ChoXacNhan" 
                                           class="btn btn-sm btn-warning text-dark" title="Khôi phục đơn hàng"
                                           onclick="return confirm('Khôi phục đơn hàng này? Hệ thống sẽ kiểm tra xem Tour còn chỗ trống không.')">
                                           <i class="fas fa-undo"></i> Khôi phục
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>