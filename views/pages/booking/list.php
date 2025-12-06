<div class="card my-4">
    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
        <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center px-4">
            <h6 class="text-white text-capitalize mb-0">Quản Lý Booking Tour</h6>
            <a href="?action=add-booking" class="btn btn-sm btn-light text-primary font-weight-bold mb-0 shadow-sm">
                <i class="material-symbols-rounded text-sm me-1">Thêm Booking</i>
            </a>
        </div>
    </div>

    <div class="card-body px-0 pb-2">
        <div class="table-responsive">
            <table class="table table-hover align-items-center mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder">ID</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Khách Hàng</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Lịch Trình</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">SL Khách</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Tổng Tiền</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Trạng Thái</th>
                        <th class="text-secondary"></th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($bookingList as $item): ?>
                        <tr>
                            <!-- ID -->
                            <td class="px-3 py-3">
                                <div class="d-flex flex-column">
                                    <h6 class="mb-0 text-sm fw-bold">#<?= $item['MaDatTour'] ?></h6>
                                    <p class="text-xs text-secondary mb-0"><?= date('d/m/Y', strtotime($item['NgayDatTour'])) ?></p>
                                </div>
                            </td>

                            <!-- Khách -->
                            <td>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-0 text-sm fw-bold"><?= $item['TenKhachHang'] ?></h6>
                                    <p class="text-xs text-secondary mb-0"><?= $item['LienHeKhachHang'] ?></p>
                                </div>
                            </td>

                            <!-- Lịch trình -->
                            <td class="align-middle text-center">
                                <span class="text-secondary text-sm">
                                    <?= date('d/m', strtotime($item['NgayKhoiHanh'])) ?> - <?= date('d/m/Y', strtotime($item['NgayKetThuc'])) ?>
                                </span>
                            </td>

                            <!-- Số lượng khách -->
                            <td class="align-middle text-center">
                                <span class="badge bg-secondary text-white"><?= $item['SoLuongKhach'] ?></span>
                            </td>

                            <!-- Tổng tiền -->
                            <td class="align-middle text-center">
                                <span class="text-danger fw-bold">
                                    <?= number_format($item['TongTien'], 0, ',', '.') ?> VNĐ
                                </span>
                            </td>

                            <!-- Trạng thái -->
                            <td class="align-middle text-center">
                                <?php
                                $badgeClass = 'bg-gradient-secondary';

                                if (strpos($item['TenTrangThai'], 'Đã xác nhận') !== false) {
                                    $badgeClass = 'bg-gradient-success';
                                } elseif (strpos($item['TenTrangThai'], 'Chờ xác nhận') !== false) {
                                    $badgeClass = 'bg-gradient-warning';
                                } elseif (strpos($item['TenTrangThai'], 'Đã hủy') !== false) {
                                    $badgeClass = 'bg-gradient-danger';
                                }
                                ?>
                                <span class="badge badge-sm text-white <?= $badgeClass ?>">
                                    <?= $item['TenTrangThai'] ?>
                                </span>
                            </td>

                            <td class="align-middle text-center">
                                <a href="?action=detail-booking&id=<?= $item['MaDatTour'] ?>" class="btn btn-sm bg-gradient-info text-white rounded-pill px-3 py-1 me-2 shadow-sm" title="Chi tiết">
                                    <i class="material-symbols-rounded text-sm me-1">Chi tiết</i></a>

                                <a href="?action=edit-booking&id=<?= $item['MaDatTour'] ?>" class="btn btn-sm bg-gradient-dark text-white rounded-pill px-3 py-1 me-2 shadow-sm" title="Sửa">
                                    <i class="material-symbols-rounded text-sm me-1">Sửa</i> 
                                </a>

                                <?php if (strpos($item['TenTrangThai'], 'Hủy') === false): ?>
                                    <a href="?action=cancel-booking&id=<?= $item['MaDatTour'] ?>"
                                        onclick="return confirm('Bạn có chắc chắn muốn HUỶ booking này không? Dữ liệu sẽ được lưu lại với trạng thái Đã Huỷ.');"
                                        class="btn btn-sm bg-gradient-warning text-white rounded-pill px-3 py-1 me-2 shadow-sm"
                                        title="Huỷ Booking">
                                        <i class="material-symbols-rounded text-sm me-1">Huỷ</i> 
                                    </a>
                                <?php endif; ?>

                                <a href="?action=delete-booking&id=<?= $item['MaDatTour'] ?>"
                                    onclick="return confirm('CẢNH BÁO: Bạn có chắc chắn muốn XÓA VĨNH VIỄN booking này không? Hành động này không thể hoàn tác!');"
                                    class="btn btn-sm bg-gradient-danger text-white rounded-pill px-3 py-1 shadow-sm"
                                    title="Xóa vĩnh viễn">
                                    <i class="material-symbols-rounded text-sm me-1">Xóa</i> 
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>
        </div>
    </div>
</div>

<?php if (isset($_SESSION['alert_message'])): ?>
    <script>
        alert("<?= $_SESSION['alert_message'] ?>");
    </script>

    <?php
    unset($_SESSION['alert_message']);
    unset($_SESSION['alert_type']);
    ?>
<?php endif; ?>