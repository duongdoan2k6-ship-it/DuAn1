<div class="card my-4">
    <div class="card-header bg-gradient-primary text-white">
        <h5 class="mb-0"><?= $tour['TenTour'] ?></h5>
        <small>Loại: <?= $tour['TenLoai'] ?></small>
    </div>

    <div class="card-body">

        <!-- Giá - thời lượng - trạng thái -->
        <p><strong>Thời lượng:</strong> <?= $tour['ThoiLuong'] ?></p>
        <p><strong>Giá tour:</strong> <?= number_format($tour['GiaTour'], 0, ',', '.') ?> VND</p>
        <p><strong>Khởi hành từ:</strong> <?= $tour['DiaDiemKhoiHanh'] ?></p>
        <hr>

        <!-- Hình ảnh -->
        <h6><strong>Hình ảnh tour</strong></h6>
        <div class="row">
            <?php foreach ($tour['HinhAnh'] as $img): ?>
                <div class="col-md-3 mb-3">
                    <img src="<?= $img['URLHinhAnh'] ?>" class="img-fluid rounded">
                    <small><?= $img['ChuThich'] ?></small>
                </div>
            <?php endforeach; ?>
        </div>
        <hr>

        <!-- Lịch trình -->
        <h6><strong>Lịch trình</strong></h6>
        <?php foreach ($tour['LichTrinh'] as $lt): ?>
            <div class="mb-2">
                <strong><?= $lt['TieuDe'] ?></strong>
                <p><?= $lt['HoatDong'] ?></p>
            </div>
        <?php endforeach; ?>
        <hr>

        <!-- Chính sách -->
        <h6><strong>Chính sách tour</strong></h6>
        <?php foreach ($tour['ChinhSach'] as $cs): ?>
            <p><strong><?= $cs['TenChinhSach'] ?>:</strong> <?= $cs['NoiDungChinhSach'] ?></p>
        <?php endforeach; ?>
        <hr>

        <!-- Nhà cung cấp -->
        <h6><strong>Nhà cung cấp</strong></h6>
        <ul>
            <?php foreach ($tour['NhaCungCap'] as $ncc): ?>
                <li><?= $ncc['TenNhaCungCap'] ?> – <?= $ncc['LoaiNhaCungCap'] ?></li>
            <?php endforeach; ?>
        </ul>

    </div>
</div>
