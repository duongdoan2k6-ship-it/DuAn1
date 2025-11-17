<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-capitalize ps-3"><?php echo $pageTitle; ?></h6>
                </div>
            </div>

            <div class="card-body px-4 pb-2">
                
                <form action="index.php?action=add-tour" method="POST">
                    
                    <div class="input-group input-group-outline my-3">
                        <label class="form-label">Tên Tour</label>
                        <input type="text" name="ten_tour" class="form-control" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group input-group-outline my-3 is-filled">
                                <label class="form-label">Loại Tour</label>
                                <select name="ma_loai_tour" class="form-control" required>
                                    <option value="">-- Chọn loại tour --</option>
                                    <?php if (!empty($dsLoaiTour)): ?>
                                        <?php foreach ($dsLoaiTour as $loai): ?>
                                            <option value="<?= $loai['MaLoaiTour'] ?>">
                                                <?= htmlspecialchars($loai['TenLoai']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="input-group input-group-outline my-3">
                                <label class="form-label">Thời lượng (Ví dụ: 3 ngày 2 đêm)</label>
                                <input type="text" name="thoi_luong" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="input-group input-group-outline my-3">
                        <label class="form-label">Địa điểm khởi hành</label>
                        <input type="text" name="dia_diem" class="form-control">
                    </div>

                    <div class="input-group input-group-outline my-3">
                        <textarea name="mo_ta" class="form-control" rows="5" placeholder="Mô tả chi tiết lịch trình..."></textarea>
                    </div>

                    <div class="d-flex justify-content-end mt-4 mb-3">
                        <a href="index.php?action=list-tours" class="btn btn-outline-secondary me-2">Hủy bỏ</a>
                        <button type="submit" class="btn bg-gradient-primary">Lưu Tour</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>  