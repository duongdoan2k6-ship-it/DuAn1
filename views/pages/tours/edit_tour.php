<div class="row">
    <div class="col-12">
        <div class="card my-4">

            <!-- Header -->
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-capitalize ps-3"><?= $pageTitle ?></h6>
                </div>
            </div>

            <div class="card-body px-4 pb-2">

                <form action="index.php?action=edit-tour&id=<?= $tour['MaTour'] ?>" method="POST" enctype="multipart/form-data">

                    <!-- Tên tour -->
                    <div class="input-group input-group-outline my-3 <?= !empty($tour['TenTour']) ? 'is-filled' : '' ?>">
                        <label class="form-label">Tên Tour</label>
                        <input type="text" name="ten_tour" class="form-control" 
                               value="<?= htmlspecialchars($tour['TenTour']) ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <!-- Loại tour -->
                            <div class="input-group input-group-outline my-3 is-filled">
                                <label class="form-label">Loại Tour</label>
                                <select name="ma_loai_tour" class="form-control" required>
                                    <option value="">-- Chọn loại tour --</option>
                                    <?php foreach ($dsLoaiTour as $loai): ?>
                                        <option value="<?= $loai['MaLoaiTour'] ?>" 
                                            <?= ($tour['MaLoaiTour'] == $loai['MaLoaiTour']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($loai['TenLoai']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <!-- Thời lượng -->
                            <div class="input-group input-group-outline my-3 <?= !empty($tour['ThoiLuong']) ? 'is-filled' : '' ?>">
                                <label class="form-label">Thời lượng</label>
                                <input type="text" name="thoi_luong" class="form-control" 
                                       value="<?= htmlspecialchars($tour['ThoiLuong']) ?>" required>
                            </div>
                        </div>
                    </div>

                    <!-- Giá -->
                    <div class="input-group input-group-outline my-3 <?= !empty($tour['GiaTour']) ? 'is-filled' : '' ?>">
                        <label class="form-label">Giá Tour (VNĐ)</label>
                        <input type="number" name="gia_tour" class="form-control" 
                               value="<?= $tour['GiaTour'] ?>" required min="0">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <!-- Trạng thái -->
                            <div class="input-group input-group-outline my-3 is-filled">
                                <label class="form-label">Trạng thái</label>
                                <select name="trang_thai" class="form-control" required>
                                    <option value="1" <?= ($tour['TrangThai'] == 1) ? 'selected' : '' ?>>Hoạt động</option>
                                    <option value="0" <?= ($tour['TrangThai'] == 0) ? 'selected' : '' ?>>Tạm dừng</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <!-- Địa điểm khởi hành -->
                            <div class="input-group input-group-outline my-3 <?= !empty($tour['DiaDiemKhoiHanh']) ? 'is-filled' : '' ?>">
                                <label class="form-label">Địa điểm khởi hành</label>
                                <input type="text" name="dia_diem" class="form-control"
                                       value="<?= htmlspecialchars($tour['DiaDiemKhoiHanh']) ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Mô tả -->
                    <div class="input-group input-group-outline my-3 <?= !empty($tour['MoTa']) ? 'is-filled' : '' ?>">
                        <label class="form-label">Mô tả</label>
                        <textarea name="mo_ta" class="form-control" rows="5" 
                                  placeholder="Mô tả chi tiết tour..."><?= htmlspecialchars($tour['MoTa'] ?? '') ?></textarea>
                    </div>

                    <hr class="my-4">

                    <!-- ================= LỊCH TRÌNH TOUR ================= -->
                    <h5 class="mb-3">📅 Lịch trình tour</h5>

                    <div id="lich-trinh-wrapper">
                        <?php if (!empty($tour['LichTrinh'])): ?>
                            <?php foreach ($tour['LichTrinh'] as $index => $lt): ?>
                                <div class="lich-trinh-item border p-3 mb-3 rounded">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="input-group input-group-outline is-filled mb-3">
                                                <label class="form-label">Ngày</label>
                                                <input type="number" name="lich_trinh[<?= $index ?>][so_ngay]" 
                                                       class="form-control" min="1" 
                                                       value="<?= $lt['SoNgay'] ?>" required>
                                            </div>
                                        </div>

                                        <div class="col-md-5">
                                            <div class="input-group input-group-outline is-filled mb-3">
                                                <label class="form-label">Tiêu đề</label>
                                                <input type="text" name="lich_trinh[<?= $index ?>][tieu_de]" 
                                                       class="form-control" 
                                                       value="<?= htmlspecialchars($lt['TieuDe']) ?>" required>
                                            </div>
                                        </div>

                                        <div class="col-md-5">
                                            <div class="input-group input-group-outline is-filled mb-3">
                                                <label class="form-label">Hoạt động</label>
                                                <textarea name="lich_trinh[<?= $index ?>][hoat_dong]" 
                                                          class="form-control" rows="2" required><?= htmlspecialchars($lt['HoatDong']) ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Nếu không có lịch trình, hiển thị 1 mẫu trống -->
                            <div class="lich-trinh-item border p-3 mb-3 rounded">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="input-group input-group-outline is-filled mb-3">
                                            <label class="form-label">Ngày</label>
                                            <input type="number" name="lich_trinh[0][so_ngay]" 
                                                   class="form-control" min="1" value="1" required>
                                        </div>
                                    </div>

                                    <div class="col-md-5">
                                        <div class="input-group input-group-outline is-filled mb-3">
                                            <label class="form-label">Tiêu đề</label>
                                            <input type="text" name="lich_trinh[0][tieu_de]" 
                                                   class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="col-md-5">
                                        <div class="input-group input-group-outline is-filled mb-3">
                                            <label class="form-label">Hoạt động</label>
                                            <textarea name="lich_trinh[0][hoat_dong]" 
                                                      class="form-control" rows="2" required></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <button type="button" class="btn btn-sm bg-gradient-dark mb-3"
                        onclick="addLichTrinh()">+ Thêm ngày</button>

                    <hr class="my-4">

                    <!-- ================= HÌNH ẢNH TOUR ================= -->
                    <h5 class="mb-3">🖼️ Hình ảnh tour</h5>

                    <!-- Hiển thị hình ảnh hiện có -->
                    <?php if (!empty($tour['HinhAnh'])): ?>
                        <div class="row mb-3">
                            <?php foreach ($tour['HinhAnh'] as $img): ?>
                                <div class="col-md-2 mb-2">
                                    <div class="position-relative">
                                        <img src="<?= htmlspecialchars($img['URLHinhAnh']) ?>" 
                                             class="img-fluid rounded border" 
                                             style="height: 100px; object-fit: cover; width: 100%;"
                                             onerror="this.src='https://via.placeholder.com/150x100?text=Không+tải+được+hình'">
                                        <div class="mt-1 text-center">
                                            <small class="text-muted">Hình hiện có</small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Upload thêm ảnh mới -->
                    <div class="input-group input-group-outline is-filled mb-3">
                        <label class="form-label">Thêm hình ảnh mới</label>
                        <input type="file" name="hinh_anh[]" multiple class="form-control" accept="image/*">
                    </div>

                    <small class="text-secondary">
                        Chọn nhiều ảnh cùng lúc, hệ thống sẽ thêm vào tour (không xóa ảnh cũ)
                    </small>

                    <hr class="my-4">

                    <!-- ================= CHÍNH SÁCH TOUR ================= -->
                    <h5 class="mb-3">📌 Chính sách tour</h5>

                    <div id="chinh-sach-wrapper">
                        <?php if (!empty($tour['ChinhSach'])): ?>
                            <?php foreach ($tour['ChinhSach'] as $index => $cs): ?>
                                <div class="border p-3 mb-3 rounded">
                                    <div class="input-group input-group-outline is-filled mb-3">
                                        <label class="form-label">Tên chính sách</label>
                                        <input type="text" name="chinh_sach[<?= $index ?>][ten]" 
                                               class="form-control" 
                                               value="<?= htmlspecialchars($cs['TenChinhSach']) ?>" required>
                                    </div>

                                    <div class="input-group input-group-outline is-filled mb-3">
                                        <label class="form-label">Nội dung chính sách</label>
                                        <textarea name="chinh_sach[<?= $index ?>][noi_dung]" 
                                                  class="form-control" rows="3" required><?= htmlspecialchars($cs['NoiDungChinhSach']) ?></textarea>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Nếu không có chính sách, hiển thị 1 mẫu trống -->
                            <div class="border p-3 mb-3 rounded">
                                <div class="input-group input-group-outline is-filled mb-3">
                                    <label class="form-label">Tên chính sách</label>
                                    <input type="text" name="chinh_sach[0][ten]" 
                                           class="form-control" required>
                                </div>

                                <div class="input-group input-group-outline is-filled mb-3">
                                    <label class="form-label">Nội dung chính sách</label>
                                    <textarea name="chinh_sach[0][noi_dung]" 
                                              class="form-control" rows="3" required></textarea>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <button type="button" class="btn btn-sm bg-gradient-dark mb-3"
                        onclick="addChinhSach()">+ Thêm chính sách</button>

                    <hr class="my-4">

                    <!-- ================= NHÀ CUNG CẤP ================= -->
                    <h5 class="mb-3">🏨 Nhà cung cấp</h5>

                    <div class="row">
                        <?php foreach ($dsNhaCungCap as $ncc): ?>
                            <div class="col-md-4 mb-2">
                                <label>
                                    <input type="checkbox" name="nha_cung_cap[]"
                                        value="<?= $ncc['MaNhaCungCap'] ?>"
                                        <?= in_array($ncc['MaNhaCungCap'], $selectedNCC) ? 'checked' : '' ?>>
                                    <?= htmlspecialchars($ncc['TenNhaCungCap']) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <hr class="my-4">

                    <!-- Buttons -->
                    <div class="d-flex justify-content-end mt-4 mb-3">
                        <a href="index.php?action=list-tours" class="btn btn-outline-secondary me-2">Hủy bỏ</a>
                        <button type="submit" class="btn bg-gradient-primary">Cập nhật Tour</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

<script>
// Lịch trình
let ltIndex = <?= !empty($tour['LichTrinh']) ? count($tour['LichTrinh']) : 1 ?>;

function addLichTrinh() {
    let wrapper = document.getElementById('lich-trinh-wrapper');

    let html = `
        <div class="lich-trinh-item border p-3 mb-3 rounded">
            <div class="row">
                <div class="col-md-2">
                    <div class="input-group input-group-outline is-filled mb-3">
                        <label class="form-label">Ngày</label>
                        <input type="number" name="lich_trinh[${ltIndex}][so_ngay]" 
                               class="form-control" min="1" required>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="input-group input-group-outline is-filled mb-3">
                        <label class="form-label">Tiêu đề</label>
                        <input type="text" name="lich_trinh[${ltIndex}][tieu_de]" 
                               class="form-control" required>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="input-group input-group-outline is-filled mb-3">
                        <label class="form-label">Hoạt động</label>
                        <textarea name="lich_trinh[${ltIndex}][hoat_dong]" 
                                  class="form-control" rows="2" required></textarea>
                    </div>
                </div>
            </div>
        </div>
    `;

    wrapper.insertAdjacentHTML('beforeend', html);
    ltIndex++;
}

// Chính sách
let csIndex = <?= !empty($tour['ChinhSach']) ? count($tour['ChinhSach']) : 1 ?>;

function addChinhSach() {
    let wrapper = document.getElementById('chinh-sach-wrapper');

    let html = `
        <div class="border p-3 mb-3 rounded">
            <div class="input-group input-group-outline is-filled mb-3">
                <label class="form-label">Tên chính sách</label>
                <input type="text" name="chinh_sach[${csIndex}][ten]" 
                       class="form-control" required>
            </div>

            <div class="input-group input-group-outline is-filled mb-3">
                <label class="form-label">Nội dung chính sách</label>
                <textarea name="chinh_sach[${csIndex}][noi_dung]" 
                          class="form-control" rows="3" required></textarea>
            </div>
        </div>
    `;

    wrapper.insertAdjacentHTML('beforeend', html);
    csIndex++;
}
</script>