<!DOCTYPE html>
<html lang="vi">

<head>
    <title>Thùng Rác Tours</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="bg-light">
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="text-danger fw-bold">🗑️ Thùng Rác Tours</h3>
            <div>
                <a href="<?= BASE_URL ?>routes/index.php?action=admin-tours" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại Danh sách
                </a>
            </div>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php
                if ($_GET['msg'] == 'restored') echo "✅ Đã khôi phục tour thành công!";
                if ($_GET['msg'] == 'destroyed') echo "🗑️ Đã xóa vĩnh viễn tour và dữ liệu liên quan!";
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-danger">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered mb-0 align-middle">
                        <thead class="table-danger text-center">
                            <tr>
                                <th width="50">ID</th>
                                <th width="100">Ảnh</th>
                                <th>Tên Tour</th>
                                <th>Loại Tour</th>
                                <th width="250">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($tours)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">Thùng rác trống!</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($tours as $t): ?>
                                    <tr>
                                        <td class="text-center fw-bold">#<?= $t['id'] ?></td>
                                        <td class="text-center">
                                            <?php if (!empty($t['anh_tour'])): ?>
                                                <img src="assets/uploads/<?= $t['anh_tour'] ?>" width="80" height="50" class="rounded" style="object-fit: cover;">
                                            <?php else: ?>
                                                <span class="text-muted small">No Image</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="fw-bold"><?= $t['ten_tour'] ?></td>
                                        <td class="text-center"><span class="badge bg-secondary"><?= $t['ten_loai'] ?></span></td>

                                        <td class="text-center">
                                            <a href="<?= BASE_URL ?>routes/index.php?action=admin-tour-restore&id=<?= $t['id'] ?>"
                                                class="btn btn-sm btn-success" title="Khôi phục">
                                                <i class="bi bi-arrow-counterclockwise"></i> Khôi phục
                                            </a>

                                            <a href="<?= BASE_URL ?>routes/index.php?action=admin-tour-destroy&id=<?= $t['id'] ?>"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('⚠️ CẢNH BÁO: Hành động này không thể hoàn tác!\nBạn có chắc chắn muốn xóa vĩnh viễn Tour này và toàn bộ ảnh liên quan?')"
                                                title="Xóa vĩnh viễn">
                                                <i class="bi bi-x-circle"></i> Xóa vĩnh viễn
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>