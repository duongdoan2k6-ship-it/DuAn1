<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Nhật Ký Tour</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark fw-bold">
                CHỈNH SỬA NHẬT KÝ
            </div>
            <div class="card-body">
                <form action="<?= BASE_URL ?>routes/index.php?action=hdv-update-nhat-ky" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $log['id'] ?>">
                    <input type="hidden" name="lich_khoi_hanh_id" value="<?= $log['lich_khoi_hanh_id'] ?>">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Tiêu đề</label>
                        <input type="text" class="form-control" name="tieu_de" value="<?= htmlspecialchars($log['tieu_de'] ?? '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nội dung</label>
                         <textarea class="form-control" name="noi_dung" rows="5" required><?= htmlspecialchars($log['noi_dung'] ?? '') ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-danger fw-bold">Sự cố (nếu có)</label>
                            <textarea class="form-control" name="su_co" rows="3"><?= htmlspecialchars($log['su_co'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-info fw-bold">Phản hồi khách</label>
                            <textarea class="form-control" name="phan_hoi_khach" rows="3"><?= htmlspecialchars($log['phan_hoi_khach'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Hình ảnh</label>
                        <?php if (!empty($log['hinh_anh'])): ?>
                            <div class="mb-2">
                                <img src="<?= BASE_URL . 'public/' . $log['hinh_anh'] ?>" width="150" class="img-thumbnail">
                                <small class="text-muted d-block">Ảnh hiện tại</small>
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" name="hinh_anh" accept="image/*">
                        <small class="text-muted">Chỉ chọn ảnh nếu muốn thay đổi ảnh cũ.</small>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="<?= BASE_URL ?>routes/index.php?action=hdv-tour-detail&id=<?= $log['lich_khoi_hanh_id'] ?>" class="btn btn-secondary">Hủy bỏ</a>
                        <button type="submit" class="btn btn-warning fw-bold">Cập Nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>