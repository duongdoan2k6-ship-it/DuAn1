<?php
// Khởi tạo Model để lấy lịch trình
$lkhModel = new LichKhoiHanhModel();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Quản Lý Nhân Sự</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* CSS cho nút disabled đẹp hơn */
        .btn.disabled, .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: auto; /* Để vẫn hiện tooltip title khi hover */
        }
    </style>
</head>
<body class="bg-light">
<div class="container mt-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="text-primary fw-bold">
            <?= (isset($isTrash) && $isTrash) ? '<i class="bi bi-trash"></i> Thùng Rác Nhân Sự' : '👥 Danh Sách Nhân Sự' ?>
        </h3>
        <div>
            <?php if (isset($isTrash) && $isTrash): ?>
                <a href="<?= BASE_URL ?>routes/index.php?action=admin-guides" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại danh sách
                </a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>routes/index.php?action=admin-guides&view=trash" class="btn btn-warning me-2">
                    <i class="bi bi-trash"></i> Thùng rác
                </a>
                <a href="<?= BASE_URL ?>routes/index.php?action=admin-dashboard" class="btn btn-secondary me-2">Dashboard</a>
                <a href="<?= BASE_URL ?>routes/index.php?action=admin-guide-create" class="btn btn-success">+ Thêm Mới</a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="card shadow-sm mb-4">
        <div class="card-body py-2">
            <form action="<?= BASE_URL ?>routes/index.php" method="GET" class="row g-2 align-items-center">
                <input type="hidden" name="action" value="admin-guides">
                
                <?php if (isset($isTrash) && $isTrash): ?>
                    <input type="hidden" name="view" value="trash">
                <?php endif; ?>

                <div class="col-md-4">
                    <input type="text" name="keyword" class="form-control" placeholder="Tìm tên, email..." value="<?= htmlspecialchars($filters['keyword'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-select">
                        <option value="">-- Tất cả vai trò --</option>
                        <option value="HDV" <?= ($filters['role'] ?? '') == 'HDV' ? 'selected' : '' ?>>Hướng Dẫn Viên</option>
                        <option value="TaiXe" <?= ($filters['role'] ?? '') == 'TaiXe' ? 'selected' : '' ?>>Tài Xế</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Lọc</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover table-bordered mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="50" class="text-center">#</th>
                        <th width="80" class="text-center">Ảnh</th>
                        <th>Họ Tên / Email</th>
                        <th>Vai Trò</th>
                        <th width="30%" class="text-secondary opacity-7">Lịch trình sắp tới</th>
                        <th width="200" class="text-center">Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($guides)): ?>
                        <tr><td colspan="6" class="text-center py-4">Không có dữ liệu.</td></tr>
                    <?php else: ?>
                        <?php foreach ($guides as $g): ?>
                            <?php 
                                // [LOGIC MỚI] Lấy lịch trình ngay đầu vòng lặp để dùng cho cả 2 cột
                                $schedules = $lkhModel->getUpcomingSchedulesByStaff($g['id']);
                                $count = count($schedules);
                                $hasSchedule = ($count > 0);
                            ?>
                        <tr>
                            <td class="text-center"><?= $g['id'] ?></td>
                            <td class="text-center">
                                <img src="<?= BASE_URL ?>assets/uploads/hdv/<?= $g['anh_dai_dien'] ?>" class="rounded-circle border" width="50" height="50" style="object-fit: cover;">
                            </td>
                            <td>
                                <div class="fw-bold text-primary"><?= $g['ho_ten'] ?></div>
                                <small class="text-muted"><?= $g['email'] ?></small>
                            </td>
                            <td>
                                <?php if($g['phan_loai_nhan_su'] == 'HDV'): ?>
                                    <span class="badge bg-primary">Hướng Dẫn Viên</span>
                                <?php elseif($g['phan_loai_nhan_su'] == 'TaiXe'): ?>
                                    <span class="badge bg-secondary">Tài Xế</span>
                                <?php else: ?>
                                    <span class="badge bg-info text-dark">Hậu Cần</span>
                                <?php endif; ?>
                            </td>
                            
                            <td class="align-middle">
                                <?php if (!$hasSchedule): ?>
                                    <span class="badge bg-light text-muted border">Chưa có lịch</span>
                                <?php else: ?>
                                    <div class="fw-bold text-primary mb-2">
                                        Có <?= $count ?> lịch
                                    </div>

                                    <?php $nearest = $schedules[0]; ?>
                                    <div class="p-2 border rounded bg-white">
                                        <div class="text-muted small text-uppercase fw-bold" style="font-size: 11px;">Lịch gần nhất:</div>
                                        <div class="mt-1">
                                            <span class="fw-bold text-dark" title="<?= htmlspecialchars($nearest['ten_tour']) ?>">
                                                <?= htmlspecialchars($nearest['ten_tour']) ?>
                                            </span>
                                            <div class="small text-muted mt-1">
                                                <i class="bi bi-calendar-event"></i> <?= date('d/m/Y', strtotime($nearest['ngay_khoi_hanh'])) ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <td class="text-center">
                                <a href="<?= BASE_URL ?>routes/index.php?action=admin-guide-detail&id=<?= $g['id'] ?>" class="btn btn-sm btn-info text-white" title="Xem chi tiết"><i class="bi bi-eye"></i></a>
                                
                                <?php if (isset($isTrash) && $isTrash): ?>
                                    <a href="<?= BASE_URL ?>routes/index.php?action=admin-guide-restore&id=<?= $g['id'] ?>" 
                                       class="btn btn-sm btn-success" 
                                       onclick="return confirm('Bạn có chắc muốn khôi phục nhân sự này?')"
                                       title="Khôi phục lại danh sách">
                                        <i class="bi bi-arrow-counterclockwise"></i> Khôi phục
                                    </a>
                                <?php else: ?>
                                    <a href="<?= BASE_URL ?>routes/index.php?action=admin-guide-edit&id=<?= $g['id'] ?>" class="btn btn-sm btn-warning" title="Sửa thông tin"><i class="bi bi-pencil"></i></a>
                                    
                                    <?php if ($hasSchedule): ?>
                                        <button class="btn btn-sm btn-secondary disabled" title="Không thể xóa vì đang có lịch phân công" disabled>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    <?php else: ?>
                                        <a href="<?= BASE_URL ?>routes/index.php?action=admin-guide-delete&id=<?= $g['id'] ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Xóa tạm thời vào thùng rác?')" 
                                           title="Xóa">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    <?php endif; ?>

                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>