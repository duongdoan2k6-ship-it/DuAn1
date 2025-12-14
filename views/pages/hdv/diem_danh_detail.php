<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi Tiết Điểm Danh</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">
    
    <?php $isLocked = isset($phienInfo['trang_thai_khoa']) && $phienInfo['trang_thai_khoa'] == 1; ?>

    <nav class="navbar navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="<?= BASE_URL ?>routes/index.php?action=hdv-tour-detail&id=<?= $lichId ?>">
                <i class="bi bi-arrow-left"></i> Quay lại Chi tiết Tour
            </a>
            <span class="navbar-text text-white fw-bold">
                <?= htmlspecialchars($tourInfo['ten_tour']) ?>
            </span>
        </div>
    </nav>

    <div class="container">
        <?php if(isset($_GET['status']) && $_GET['status'] == 'locked_error'): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-triangle"></i> Phiên này đã khóa, không thể chỉnh sửa!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['status']) && $_GET['status'] == 'dd_saved'): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle"></i> Đã lưu và khóa điểm danh thành công!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <div class="card shadow">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0 text-primary">
                        <i class="bi bi-clipboard-check"></i> <?= htmlspecialchars($phienInfo['tieu_de']) ?>
                        <?php if($isLocked): ?>
                            <span class="badge bg-danger ms-2"><i class="bi bi-lock-fill"></i> Đã khóa</span>
                        <?php else: ?>
                            <span class="badge bg-success ms-2"><i class="bi bi-unlock-fill"></i> Đang mở</span>
                        <?php endif; ?>
                    </h4>
                    <small class="text-muted">Ngày tạo: <?= date('H:i d/m/Y', strtotime($phienInfo['thoi_gian_tao'])) ?></small>
                </div>
                
                <?php if(!$isLocked): ?>
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="checkAllPresent()">
                    <i class="bi bi-check-all"></i> Tất cả Có mặt
                </button>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <form action="<?= BASE_URL ?>routes/index.php?action=hdv-save-diem-danh" method="POST">
                    <input type="hidden" name="lich_id" value="<?= $lichId ?>">
                    <input type="hidden" name="phien_id" value="<?= $phienId ?>">

                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-bordered">
                            <thead class="table-light text-center">
                                <tr>
                                    <th style="width: 5%">STT</th>
                                    <th style="width: 25%">Họ Tên Khách</th>
                                    <th style="width: 15%">SĐT</th>
                                    <th style="width: 25%">Trạng Thái</th>
                                    <th>Ghi Chú</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($passengers as $index => $p): ?>
                                <tr>
                                    <td class="text-center"><?= $index + 1 ?></td>
                                    <td class="fw-bold">
                                        <?= htmlspecialchars($p['ho_ten_khach']) ?>
                                        <?php if($p['trang_thai'] == 1): ?>
                                            <i class="bi bi-check-circle-fill text-success ms-1"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center"><?= $p['sdt_lien_he'] ?></td>
                                    
                                    <td class="text-center">
                                        <div class="btn-group w-100" role="group">
                                            <input type="radio" class="btn-check" 
                                                   name="attendance[<?= $p['khach_id'] ?>][status]" 
                                                   id="btn-absent-<?= $p['khach_id'] ?>" 
                                                   value="0" <?= $p['trang_thai'] == 0 ? 'checked' : '' ?>
                                                   <?= $isLocked ? 'disabled' : '' ?>>
                                            <label class="btn btn-outline-danger" for="btn-absent-<?= $p['khach_id'] ?>">Vắng</label>

                                            <input type="radio" class="btn-check class-present" 
                                                   name="attendance[<?= $p['khach_id'] ?>][status]" 
                                                   id="btn-present-<?= $p['khach_id'] ?>" 
                                                   value="1" <?= $p['trang_thai'] == 1 ? 'checked' : '' ?>
                                                   <?= $isLocked ? 'disabled' : '' ?>>
                                            <label class="btn btn-outline-success" for="btn-present-<?= $p['khach_id'] ?>">Có mặt</label>
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <input type="text" class="form-control" 
                                               name="attendance[<?= $p['khach_id'] ?>][note]" 
                                               value="<?= htmlspecialchars($p['ghi_chu'] ?? '') ?>" 
                                               placeholder="Lý do vắng..."
                                               <?= $isLocked ? 'disabled' : '' ?>>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <a href="<?= BASE_URL ?>routes/index.php?action=hdv-tour-detail&id=<?= $lichId ?>" class="btn btn-secondary me-2">Quay lại</a>
                        
                        <?php if(!$isLocked): ?>
                            <button type="submit" class="btn btn-primary px-4 fw-bold" onclick="return confirm('LƯU Ý: Sau khi lưu, bạn sẽ KHÔNG thể chỉnh sửa lại. Bạn có chắc chắn không?')">
                                <i class="bi bi-save"></i> LƯU & KHÓA
                            </button>
                        <?php else: ?>
                            <button type="button" class="btn btn-secondary px-4 fw-bold" disabled>
                                <i class="bi bi-lock-fill"></i> ĐÃ KHÓA
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function checkAllPresent() {
            let presents = document.querySelectorAll('.class-present');
            presents.forEach(radio => {
                // Kiểm tra xem radio có bị disabled không trước khi check
                if (!radio.disabled) {
                    radio.checked = true;
                }
            });
        }
    </script>
</body>
</html>