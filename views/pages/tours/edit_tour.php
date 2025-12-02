<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-capitalize ps-3"><?php echo $pageTitle; ?></h6>
                </div>
            </div>

            <div class="card-body px-4 pb-2">

                <form action="index.php?action=edit-tour&id=<?= $tour['MaTour'] ?>" method="POST">




                    <div class="row">
                        <div class="col-md-6">

                            <div class="input-group input-group-outline my-3 is-filled">
                                <label class="form-label">Tên tour</label>
                                <input type="text" name="ten_tour" class="form-control"
                                    value="<?= htmlspecialchars($tour['TenTour']) ?>" required>
                            </div>



                            <div class="input-group input-group-outline my-3 is-filled">
                                <label class="form-label">Loại tour</label>
                                <select name="ma_loai_tour" class="form-control" required>
                                    <option value="">-- Chọn loại tour --</option>
                                    <?php if (!empty($dsLoaiTour)): ?>
                                        <?php foreach ($dsLoaiTour as $loai): ?>
                                            <option value="<?= $loai['MaLoaiTour'] ?>"
                                                <?= $tour['MaLoaiTour'] == $loai['MaLoaiTour'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($loai['TenLoai']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>


                    </div>

                    <div class="input-group input-group-outline my-3  is-filled">
                        <label class="form-label">Trạng thái</label>
                        <select name="trang_thai" class="form-control" required>
                            <option value="1" <?= $tour['TrangThai'] == 1 ? 'selected' : '' ?>> Hoạt động</option>
                            <option value="0" <?= $tour['TrangThai'] == 0 ? 'selected' : '' ?>> Tạm dừng</option>
                        </select>
                    </div>


                    <div class="input-group input-group-outline my-3 is-filled">
                        <label class="form-label">Giá</label>
                        <input type="number" name="gia_tour" class="form-control"
                            value="<?= htmlspecialchars($tour['GiaTour']) ?>" required min="0">
                    </div>

                    <div class="input-group input-group-outline my-3  is-filled">
                        <label class="form-label">Địa điểm khởi hành</label>
                        <input type="text" name="dia_diem" class="form-control"
                            value="<?= htmlspecialchars($tour['DiaDiemKhoiHanh']) ?>">
                    </div>

                    <div class="input-group input-group-outline my-3 is-filled">
                        <textarea name="mo_ta" class="form-control" rows="5" placeholder="Mô tả chi tiết lịch trình..."><?= htmlspecialchars($tour['MoTa']) ?></textarea>
                    </div>

                    <div class="d-flex justify-content-end mt-4 mb-3  is-filled">
                        <a href="index.php?action=list-tours" class="btn btn-outline-secondary me-2">Hủy bỏ</a>
                        <button type="submit" class="btn bg-gradient-primary">Lưu Tour</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>