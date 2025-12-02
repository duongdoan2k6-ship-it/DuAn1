<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="text-primary">Quản lý Booking</h4>
        <a href="/Booking/add" class="btn btn-primary">
            + Tạo booking mới
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center">ID</th>
                            <th>Tour du lịch</th>
                            <th>Khách hàng</th>
                            <th class="text-center">Số khách</th>
                            <th class="text-center">Ngày đặt</th>
                            <th class="text-center">Tổng tiền</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="text-center">Hành động</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if (isset($bookingList) && count($bookingList) > 0): ?>
                        
                        <?php foreach($bookingList as $item): ?>
                            <tr>
                                <td class="text-center fw-bold">#<?= $item['MaDatTour'] ?></td>

                                <td>
                                    <span class="text-primary fw-bold">
                                        <?= $item['TenTour'] ?? 'Tour theo lịch #' . ($item['MaLichKhoiHanh'] ?? '?') ?>
                                    </span>
                                </td>

                                <td>
                                    <b><?= $item['TenKhachHang'] ?></b><br>
                                    <small class="text-muted"><?= $item['LienHeKhachHang'] ?></small>
                                </td>

                                <td class="text-center"><?= $item['SoLuongKhach'] ?></td>

                                <td class="text-center">
                                    <?= date('d/m/Y H:i', strtotime($item['NgayDatTour'])) ?>
                                </td>

                                <td class="text-end text-danger fw-bold">
                                    <?= number_format($item['TongTien'], 0, ',', '.') ?> đ
                                </td>

                                <td class="text-center">
                                    <?php 
                                        $tt = $item['TrangThai'];
                                        if($tt == 1) echo '<span class="badge bg-warning text-dark">Mới đặt</span>';
                                        elseif($tt == 2) echo '<span class="badge bg-primary">Đã cọc</span>';
                                        elseif($tt == 3) echo '<span class="badge bg-success">Hoàn thành</span>';
                                        elseif($tt == 4) echo '<span class="badge bg-danger">Đã hủy</span>';
                                        else echo '<span class="badge bg-secondary">Không rõ</span>';
                                    ?>
                                </td>

                                <td class="text-center">
                                    <a href="#" class="btn btn-sm btn-outline-info">Sửa</a>
                                    <a href="#" class="btn btn-sm btn-outline-danger">Xóa</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                Chưa có dữ liệu booking nào!
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
                </div>
        </div>
    </div>
</div>