<div class="container mt-4">
    <h3 class="text-primary">Cập nhật trạng thái Booking #<?= $booking['MaDatTour'] ?></h3>

    <div class="card shadow-sm mt-3">
        <div class="card-body">

            <form method="POST">

                <div class="mb-3">
                    <label class="form-label">Trạng thái hiện tại:</label><br>
                    <span class="badge bg-info"><?= $booking['TenTrangThai'] ?></span>
                </div>

                <div class="mb-3">
                    <label class="form-label">Trạng thái mới</label>
                    <select name="trang_thai" class="form-select" required>
                        <?php foreach ($statusList as $st): ?>
                            <option value="<?= $st['MaTrangThai'] ?>"><?= $st['TenTrangThai'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ghi chú</label>
                    <textarea name="ghi_chu" class="form-control"></textarea>
                </div>

                <button class="btn btn-primary">Cập nhật</button>

            </form>

        </div>
    </div>
</div>
