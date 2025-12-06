<div class="card my-4">
    <div class="card-header bg-gradient-primary text-white">
        <h5 class="mb-0"><?= htmlspecialchars($tour['TenTour']) ?></h5>
        <small>Loại: <?= htmlspecialchars($tour['TenLoai']) ?></small>
    </div>

    <div class="card-body">
        <!-- Giá - thời lượng - trạng thái -->
        <p><strong>Thời lượng:</strong> <?= htmlspecialchars($tour['ThoiLuong']) ?></p>
        <p><strong>Giá tour:</strong> <?= number_format($tour['GiaTour'], 0, ',', '.') ?> VND</p>
        <p><strong>Khởi hành từ:</strong> <?= htmlspecialchars($tour['DiaDiemKhoiHanh']) ?></p>
        
        <hr>
        
        <!-- MÔ TẢ -->
        <?php if (!empty($tour['MoTa'])): ?>
        <div class="mb-3">
            <h6><strong>Mô tả tour</strong></h6>
            <p><?= nl2br(htmlspecialchars($tour['MoTa'])) ?></p>
        </div>
        <hr>
        <?php endif; ?>

        <!-- HÌNH ẢNH -->
        <?php if (!empty($tour['HinhAnh'])): ?>
        <h6><strong>Hình ảnh tour</strong></h6>
        <div class="row">
            <?php foreach ($tour['HinhAnh'] as $img): ?>
                <div class="col-md-3 mb-3">
                    <?php 
                    // Lấy đường dẫn ảnh
                    $imageUrl = $img['URLHinhAnh'];
                    
                    // Đảm bảo đường dẫn bắt đầu bằng /
                    if (strpos($imageUrl, '/') !== 0) {
                        $imageUrl = '/' . $imageUrl;
                    }
                    ?>
                    <img src="<?= htmlspecialchars($imageUrl) ?>" 
                         class="img-fluid rounded shadow-sm border" 
                         alt="Hình tour"
                         style="height: 200px; object-fit: cover; width: 100%;"
                         onerror="this.onerror=null; this.src='https://via.placeholder.com/300x200?text=Kh%C3%B4ng+th%E1%BB%83+t%E1%BA%A3i+h%C3%ACnh'">
                    
                    <?php if (!empty($img['ChuThich'])): ?>
                        <small class="text-muted d-block mt-1"><?= htmlspecialchars($img['ChuThich']) ?></small>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <hr>
        <?php endif; ?>

        <!-- Lịch trình -->
        <?php if (!empty($tour['LichTrinh'])): ?>
        <h6><strong>Lịch trình</strong></h6>
        <?php foreach ($tour['LichTrinh'] as $lt): ?>
            <div class="mb-3 p-2 border-start border-primary border-3 ps-3">
                <strong class="d-block"><?= htmlspecialchars($lt['TieuDe']) ?></strong>
                <small class="text-muted">Ngày <?= $lt['SoNgay'] ?></small>
                <p class="mt-1 mb-0"><?= nl2br(htmlspecialchars($lt['HoatDong'])) ?></p>
            </div>
        <?php endforeach; ?>
        <hr>
        <?php endif; ?>

        <!-- Chính sách -->
        <?php if (!empty($tour['ChinhSach'])): ?>
        <h6><strong>Chính sách tour</strong></h6>
        <?php foreach ($tour['ChinhSach'] as $cs): ?>
            <div class="mb-2">
                <strong class="text-primary"><?= htmlspecialchars($cs['TenChinhSach']) ?>:</strong>
                <p class="mb-1"><?= nl2br(htmlspecialchars($cs['NoiDungChinhSach'])) ?></p>
            </div>
        <?php endforeach; ?>
        <hr>
        <?php endif; ?>

        <!-- Nhà cung cấp -->
        <?php if (!empty($tour['NhaCungCap'])): ?>
        <h6><strong>Nhà cung cấp</strong></h6>
        <div class="row">
            <?php foreach ($tour['NhaCungCap'] as $ncc): ?>
                <div class="col-md-4 mb-2">
                    <div class="card border">
                        <div class="card-body">
                            <h6 class="card-title"><?= htmlspecialchars($ncc['TenNhaCungCap']) ?></h6>
                            <p class="card-text mb-1">
                                <small>Loại: <?= htmlspecialchars($ncc['LoaiNhaCungCap']) ?></small>
                            </p>
                            <?php if (!empty($ncc['DiaChi'])): ?>
                                <p class="card-text mb-1">
                                    <small>Địa chỉ: <?= htmlspecialchars($ncc['DiaChi']) ?></small>
                                </p>
                            <?php endif; ?>
                            <?php if (!empty($ncc['SoDienThoai'])): ?>
                                <p class="card-text mb-1">
                                    <small>Điện thoại: <?= htmlspecialchars($ncc['SoDienThoai']) ?></small>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>