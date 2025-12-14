<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Lịch Khởi Hành & Phân Công</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .section-title {
            border-bottom: 2px solid #ffc107;
            padding-bottom: 10px;
            margin-bottom: 20px;
            color: #333;
            font-weight: bold;
        }
        .readonly-field {
            background-color: #e9ecef;
            cursor: not-allowed;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5 mb-5">
        
        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle"></i> 
                <?php 
                    $msg = $_GET['msg'];
                    if($msg == 'updated') echo 'Cập nhật thông tin thành công!';
                    elseif($msg == 'Staff assigned successfully') echo 'Đã thêm nhân sự vào đoàn!';
                    elseif($msg == 'Staff removed') echo 'Đã gỡ nhân sự khỏi đoàn!';
                    else echo htmlspecialchars($msg);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($_GET['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow">
                    <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-pencil-square"></i> Quản Lý Chuyến Đi #<?= $lich['id'] ?></h5>
                        <a href="<?= BASE_URL ?>routes/index.php?action=admin-dashboard" class="btn btn-sm btn-dark">
                            <i class="bi bi-arrow-left"></i> Quay Lại
                        </a>
                    </div>
                    <div class="card-body">
                        
                        <h6 class="section-title"><i class="bi bi-info-circle-fill"></i> I. Thông Tin Lịch Trình</h6>
                        <form action="<?= BASE_URL ?>routes/index.php?action=admin-update-lich" method="POST">
                            <input type="hidden" name="id" value="<?= $lich['id'] ?>">
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Tour Du Lịch <small class="text-muted">(Không được đổi)</small></label>
                                    <select class="form-select readonly-field" disabled>
                                        <option selected><?= $lich['ten_tour'] ?></option>
                                    </select>
                                    <input type="hidden" name="tour_id" value="<?= $lich['tour_id'] ?>">
                                </div>
                                
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">Ngày Khởi Hành</label>
                                    <input type="date" class="form-control readonly-field" 
                                           value="<?= date('Y-m-d', strtotime($lich['ngay_khoi_hanh'])) ?>" readonly>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold text-primary">Giờ Khởi Hành</label>
                                    <input type="time" name="gio_khoi_hanh" class="form-control border-primary" 
                                           value="<?= date('H:i', strtotime($lich['ngay_khoi_hanh'])) ?>" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Ngày Kết Thúc</label>
                                            <input type="date" class="form-control readonly-field" 
                                                   value="<?= date('Y-m-d', strtotime($lich['ngay_ket_thuc'])) ?>" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-primary">Giờ Kết Thúc</label>
                                            <input type="time" name="gio_ket_thuc" class="form-control border-primary" 
                                                   value="<?= date('H:i', strtotime($lich['ngay_ket_thuc'])) ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Điểm Tập Trung</label>
                                    <input type="text" name="diem_tap_trung" class="form-control" 
                                           value="<?= htmlspecialchars($lich['diem_tap_trung']) ?>" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Số Chỗ Tối Đa</label>
                                    <input type="number" name="so_cho_toi_da" class="form-control" 
                                           value="<?= $lich['so_cho_toi_da'] ?>" 
                                           min="<?= $lich['so_cho_da_dat'] ?>" required>
                                    <small class="text-danger fst-italic">
                                        Đã đặt: <?= $lich['so_cho_da_dat'] ?> chỗ (Không thể giảm thấp hơn số này)
                                    </small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Trạng Thái</label>
                                    <select name="trang_thai" class="form-select">
                                        <?php 
                                            $states = ['NhanKhach' => 'Nhận Khách', 'KhongNhanThemKhach' => 'Không nhận thêm khách'];
                                            foreach ($states as $key => $label): 
                                        ?>
                                            <option value="<?= $key ?>" <?= $key == $lich['trang_thai'] ? 'selected' : '' ?>>
                                                <?= $label ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="text-end border-bottom pb-4 mb-4">
                                <button type="submit" class="btn btn-warning fw-bold px-4">
                                    <i class="bi bi-save"></i> Lưu Thay Đổi
                                </button>
                            </div>
                        </form>

                        <h6 class="section-title text-primary" style="border-color: #0d6efd;"><i class="bi bi-people-fill"></i> II. Phân Công Nhân Sự (HDV & Tài Xế)</h6>
                        
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-primary">
                                    <tr>
                                        <th>Họ Tên</th>
                                        <th>Vai Trò</th>
                                        <th>SĐT Liên Hệ</th>
                                        <th>Loại Hình</th>
                                        <th class="text-center">Hành Động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($assignedStaff)): ?>
                                        <?php foreach ($assignedStaff as $s): ?>
                                            <tr>
                                                <td class="fw-bold"><?= $s['ho_ten'] ?></td>
                                                <td>
                                                    <?php 
                                                        $roles = [
                                                            'HDV_chinh' => '<span class="badge bg-success">HDV Chính</span>',
                                                            'HDV_phu' => '<span class="badge bg-info">HDV Phụ</span>',
                                                            'TaiXe' => '<span class="badge bg-secondary">Tài Xế</span>',
                                                            'HauCan' => '<span class="badge bg-light text-dark">Hậu Cần</span>'
                                                        ];
                                                        echo $roles[$s['vai_tro']] ?? $s['vai_tro'];
                                                    ?>
                                                </td>
                                                <td><?= $s['sdt'] ?></td>
                                                <td><?= $s['phan_loai_nhan_su'] ?></td>
                                                <td class="text-center">
                                                    <a href="<?= BASE_URL ?>routes/index.php?action=admin-remove-staff&id=<?= $s['id'] ?>&lich_id=<?= $lich['id'] ?>" 
                                                       class="btn btn-sm btn-outline-danger" 
                                                       onclick="return confirm('Bạn có chắc muốn gỡ nhân sự này khỏi đoàn?')">
                                                        <i class="bi bi-trash"></i> Gỡ
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted fst-italic py-3">Chưa có nhân sự nào được phân công.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="bg-light p-3 rounded border">
                            <h6 class="fw-bold mb-3">➕ Thêm nhân sự vào đoàn:</h6>
                            <form action="<?= BASE_URL ?>routes/index.php?action=admin-add-staff" method="POST" class="row g-2 align-items-center">
                                <input type="hidden" name="lich_id" value="<?= $lich['id'] ?>">
                                
                                <div class="col-md-5">
                                    <select name="nhan_vien_id" class="form-select" required>
                                        <option value="">-- Chọn nhân sự (HDV/Tài xế) --</option>
                                        <?php if(isset($allStaff)): ?>
                                            <?php foreach ($allStaff as $nv): ?>
                                                <option value="<?= $nv['id'] ?>">
                                                    <?= $nv['ho_ten'] ?> (<?= $nv['phan_loai_nhan_su'] ?>) - <?= $nv['sdt'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select name="vai_tro" class="form-select" required>
                                        <option value="">-- Chọn vai trò --</option>
                                        <option value="HDV_chinh">Hướng Dẫn Viên Chính</option>
                                        <option value="HDV_phu">Hướng Dẫn Viên Phụ</option>
                                        <option value="TaiXe">Tài Xế</option>
                                        <option value="HauCan">Nhân viên Hậu cần</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-person-plus-fill"></i> Phân Công
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>