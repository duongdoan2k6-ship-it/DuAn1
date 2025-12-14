<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Hồ Sơ: <?= htmlspecialchars($guide['ho_ten']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">
<div class="container mt-4 mb-5">
    <div class="d-flex justify-content-between mb-4">
        <h3 class="text-primary fw-bold">Hồ Sơ Nhân Sự</h3>
        <a href="<?= BASE_URL ?>routes/index.php?action=admin-guides" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Quay lại</a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm text-center p-3 mb-3">
                <img src="<?= BASE_URL ?>assets/uploads/hdv/<?= $guide['anh_dai_dien'] ?>" class="rounded-circle mx-auto mb-3 border" width="150" height="150" style="object-fit: cover;">
                <h4 class="mb-1"><?= htmlspecialchars($guide['ho_ten']) ?></h4>
                <p class="text-muted mb-2"><?= htmlspecialchars($guide['email']) ?></p>
                
                <?php 
                    $roleMap = [
                        'HDV' => ['Hướng Dẫn Viên', 'bg-primary'],
                        'TaiXe' => ['Tài Xế', 'bg-warning text-dark'],
                    ];
                    $roleInfo = $roleMap[$guide['phan_loai_nhan_su']] ?? [$guide['phan_loai_nhan_su'], 'bg-secondary'];
                ?>
                <div><span class="badge <?= $roleInfo[1] ?> fs-6"><?= $roleInfo[0] ?></span></div>
            </div>
            
            <div class="card shadow-sm p-3">
                <h6 class="text-uppercase text-muted fw-bold border-bottom pb-2 mb-3" style="font-size: 0.85rem;">Thông tin liên hệ</h6>
                <p class="mb-2"><i class="bi bi-telephone-fill text-primary me-2"></i><strong>SĐT:</strong> <?= htmlspecialchars($guide['sdt']) ?></p>
                <p class="mb-2"><i class="bi bi-calendar-event-fill text-primary me-2"></i><strong>Ngày sinh:</strong> <?= date('d/m/Y', strtotime($guide['ngay_sinh'])) ?></p>
                
                <h6 class="text-uppercase text-muted fw-bold border-bottom pb-2 mb-3 mt-4" style="font-size: 0.85rem;">Thông tin khác</h6>
                <p class="mb-0"><strong>Sức khỏe:</strong> <?= htmlspecialchars($guide['suc_khoe']) ?></p>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white fw-bold text-primary"><i class="bi bi-mortarboard-fill me-2"></i>Thông Tin Nghiệp Vụ</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="fw-bold text-secondary">Chứng chỉ / Bằng cấp:</label>
                        <div class="bg-light p-2 rounded border mt-1">
                            <?= nl2br(htmlspecialchars($guide['chung_chi'] ?: 'Chưa cập nhật')) ?>
                        </div>
                    </div>
                    <div>
                        <label class="fw-bold text-secondary">Kinh nghiệm làm việc:</label>
                        <div class="bg-light p-2 rounded border mt-1">
                            <?= nl2br(htmlspecialchars($guide['kinh_nghiem'] ?: 'Chưa cập nhật')) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-white fw-bold text-primary d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-clock-history me-2"></i>Lịch Sử Công Tác</span>
                    <span class="badge bg-secondary"><?= count($history) ?> chuyến</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Tên Tour / Chuyến đi</th>
                                    <th>Thời gian</th>
                                    <th class="text-center">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($history)): ?>
                                    <tr><td colspan="3" class="text-center py-4 text-muted">Chưa có lịch sử công tác nào.</td></tr>
                                <?php else: ?>
                                    <?php 
                                        $statusMap = [
                                            'NhanKhach'           => ['bg-info text-dark', 'Đang nhận khách'],
                                            'KhongNhanThemKhach'  => ['bg-warning text-dark', 'Ngừng nhận khách'],
                                            'DaDay'               => ['bg-danger', 'Đã đầy'],
                                            'Huy'                 => ['bg-secondary', 'Đã hủy'],
                                            'HuyDoKhongDuSoLuong' => ['bg-secondary', 'Hủy (Thiếu khách)'],
                                            'DangDi'              => ['bg-primary', 'Đang đi'],
                                            'HoanThanh'           => ['bg-success', 'Hoàn thành']
                                        ];
                                    ?>
                                    <?php foreach($history as $h): ?>
                                    <?php
                                        $today = time();
                                        $start = strtotime($h['ngay_khoi_hanh']);
                                        $end   = strtotime($h['ngay_ket_thuc']);
                                        $rawStatus = $h['trang_thai'];
                                        
                                        $displayStatusKey = $rawStatus;

                                        if ($rawStatus !== 'Huy' && $rawStatus !== 'HuyDoKhongDuSoLuong') {
                                            if ($today > $end) {
                                                $displayStatusKey = 'HoanThanh';
                                            } elseif ($today >= $start && $today <= $end) {
                                                $displayStatusKey = 'DangDi';
                                            }
                                        }

                                        $stt = $statusMap[$displayStatusKey] ?? ['bg-secondary', $displayStatusKey];
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold"><?= htmlspecialchars($h['ten_tour']) ?></div>
                                            <?php if(isset($h['vai_tro'])): ?>
                                                <small class="text-muted">Vai trò: <?= $h['vai_tro'] ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="small"><i class="bi bi-arrow-right-circle me-1"></i><?= date('d/m/Y', $start) ?></div>
                                            <div class="small"><i class="bi bi-flag-fill me-1"></i><?= date('d/m/Y', $end) ?></div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge <?= $stt[0] ?>"><?= $stt[1] ?></span>
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
    </div>
</div>
</body>
</html>