<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa thông tin nhân sự</title>
    <style>
        .form-control, .form-select {
            border: 1px solid #d2d6da !important;
            padding: 0.5rem 0.75rem;
            border-radius: 0.375rem;
            background-color: #fff;
        }

        .form-control:focus, .form-select:focus {
            border-color: #e91e63 !important;
            box-shadow: 0 0 0 2px rgba(233, 30, 99, 0.25);
        }

        .section-title {
            font-size: 0.9rem;
            font-weight: 700;
            color: #344767;
            margin-bottom: 1rem;
            margin-top: 1rem;
            text-transform: uppercase;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        
        .avatar-preview {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid #e91e63;
        }
    </style>
</head>

<body>
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Cập nhật thông tin nhân sự: <?= $person['HoTen'] ?></h6>
                    </div>
                </div>
                <div class="card-body px-4 py-4">
                    <form action="index.php?controller=person&action=updatePerson&id=<?= $person['MaHDV'] ?>" method="POST" enctype="multipart/form-data">
                        
                        <h6 class="section-title">1. Thông tin cá nhân</h6>
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Họ và tên</label>
                                <input type="text" class="form-control" name="HoTen" value="<?= $person['HoTen'] ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Ngày sinh</label>
                                <input type="date" class="form-control" name="NgaySinh" value="<?= $person['NgaySinh'] ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Giới tính</label>
                                <select class="form-select" name="GioiTinh">
                                    <option value="Nam" <?= $person['GioiTinh'] == 'Nam' ? 'selected' : '' ?>>Nam</option>
<input type="text" class="form-control" name="MaThe" value="<?= $person['MaThe'] ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Loại thẻ HDV</label>
                                <select class="form-select" name="LoaiHDV">
                                    <option value="Nội địa" <?= $person['LoaiHDV'] == 'Nội địa' ? 'selected' : '' ?>>Nội địa</option>
                                    <option value="Quốc tế" <?= $person['LoaiHDV'] == 'Quốc tế' ? 'selected' : '' ?>>Quốc tế</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Trạng thái hiện tại</label>
                                <select class="form-select" name="TrangThai">
                                    <option value="Rảnh" <?= $person['TrangThai'] == 'Rảnh' ? 'selected' : '' ?>>Rảnh</option>
                                    <option value="Bận" <?= $person['TrangThai'] == 'Bận' ? 'selected' : '' ?>>Bận</option>
                                    <option value="Đang đi tour" <?= $person['TrangThai'] == 'Đang đi tour' ? 'selected' : '' ?>>Đang đi tour</option>
                                    <option value="Nghỉ Phép" <?= $person['TrangThai'] == 'Nghỉ Phép' ? 'selected' : '' ?>>Nghỉ Phép</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Ngôn ngữ thành thạo</label>
                                <input type="text" class="form-control" name="NgonNgu" value="<?= $person['NgonNgu'] ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tình trạng sức khoẻ</label>
                                <input type="text" class="form-control" name="TinhTrangSucKhoe" value="<?= $person['TinhTrangSucKhoe'] ?>" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Kinh nghiệm làm việc</label>
                                <textarea class="form-control" name="KinhNghiem" rows="3"><?= $person['KinhNghiem'] ?></textarea>
                            </div>
                        </div>
                        
                        <input type="hidden" name="Sotour" value="<?= $person['Sotour'] ?>">
                        <input type="hidden" name="DanhGia" value="<?= $person['DanhGia'] ?>">

                        <div class="text-end mt-4">
                            <a href="index.php?controller=person" class="btn btn-light m-0">Hủy bỏ</a>
<option value="Nữ" <?= $person['GioiTinh'] == 'Nữ' ? 'selected' : '' ?>>Nữ</option>
                                    <option value="Khác" <?= $person['GioiTinh'] == 'Khác' ? 'selected' : '' ?>>Khác</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">CCCD/CMND</label>
                                <input type="text" class="form-control" name="CCCD" value="<?= $person['CCCD'] ?>" required>
                            </div>
                            
                            <div class="col-md-8">
                                <label class="form-label fw-bold">Ảnh đại diện</label>
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <img src="uploads/<?= $person['AnhDaiDien'] ?>" class="avatar-preview" alt="Current Image">
                                    </div>
                                    <div class="flex-grow-1">
                                        <input type="file" class="form-control" name="AnhDaiDien">
                                        <small class="text-muted">Chỉ chọn ảnh nếu muốn thay đổi ảnh hiện tại.</small>
                                        <input type="hidden" name="AnhCu" value="<?= $person['AnhDaiDien'] ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h6 class="section-title">2. Thông tin liên hệ</h6>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Số điện thoại</label>
                                <input type="text" class="form-control" name="SDT" value="<?= $person['SDT'] ?>" required> 
                                </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email</label>
                                <input type="email" class="form-control" name="Email" value="<?= $person['Email'] ?>" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Địa chỉ thường trú</label>
                                <input type="text" class="form-control" name="DiaChi" value="<?= $person['DiaChi'] ?>" required>
                            </div>
                        </div>

                        <h6 class="section-title">3. Thông tin nghề nghiệp & Thẻ</h6>
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Mã thẻ HDV</label>
<button type="submit" class="btn bg-gradient-info m-0 ms-2">Cập nhật thông tin</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>