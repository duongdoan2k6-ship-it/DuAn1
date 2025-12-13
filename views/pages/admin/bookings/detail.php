<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi Tiết Booking #<?= $booking['id'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="text-white mb-0"><i class="fas fa-info-circle"></i> Thông tin đơn hàng #<?= $booking['id'] ?></h5>
                    <span class="badge bg-light text-primary fw-bold"><?= $booking['trang_thai'] ?></span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong><i class="fas fa-user"></i> Người đặt:</strong> <?= $booking['ten_nguoi_dat'] ?></p>
                            <p><strong><i class="fas fa-phone"></i> SĐT:</strong> <?= $booking['sdt_lien_he'] ?></p>
                            <p><strong><i class="fas fa-envelope"></i> Email:</strong> <?= $booking['email_lien_he'] ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong><i class="fas fa-map-marker-alt"></i> Tour:</strong> <?= $booking['ten_tour'] ?></p>
                            <p><strong><i class="far fa-calendar-alt"></i> Ngày đi:</strong> <?= date('d/m/Y H:i', strtotime($booking['ngay_khoi_hanh'])) ?></p>
                            <p><strong><i class="fas fa-users"></i> Số lượng:</strong> <?= $booking['so_nguoi_lon'] ?> Lớn - <?= $booking['so_tre_em'] ?> Trẻ</p>
                        </div>
                    </div>
                    <div class="alert alert-warning mb-0">
                        <strong><i class="fas fa-money-bill-wave"></i> Tổng tiền:</strong> 
                        <span class="text-danger fw-bold fs-5"><?= number_format($booking['tong_tien']) ?> VNĐ</span>
                    </div>
                    <?php if(!empty($booking['ghi_chu'])): ?>
                        <div class="mt-3 p-2 bg-light border rounded">
                            <strong>Ghi chú:</strong> <?= $booking['ghi_chu'] ?>
                        </div>
                    <?php endif; ?>
                    
                    </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-users"></i> Danh sách thành viên đoàn</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Họ Tên</th>
                                <th>Loại</th>
                                <th>Giới tính</th>
                                <th>Ngày sinh</th>
                                <th>Ghi chú</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($guests)): ?>
                                <?php foreach ($guests as $i => $guest): ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td class="fw-bold"><?= $guest['ho_ten_khach'] ?></td>
                                        <td>
                                            <?php if ($guest['loai_khach'] == 'NguoiLon'): ?>
                                                <span class="badge bg-primary">Người lớn</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">Trẻ em</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $guest['gioi_tinh'] ?></td>
                                        <td><?= $guest['ngay_sinh'] ? date('d/m/Y', strtotime($guest['ngay_sinh'])) : '-' ?></td>
                                        <td class="text-muted small"><?= $guest['ghi_chu_dac_biet'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-3 text-muted">Chưa có thông tin danh sách đoàn.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h6 class="text-white mb-0"><i class="fas fa-history"></i> Lịch sử xử lý</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php if (empty($history)): ?>
                            <li class="list-group-item text-center py-3">Chưa có lịch sử thay đổi</li>
                        <?php else: ?>
                            <?php foreach ($history as $log): ?>
                            <li class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <small class="text-muted"><?= date('H:i d/m/Y', strtotime($log['thoi_gian'])) ?></small>
                                    <small class="fw-bold text-primary"><?= $log['nguoi_thay_doi'] ?></small>
                                </div>
                                <div class="mt-1">
                                    <span class="badge bg-light text-dark border"><?= $log['trang_thai_cu'] ?></span>
                                    <i class="fas fa-arrow-right text-muted mx-1" style="font-size: 0.8rem;"></i>
                                    <span class="badge bg-info text-dark"><?= $log['trang_thai_moi'] ?></span>
                                </div>
                                <?php if($log['ghi_chu_thay_doi']): ?>
                                    <small class="text-muted d-block mt-1 fst-italic border-top pt-1">
                                        Note: <?= $log['ghi_chu_thay_doi'] ?>
                                    </small>
                                <?php endif; ?>
                            </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-3 pb-5">
        <a href="<?= BASE_URL ?>routes/index.php?action=admin-bookings" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>