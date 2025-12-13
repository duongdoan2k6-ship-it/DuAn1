<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi Tiết Chuyến Đi #<?= $lich['id'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">
<div class="container mt-4 mb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-primary mb-0">
                <i class="fas fa-bus-alt"></i> Chi Tiết Chuyến Đi #<?= $lich['id'] ?>
            </h4>
            <span class="text-muted"><?= $lich['ten_tour'] ?></span>
        </div>
        <div>
            <a href="<?= BASE_URL ?>routes/index.php?action=admin-schedule-staff&id=<?= $lich['id'] ?>" class="btn btn-warning">
                <i class="fas fa-edit"></i> Sửa & Phân Công
            </a>
            <a href="<?= BASE_URL ?>routes/index.php?action=admin-dashboard" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white fw-bold">
                    <i class="fas fa-info-circle"></i> Thông Tin Chung
                </div>
                <div class="card-body">
                    <p><strong>Ngày đi:</strong> <?= date('d/m/Y H:i', strtotime($lich['ngay_khoi_hanh'])) ?></p>
                    <p><strong>Ngày về:</strong> <?= date('d/m/Y H:i', strtotime($lich['ngay_ket_thuc'])) ?></p>
                    <p><strong>Điểm đón:</strong> <?= $lich['diem_tap_trung'] ?></p>
                    <p><strong>Số chỗ:</strong> <span class="fw-bold"><?= $lich['so_cho_da_dat'] ?></span> / <?= $lich['so_cho_toi_da'] ?></p>
                    <p><strong>Trạng thái:</strong> <span class="badge bg-primary"><?= $lich['trang_thai'] ?></span></p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white fw-bold">
                    <i class="fas fa-user-tie"></i> Đội Ngũ Nhân Sự
                </div>
                <ul class="list-group list-group-flush">
                    <?php if(empty($staff)): ?>
                        <li class="list-group-item text-muted fst-italic">Chưa phân công nhân sự.</li>
                    <?php else: ?>
                        <?php foreach($staff as $s): ?>
                            <li class="list-group-item">
                                <strong><?= $s['ho_ten'] ?></strong>
                                <br>
                                <small class="text-muted">
                                    <?= $s['vai_tro'] == 'HDV_chinh' ? '⭐ HDV Trưởng' : ($s['vai_tro'] == 'TaiXe' ? '🚌 Tài Xế' : 'User') ?>
                                    - <?= $s['sdt'] ?>
                                </small>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <div class="col-md-8">
            
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active fw-bold" id="booking-tab" data-bs-toggle="tab" data-bs-target="#booking" type="button">
                        <i class="fas fa-file-invoice"></i> Danh Sách Booking (<?= count($bookings) ?>)
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-bold" id="guest-tab" data-bs-toggle="tab" data-bs-target="#guest" type="button">
                        <i class="fas fa-users"></i> Danh Sách Hành Khách (<?= count($passengers) ?>)
                    </button>
                </li>
            </ul>

            <div class="tab-content bg-white border border-top-0 p-3 shadow-sm rounded-bottom" id="myTabContent">
                
                <div class="tab-pane fade show active" id="booking">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã Booking</th>
                                    <th>Người Đặt</th>
                                    <th>Số Lượng</th>
                                    <th>Tổng Tiền</th>
                                    <th>Trạng Thái</th>
                                    <th>Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($bookings)): ?>
                                    <tr><td colspan="6" class="text-center py-3">Chưa có booking nào.</td></tr>
                                <?php else: ?>
                                    <?php foreach($bookings as $b): ?>
                                        <tr>
                                            <td class="fw-bold">#<?= $b['id'] ?></td>
                                            <td>
                                                <?= $b['ten_nguoi_dat'] ?><br>
                                                <small class="text-muted"><?= $b['sdt_lien_he'] ?></small>
                                            </td>
                                            <td><?= $b['so_nguoi_lon'] + $b['so_tre_em'] ?> khách</td>
                                            <td class="text-danger fw-bold"><?= number_format($b['tong_tien']) ?> đ</td>
                                            <td><span class="badge bg-secondary"><?= $b['trang_thai'] ?></span></td>
                                            <td>
                                                <a href="<?= BASE_URL ?>routes/index.php?action=admin-booking-detail&id=<?= $b['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                    Xem
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="guest">
                    <div class="d-flex justify-content-end mb-2">
                        <button class="btn btn-sm btn-success" onclick="window.print()"><i class="fas fa-print"></i> In Danh Sách</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-success">
                                <tr>
                                    <th>STT</th>
                                    <th>Họ Tên Khách</th>
                                    <th>Loại</th>
                                    <th>Giới Tính</th>
                                    <th>Ngày Sinh</th>
                                    <th>Ghi Chú</th>
                                    <th>Thuộc Booking</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($passengers)): ?>
                                    <tr><td colspan="7" class="text-center py-3">Chưa có hành khách nào.</td></tr>
                                <?php else: ?>
                                    <?php foreach($passengers as $key => $p): ?>
                                        <tr>
                                            <td class="text-center"><?= $key + 1 ?></td>
                                            <td class="fw-bold text-uppercase"><?= $p['ho_ten_khach'] ?></td>
                                            <td><?= $p['loai_khach'] == 'NguoiLon' ? 'Người lớn' : 'Trẻ em' ?></td>
                                            <td><?= $p['gioi_tinh'] ?></td>
                                            <td><?= $p['ngay_sinh'] ? date('d/m/Y', strtotime($p['ngay_sinh'])) : '' ?></td>
                                            <td><?= $p['ghi_chu_dac_biet'] ?></td>
                                            <td><small>#<?= $p['booking_id'] ?> (<?= $p['ten_nguoi_dat'] ?>)</small></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>