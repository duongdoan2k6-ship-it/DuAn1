<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="text-primary">Quản lý Booking</h4>

        <!-- Sửa đường dẫn -->
        <a href="index.php?action=add-booking" class="btn btn-primary">
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
                        <?php if (!empty($bookingList)): ?>

                            <?php foreach ($bookingList as $item): ?>
                                <tr>
                                    <td class="text-center fw-bold">#<?= $item['MaDatTour'] ?></td>

                                    <td>
                                        <span class="text-primary fw-bold">
                                            <?= $item['TenTour'] ?? 'Tour #' . $item['MaLichKhoiHanh'] ?>
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
                                        $tt = $item['MaTrangThai'];

                                        if ($tt == 1)
                                            echo '<span class="badge bg-warning text-dark">Chờ xác nhận</span>';
                                        elseif ($tt == 2)
                                            echo '<span class="badge bg-success">Đã xác nhận</span>';
                                        elseif ($tt == 3)
                                            echo '<span class="badge bg-danger">Đã hủy</span>';
                                        else
                                            echo '<span class="badge bg-secondary">Không rõ</span>';
                                        ?>
                                    </td>

                                    <td class="text-center">

                                        <!-- Mở trang sửa trạng thái booking -->
                                        <a href="index.php?action=edit-booking-status&id=<?= $item['MaDatTour'] ?>"
                                            class="btn btn-sm btn-outline-info">
                                            Cập nhật trạng thái
                                        </a>



                                        <!-- Xóa booking -->
                                        <a href="index.php?action=delete-booking&id=<?= $item['MaDatTour'] ?>"
                                            onclick="return confirm('Xóa booking này?');"
                                            class="btn btn-sm btn-outline-danger">
                                            Xóa
                                        </a>

                                        <a href="index.php?action=detail-booking&id=<?= $item['MaDatTour'] ?>"
                                            class="btn btn-sm btn-outline-primary">
                                            Xem
                                        </a>


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