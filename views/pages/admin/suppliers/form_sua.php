<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Cập Nhật Nhà Cung Cấp</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark fw-bold">
                    ✏️ Cập Nhật Thông Tin: <?= htmlspecialchars($supplier['ten_ncc']) ?>
                </div>
                <div class="card-body p-4">
                    <form action="index.php?action=admin-supplier-update" method="POST">
                        <input type="hidden" name="id" value="<?= $supplier['id'] ?>">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tên Nhà Cung Cấp <span class="text-danger">*</span></label>
                            <input type="text" name="ten_ncc" class="form-control" value="<?= htmlspecialchars($supplier['ten_ncc']) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Loại Dịch Vụ</label>
                            <input type="text" name="dich_vu" class="form-control" value="<?= htmlspecialchars($supplier['dich_vu']) ?>" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Số Điện Thoại</label>
                                <input type="text" name="sdt" class="form-control" value="<?= htmlspecialchars($supplier['sdt']) ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Email</label>
                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($supplier['email']) ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Địa chỉ</label>
                            <textarea name="dia_chi" class="form-control" rows="2"><?= htmlspecialchars($supplier['dia_chi']) ?></textarea>
                        </div>

                        <div class="text-end border-top pt-3">
                            <a href="index.php?action=admin-suppliers" class="btn btn-secondary">Hủy bỏ</a>
                            <button type="submit" class="btn btn-warning px-4 fw-bold">Cập Nhật</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>