<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Quản Lý Tours</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="text-primary fw-bold">🏞️ Danh Sách Tours</h3>
        <div>
            <a href="<?= BASE_URL ?>routes/index.php?action=admin-dashboard" class="btn btn-secondary">
                <i class="bi bi-speedometer2"></i> Về Dashboard
            </a>
            <a href="<?= BASE_URL ?>routes/index.php?action=admin-tour-create" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Thêm Tour Mới
            </a>
        </div>
    </div>
    
    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php 
                $msg = $_GET['msg'];
                if($msg == 'created') echo "Thêm tour mới thành công!";
                elseif($msg == 'updated') echo "Cập nhật tour thành công!";
                elseif($msg == 'deleted') echo "Đã xóa tour thành công!";
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0 align-middle">
                    <thead class="table-dark text-center">
                        <tr>
                            <th width="50">ID</th>
                            <th width="100">Ảnh</th>
                            <th>Tên Tour</th>
                            <th width="100">Thời gian</th>
                            <th>Giá (Người lớn)</th>
                            <th>Loại Tour</th>
                            <th width="150">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($tours)): ?>
                            <tr><td colspan="7" class="text-center py-4">Chưa có tour nào. Hãy thêm mới!</td></tr>
                        <?php else: ?>
                            <?php foreach ($tours as $t): ?>
                            <tr>
                                <td class="text-center fw-bold">#<?= $t['id'] ?></td>
                                <td class="text-center">
                                    <?php if(!empty($t['anh_tour'])): ?>
                                        <img src="assets/uploads/<?= $t['anh_tour'] ?>" width="80" height="50" class="rounded" style="object-fit: cover;">
                                    <?php else: ?>
                                        <span class="text-muted small">No Image</span>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold text-primary"><?= $t['ten_tour'] ?></td>
                                
                                <td class="text-center"><?= $t['so_ngay'] ?> ngày</td>
                                
                                <td class="text-danger fw-bold text-end pe-3"><?= number_format($t['gia_nguoi_lon']) ?>đ</td>
                                
                                <td class="text-center"><span class="badge bg-info text-dark"><?= $t['ten_loai'] ?></span></td>
                                
                                <td class="text-center">
                                    <a href="<?= BASE_URL ?>routes/index.php?action=admin-tour-edit&id=<?= $t['id'] ?>" class="btn btn-sm btn-warning" title="Sửa">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="<?= BASE_URL ?>routes/index.php?action=admin-tour-delete&id=<?= $t['id'] ?>" class="btn btn-sm btn-danger" title="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa tour này? Tất cả dữ liệu liên quan sẽ bị mất!')">
                                        <i class="bi bi-trash"></i>
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