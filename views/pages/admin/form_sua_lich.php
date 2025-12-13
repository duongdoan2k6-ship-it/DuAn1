<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Lịch Khởi Hành</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">✏️ Cập Nhật Lịch Trình #<?= $lich['id'] ?></h4>
                        <a href="<?= BASE_URL ?>routes/index.php?action=admin-dashboard" class="btn btn-sm btn-dark">
                            <i class="bi bi-arrow-left"></i> Quay Lại
                        </a>
                    </div>
                    <div class="card-body">
                        <form action="<?= BASE_URL ?>routes/index.php?action=admin-update-lich&id=<?= $lich['id'] ?>" method="POST">
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tour Du Lịch</label>
                                <select name="tour_id" class="form-select" required>
                                    <?php foreach ($tours as $t): ?>
                                        <option value="<?= $t['id'] ?>" <?= $t['id'] == $lich['tour_id'] ? 'selected' : '' ?>>
                                            <?= $t['ten_tour'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Ngày Giờ Khởi Hành</label>
                                    <input type="datetime-local" name="ngay_khoi_hanh" class="form-control" 
                                           value="<?= date('Y-m-d\TH:i', strtotime($lich['ngay_khoi_hanh'])) ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Ngày Giờ Kết Thúc</label>
                                    <input type="datetime-local" name="ngay_ket_thuc" class="form-control" 
                                           value="<?= date('Y-m-d\TH:i', strtotime($lich['ngay_ket_thuc'])) ?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Điểm Tập Trung</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                    <input type="text" name="diem_tap_trung" class="form-control" 
                                           value="<?= htmlspecialchars($lich['diem_tap_trung']) ?>" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Số Chỗ Tối Đa</label>
                                    <input type="number" name="so_cho_toi_da" class="form-control" 
                                           value="<?= $lich['so_cho_toi_da'] ?>" min="1" required>
                                    <div class="form-text text-danger">
                                        Đã có <b><?= $lich['so_cho_da_dat'] ?></b> khách đặt chỗ. (Không thể giảm thấp hơn số này)
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Trạng Thái</label>
                                    <select name="trang_thai" class="form-select">
                                        <?php 
                                            $states = ['NhanKhach' => 'Nhận Khách', 'DaDay' => 'Đã Đầy', 'DangDi' => 'Đang Đi', 'HoanThanh' => 'Hoàn Thành', 'Huy' => 'Hủy'];
                                            foreach ($states as $key => $label): 
                                        ?>
                                            <option value="<?= $key ?>" <?= $key == $lich['trang_thai'] ? 'selected' : '' ?>>
                                                <?= $label ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="alert alert-info d-flex align-items-center" role="alert">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                <div>
                                    Để thay đổi <b>Hướng dẫn viên</b> hoặc <b>Tài xế</b>, vui lòng sử dụng chức năng 
                                    <a href="<?= BASE_URL ?>routes/index.php?action=admin-schedule-staff&id=<?= $lich['id'] ?>" class="fw-bold text-decoration-underline">Phân bổ nhân sự</a>.
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-3">
                                <button type="submit" class="btn btn-warning fw-bold px-4">
                                    <i class="bi bi-save"></i> Cập Nhật
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>