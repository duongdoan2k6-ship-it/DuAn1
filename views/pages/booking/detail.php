<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-info shadow-info border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center px-4">
                        <h6 class="text-white text-capitalize mb-0">Chi tiết Booking <?= $booking['MaDatTour'] ?></h6>
                        <div>
                            <a href="?action=edit-booking&id=<?= $booking['MaDatTour'] ?>" class="btn btn-sm btn-light text-info fw-bold mb-0 me-2">
                                <i class="material-symbols-rounded text-sm me-1">Sửa</i>
                            </a>
                            <a href="?action=list-booking" class="btn btn-sm btn-light text-dark fw-bold mb-0">
                                <i class="material-symbols-rounded text-sm me-1">Quay lại</i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-12 text-center">
                            <span class="text-uppercase text-secondary text-xs font-weight-bolder">Trạng thái hiện tại</span>
                            <div class="mt-2">
                                <?php
                                $badgeClass = 'bg-secondary';
                                if ($booking['MaTrangThai'] == 1) $badgeClass = 'bg-gradient-warning';
                                elseif ($booking['MaTrangThai'] == 2) $badgeClass = 'bg-gradient-success';
                                elseif ($booking['MaTrangThai'] == 3) $badgeClass = 'bg-gradient-danger';
                                ?>
                                <span class="badge <?= $badgeClass ?> badge-lg p-2 fs-6">
                                    <?= $booking['TenTrangThai'] ?>
                                </span>
                            </div>
                            <p class="text-xs text-secondary mt-1">
                                Ngày tạo đơn: <?= date('H:i:s - d/m/Y', strtotime($booking['NgayDatTour'])) ?>
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 border-end">
                            <h5 class="mb-3 text-info border-bottom pb-2">
                                <i class="material-symbols-rounded align-middle me-1">Thông tin Khách hàng</i>
                            </h5>

                            <div class="p-3 bg-light rounded mb-3">
                                <div class="row mb-2">
                                    <div class="col-4 fw-bold text-secondary">Họ và tên:</div>
                                    <div class="col-8 fw-bold text-dark fs-5"><?= $booking['TenKhachHang'] ?></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4 fw-bold text-secondary">Liên hệ:</div>
                                    <div class="col-8"><?= $booking['LienHeKhachHang'] ?></div>
                                </div>
                            </div>

                            <h6 class="mb-2 text-dark font-weight-bold">Ghi chú từ khách hàng:</h6>
                            <div class="p-3 border rounded bg-white" style="min-height: 100px;">
                                <?php if (!empty($booking['GhiChu'])): ?>
                                    <p class="mb-0 text-sm fst-italic text-dark"><?= nl2br($booking['GhiChu']) ?></p>
                                <?php else: ?>
                                    <span class="text-muted text-xs">Không có ghi chú nào.</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-md-6 ps-md-4">
                            <h5 class="mb-3 text-info border-bottom pb-2">
                                <i class="material-symbols-rounded align-middle me-1">Chi tiết Tour & Lịch trình</i>
                            </h5>

                            <div class="mb-3">
                                <label class="text-uppercase text-secondary text-xs font-weight-bolder mb-0">Tên Tour Du Lịch</label>
                                <p class="text-dark fw-bold fs-5 mb-1">
                                    <?= $booking['TenTour'] ?? 'Chưa cập nhật tên tour' ?>
                                </p>
                                <span class="badge bg-light text-dark border">Mã Lịch: #<?= $booking['MaLichKhoiHanh'] ?></span>
                            </div>

                            <div class="row mb-3">
                                <div class="col-6">
                                    <div class="p-2 border rounded text-center">
                                        <label class="d-block text-secondary text-xs mb-1">Ngày Khởi Hành</label>
                                        <span class="fw-bold text-primary">
                                            <?= date('d/m/Y', strtotime($booking['NgayKhoiHanh'])) ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-2 border rounded text-center">
                                        <label class="d-block text-secondary text-xs mb-1">Ngày Kết Thúc</label>
                                        <span class="fw-bold text-primary">
                                            <?= date('d/m/Y', strtotime($booking['NgayKetThuc'])) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <hr class="dark horizontal my-4">

                            <h5 class="mb-3 text-info border-bottom pb-2">
                                <i class="material-symbols-rounded align-middle me-1">Chi tiết Thanh toán</i>
                            </h5>

                            <ul class="list-group list-group-flush mb-3">
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>Giá người lớn:</span>
                                    <span class="fw-bold"><?= number_format($booking['GiaNguoiLon']) ?> đ</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>Giá trẻ em:</span>
                                    <span class="fw-bold"><?= number_format($booking['GiaTreEm']) ?> đ</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0 bg-light p-2 mt-2 rounded">
                                    <span class="fw-bold text-dark">Tổng số khách:</span>
                                    <span class="fw-bold text-dark"><?= $booking['SoLuongKhach'] ?> người</span>
                                </li>
                            </ul>

                            <div class="d-flex justify-content-between align-items-center p-3 border border-2 border-danger rounded bg-white shadow-sm">
                                <div>
                                    <span class="d-block text-secondary text-xs font-weight-bold text-uppercase">Tổng tiền thanh toán</span>
                                </div>
                                <h3 class="mb-0 text-danger fw-bolder">
                                    <?= number_format($booking['TongTien'], 0, ',', '.') ?> VNĐ
                                </h3>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="card-footer p-3">
                    <?php if ($booking['MaTrangThai'] == 1): ?>
                        <div class="alert alert-warning text-white text-center mb-0" role="alert">
                            <i class="material-symbols-rounded align-middle text-lg">Đơn hàng đang chờ xác nhận</i>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</div>