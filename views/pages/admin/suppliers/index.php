<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Quản Lý Nhà Cung Cấp</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-primary fw-bold"><i class="bi bi-shop"></i> Đối Tác & Nhà Cung Cấp</h3>
        <div>
            <a href="index.php?action=admin-dashboard" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Dashboard
            </a>
            <a href="index.php?action=admin-supplier-create" class="btn btn-success">
                <i class="bi bi-plus-lg"></i> Thêm NCC Mới
            </a>
        </div>
    </div>

    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php 
                if($_GET['msg'] == 'created') echo "Thêm mới thành công!";
                elseif($_GET['msg'] == 'updated') echo "Cập nhật thành công!";
                elseif($_GET['msg'] == 'deleted') echo "Đã xóa thành công!";
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
                            <th width="5%">ID</th>
                            <th width="25%">Tên Nhà Cung Cấp</th>
                            <th width="15%">Dịch vụ</th>
                            <th width="25%">Liên hệ</th>
                            <th>Địa chỉ</th>
                            <th width="15%">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($suppliers)): ?>
                            <tr><td colspan="6" class="text-center py-4 text-muted">Chưa có nhà cung cấp nào.</td></tr>
                        <?php else: ?>
                            <?php foreach ($suppliers as $s): ?>
                            <tr>
                                <td class="text-center fw-bold"><?= $s['id'] ?></td>
                                <td class="fw-bold text-primary"><?= $s['ten_ncc'] ?></td>
                                <td class="text-center"><span class="badge bg-info text-dark"><?= $s['dich_vu'] ?></span></td>
                                <td>
                                    <div><i class="bi bi-telephone-fill text-success"></i> <?= $s['sdt'] ?></div>
                                    <div class="small text-muted"><i class="bi bi-envelope-fill"></i> <?= $s['email'] ?></div>
                                </td>
                                <td class="small"><?= $s['dia_chi'] ?></td>
                                <td class="text-center">
                                    <a href="index.php?action=admin-supplier-edit&id=<?= $s['id'] ?>" class="btn btn-sm btn-warning" title="Sửa">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="index.php?action=admin-supplier-delete&id=<?= $s['id'] ?>" class="btn btn-sm btn-danger" title="Xóa" onclick="return confirm('Bạn có chắc muốn xóa đối tác này?')">
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