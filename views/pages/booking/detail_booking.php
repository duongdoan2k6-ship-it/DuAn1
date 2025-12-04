<div class="container mt-4">

    <h3 class="text-primary mb-3">Chi tiết Booking #<?= $booking['MaDatTour'] ?></h3>

    <div class="card shadow-sm">
        <div class="card-body">

            <h5 class="mb-3"><?= $booking['TenTour'] ?></h5>

            <p><b>Khách hàng:</b> <?= $booking['TenKhachHang'] ?></p>
            <p><b>Liên hệ:</b> <?= $booking['LienHeKhachHang'] ?></p>
            <p><b>Số khách:</b> <?= $booking['SoLuongKhach'] ?></p>
            <p><b>Ngày đặt:</b> <?= $booking['NgayDatTour'] ?></p>
            <p><b>Tổng tiền:</b> <span class="text-danger fw-bold"><?= number_format($booking['TongTien']) ?> đ</span></p>
            <p><b>Trạng thái:</b> <span class="badge bg-primary"><?= $booking['TenTrangThai'] ?></span></p>
            <p><b>Ghi chú:</b> <?= $booking['GhiChu'] ?></p>

        </div>
    </div>

    <h4 class="mt-4">Lịch sử thay đổi trạng thái</h4>

    <div class="card shadow-sm mt-2">
        <div class="card-body">

            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Thời gian</th>
                        <th>Trạng thái cũ</th>
                        <th>Trạng thái mới</th>
                        <th>Nhân viên</th>
                        <th>Ghi chú</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($logs as $lg): ?>
                        <tr>
                            <td><?= $lg['ThoiGian'] ?></td>
                            <td><?= $lg['TrangThaiCu'] ?></td>
                            <td><?= $lg['TrangThaiMoi'] ?></td>
                            <td><?= $lg['NhanVienThayDoi'] ?></td>
                            <td><?= $lg['GhiChu'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>

        </div>
    </div>

</div>
