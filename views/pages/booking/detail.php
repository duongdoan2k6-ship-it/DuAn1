<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center px-4">
                        <h6 class="text-white text-capitalize mb-0">Chi tiết Booking #<?= $booking['MaDatTour'] ?></h6>
                        <a href="index.php?action=list-booking" class="btn btn-sm btn-light mb-0">Quay lại</a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 border-end">
                            <h5 class="mb-4 text-info"><i class="material-symbols-rounded"></i> Thông tin Khách hàng</h5>

                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="fw-bold">Họ và tên:</span>
                                    <span><?= $booking['TenKhachHang'] ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="fw-bold">Liên hệ (SĐT/Email):</span>
                                    <span><?= $booking['LienHeKhachHang'] ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="fw-bold">Ngày đặt tour:</span>
                                    <span><?= date('H:i - d/m/Y', strtotime($booking['NgayDatTour'])) ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="fw-bold">Trạng thái hiện tại:</span>
                                    <?php
                                    $badgeClass = 'bg-secondary';
                                    if ($booking['MaTrangThai'] == 1) $badgeClass = 'bg-warning';
                                    elseif ($booking['MaTrangThai'] == 2) $badgeClass = 'bg-success';
                                    elseif ($booking['MaTrangThai'] == 3) $badgeClass = 'bg-danger';
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= $booking['TenTrangThai'] ?></span>
                                </li>
                            </ul>

                            <?php if (!empty($booking['GhiChu'])): ?>
                                <div class="mt-3 p-3 bg-light rounded border">
                                    <small class="fw-bold text-secondary">Ghi chú của khách:</small>
                                    <p class="mb-0 text-sm"><?= $booking['GhiChu'] ?></p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <h5 class="mb-4 text-info"><i class="material-symbols-rounded"></i> Thông tin Lịch trình</h5>

                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="fw-bold">Mã Lịch Khởi Hành:</span>
                                    <span>#<?= $booking['MaLichKhoiHanh'] ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="fw-bold">Ngày đi - Về:</span>
                                    <span>
                                        <?= date('d/m/Y', strtotime($booking['NgayKhoiHanh'])) ?>
                                        <i class="material-symbols-rounded text-xs mx-1"></i>
                                        <?= date('d/m/Y', strtotime($booking['NgayKetThuc'])) ?>
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="fw-bold">Số lượng khách:</span>
                                    <span class="fw-bold text-dark"><?= $booking['SoLuongKhach'] ?> người</span>
                                </li>
                            </ul>

                            <hr class="dark horizontal my-4">

                            <h5 class="mb-3 text-info"><i class="material-symbols-rounded"></i> Chi tiết Thanh toán</h5>
                            <div class="d-flex justify-content-between align-items-center p-3 border rounded bg-light">
                                <span class="fw-bold text-dark">Tổng tiền phải thu:</span>
                                <h4 class="mb-0 text-danger fw-bolder"><?= number_format($booking['TongTien'], 0, ',', '.') ?> VNĐ</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>