<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Cập Nhật Hồ Sơ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4 mb-5">
        <div class="card shadow col-md-10 mx-auto">
            <div class="card-header bg-warning text-dark fw-bold">✏️ Cập Nhật Hồ Sơ: <?= htmlspecialchars($guide['ho_ten']) ?></div>
            <div class="card-body">
                <form action="<?= BASE_URL ?>routes/index.php?action=admin-guide-update" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $guide['id'] ?>">

                    <h5 class="text-warning text-dark mb-3 border-bottom pb-2">1. Thông tin cá nhân</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Họ và Tên</label>
                            <input type="text" name="ho_ten" class="form-control" required value="<?= htmlspecialchars($guide['ho_ten']) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Email</label>
                            <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($guide['email']) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Mật khẩu mới (Để trống nếu không đổi)</label>
                            <input type="password" name="mat_khau_moi" class="form-control" placeholder="******">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Số điện thoại</label>
                            <input type="text" name="sdt" class="form-control" value="<?= htmlspecialchars($guide['sdt']) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Ngày sinh</label>
                            <input type="date" name="ngay_sinh" class="form-control" value="<?= $guide['ngay_sinh'] ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Ảnh Đại Diện</label>
                            <div class="d-flex">
                                <img src="<?= BASE_URL ?>assets/uploads/hdv/<?= $guide['anh_dai_dien'] ?>" class="rounded-circle me-3" width="50" height="50">
                                <input type="file" name="anh_dai_dien" class="form-control">
                            </div>
                        </div>
                    </div>

                    <h5 class="text-warning text-dark mb-3 border-bottom pb-2 mt-3">2. Thông tin nghiệp vụ</h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="fw-bold">Vai Trò</label>
                            <select name="phan_loai_nhan_su" class="form-select">
                                <option value="HDV" <?= $guide['phan_loai_nhan_su'] == 'HDV' ? 'selected' : '' ?>>Hướng Dẫn Viên</option>
                                <option value="TaiXe" <?= $guide['phan_loai_nhan_su'] == 'TaiXe' ? 'selected' : '' ?>>Tài Xế</option>
                                <option value="HauCan" <?= $guide['phan_loai_nhan_su'] == 'HauCan' ? 'selected' : '' ?>>Hậu Cần</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="fw-bold">Phân loại</label>
                            <select name="phan_loai" class="form-select">
                                <option value="NoiDia" <?= $guide['phan_loai'] == 'NoiDia' ? 'selected' : '' ?>>Nội Địa</option>
                                <option value="QuocTe" <?= $guide['phan_loai'] == 'QuocTe' ? 'selected' : '' ?>>Quốc Tế</option>
                                <option value="CongTacVien" <?= $guide['phan_loai'] == 'CongTacVien' ? 'selected' : '' ?>>CTV</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="fw-bold">Trạng thái</label>
                            <select name="trang_thai" class="form-select">
                                <option value="SanSang" <?= $guide['trang_thai'] == 'SanSang' ? 'selected' : '' ?>>Sẵn Sàng</option>
                                <option value="DangBan" <?= $guide['trang_thai'] == 'DangBan' ? 'selected' : '' ?>>Đang Bận</option>
                                <option value="NghiPhep" <?= $guide['trang_thai'] == 'NghiPhep' ? 'selected' : '' ?>>Nghỉ/Khóa</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Kinh nghiệm</label>
                            <textarea name="kinh_nghiem" class="form-control" rows="3"><?= htmlspecialchars($guide['kinh_nghiem']) ?></textarea>
                        </div>
                    </div>

                    <div class="text-end">
                        <a href="<?= BASE_URL ?>routes/index.php?action=admin-guides" class="btn btn-secondary">Hủy</a>
                        <button type="submit" class="btn btn-warning fw-bold">Cập Nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>