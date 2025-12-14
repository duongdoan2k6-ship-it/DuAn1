<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Thêm Nhân Sự</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4 mb-5">
    <div class="card shadow col-md-10 mx-auto">
        <div class="card-header bg-success text-white fw-bold">➕ Thêm Hồ Sơ Nhân Sự</div>
        <div class="card-body">
            <form action="<?= BASE_URL ?>routes/index.php?action=admin-guide-store" method="POST" enctype="multipart/form-data">
                
                <h5 class="text-success mb-3 border-bottom pb-2">1. Tài khoản & Thông tin cơ bản</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Email (Đăng nhập) <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" name="mat_khau" class="form-control" required placeholder="Tối thiểu 6 ký tự">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Họ Tên <span class="text-danger">*</span></label>
                        <input type="text" name="ho_ten" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Số Điện Thoại <span class="text-danger">*</span></label>
                        <input type="text" name="sdt" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Ngày Sinh</label>
                        <input type="date" name="ngay_sinh" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Ảnh Đại Diện</label>
                        <input type="file" name="anh_dai_dien" class="form-control" accept="image/*">
                    </div>
                </div>

                <h5 class="text-success mb-3 border-bottom pb-2 mt-3">2. Thông tin nghiệp vụ</h5>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="fw-bold">Vai Trò <span class="text-danger">*</span></label>
                        <select name="phan_loai_nhan_su" class="form-select" required>
                            <option value="HDV">Hướng Dẫn Viên</option>
                            <option value="TaiXe">Tài Xế</option>
                        </select>
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <label>Sức khỏe</label>
                        <input type="text" name="suc_khoe" class="form-control" value="Tốt">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label>Chứng chỉ / Bằng cấp</label>
                        <textarea name="chung_chi" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label>Kinh nghiệm</label>
                        <textarea name="kinh_nghiem" class="form-control" rows="3"></textarea>
                    </div>
                </div>

                <div class="text-end">
                    <a href="<?= BASE_URL ?>routes/index.php?action=admin-guides" class="btn btn-secondary">Hủy</a>
                    <button type="submit" class="btn btn-success px-4">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>