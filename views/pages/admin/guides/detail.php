<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Hồ Sơ: <?= htmlspecialchars($guide['ho_ten']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">
<div class="container mt-4">
    <div class="d-flex justify-content-between mb-4">
        <h3 class="text-primary fw-bold">Hồ Sơ Nhân Sự</h3>
        <a href="<?= BASE_URL ?>routes/index.php?action=admin-guides" class="btn btn-secondary">Quay lại</a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm text-center p-3 mb-3">
                <img src="<?= BASE_URL ?>assets/uploads/hdv/<?= $guide['anh_dai_dien'] ?>" class="rounded-circle mx-auto mb-3 border" width="150" height="150" style="object-fit: cover;">
                <h4><?= $guide['ho_ten'] ?></h4>
                <p class="text-muted"><?= $guide['email'] ?></p>
                <div class="badge bg-primary fs-6"><?= $guide['phan_loai_nhan_su'] ?></div>
            </div>
            <div class="card shadow-sm p-3">
                <p><strong>SĐT:</strong> <?= $guide['sdt'] ?></p>
                <p><strong>Ngày sinh:</strong> <?= date('d/m/Y', strtotime($guide['ngay_sinh'])) ?></p>
                <p><strong>Ngôn ngữ:</strong> <?= $guide['ngon_ngu'] ?></p>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-bold">Lịch Sử Công Tác</div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>Tour</th><th>Thời gian</th><th>Trạng thái</th></tr>
                        </thead>
                        <tbody>
                            <?php if(empty($history)): ?>
                                <tr><td colspan="3" class="text-center py-3">Chưa có lịch sử.</td></tr>
                            <?php else: ?>
                                <?php foreach($history as $h): ?>
                                <tr>
                                    <td><?= $h['ten_tour'] ?></td>
                                    <td><?= date('d/m/Y', strtotime($h['ngay_khoi_hanh'])) ?></td>
                                    <td><?= $h['trang_thai'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>