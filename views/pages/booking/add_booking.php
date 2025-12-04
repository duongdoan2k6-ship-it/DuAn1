<div class="container mt-4">
    <h3 class="mb-3 text-primary">Tạo Booking Mới</h3>

    <div class="card shadow-sm">
        <div class="card-body">

            <form method="POST">

                <div class="mb-3">
                    <label class="form-label">Chọn Tour / Lịch khởi hành</label>
                    <select name="ma_lich" class="form-select" required>
                        <option value="">-- Chọn lịch khởi hành --</option>
                        <?php foreach ($lichList as $l): ?>
                            <option value="<?= $l['MaLichKhoiHanh'] ?>">
                                <?= $l['TenTour'] ?> - 
                                <?= date('d/m/Y', strtotime($l['NgayKhoiHanh'])) ?> 
                                (Còn <?= $l['ConLai'] ?> chỗ)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tên khách hàng</label>
                    <input type="text" name="ten_kh" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Liên hệ</label>
                    <input type="text" name="lien_he" class="form-control" required>
                </div>

                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label">Số lượng khách</label>
                        <input type="number" name="so_khach" min="1" class="form-control" required>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label">Tổng tiền</label>
                        <input type="number" name="tong_tien" class="form-control" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ghi chú</label>
                    <textarea name="ghi_chu" class="form-control"></textarea>
                </div>

                <button class="btn btn-primary px-4">Lưu Booking</button>

            </form>

        </div>
    </div>
</div>
