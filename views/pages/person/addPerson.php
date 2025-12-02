<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm nhân sự</title>
    <style>
        .form-control {
            border: 1px solid #d2d6da !important;
            padding: 0.5rem 0.75rem;
            border-radius: 0.375rem;
        }

        .form-control:focus {
            border-color: #e91e63 !important;
            box-shadow: 0 0 0 2px rgba(233, 30, 99, 0.25);
        }

        .form-select {
            border: 1px solid #d2d6da !important;
            padding: 0.5rem 0.75rem;
            border-radius: 0.375rem;
        }

        .form-select:focus {
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
        }
    </style>
</head>

<body>
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Thêm nhân sự mới</h6>
                    </div>
                </div>
                <div class="card-body px-4 py-4">
                    <form action="index.php?action=addPerson" method="POST" enctype="multipart/form-data">
                        
                        <h6 class="section-title">1. Thông tin cá nhân</h6>
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Họ và tên</label>
                                <input type="text" class="form-control" name="HoTen" placeholder="Nhập họ tên" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Ngày sinh</label>
                                <input type="date" class="form-control" name="NgaySinh" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Giới tính</label>
                                <select class="form-select" name="GioiTinh">
                                    <option value="Nam">Nam</option>
                                    <option value="Nữ">Nữ</option>
                                    <option value="Khác">Khác</option>
                                </select>
                            </div>
                            <div class="col-md-4">
<label class="form-label fw-bold">CCCD/CMND</label>
                                <input type="number" class="form-control" name="CCCD" placeholder="Số CCCD" required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-bold">Ảnh đại diện</label>
                                <input type="file" class="form-control" name="AnhDaiDien" required>
                            </div>
                        </div>

                        <hr class="horizontal dark my-4">

                        <h6 class="section-title">2. Thông tin liên hệ</h6>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Số điện thoại</label>
                                <input type="number" class="form-control" name="SDT" placeholder="09xxxxxxxx" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email</label>
                                <input type="email" class="form-control" name="Email" placeholder="abc@gmail.com" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Địa chỉ thường trú</label>
                                <input type="text" class="form-control" name="DiaChi" placeholder="Số nhà, đường, phường/xã..." required>
                            </div>
                        </div>

                        <hr class="horizontal dark my-4">

                        <h6 class="section-title">3. Thông tin nghề nghiệp & Thẻ</h6>
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Mã thẻ HDV</label>
                                <input type="text" class="form-control" name="MaThe" placeholder="Ví dụ: 17923xxxx" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Loại thẻ HDV</label>
                                <select class="form-select" name="LoaiHDV">
                                    <option value="Nội địa">Nội địa</option>
                                    <option value="Quốc tế">Quốc tế</option>
                                </select>
                            </div>
                             <div class="col-md-4">
                                <label class="form-label fw-bold">Trạng thái hiện tại</label>
                                <select class="form-select" name="TrangThai">
                                    <option value="Rảnh">Rảnh</option>
<option value="Bận">Bận</option>
                                    <option value="Đang đi tour">Đang đi tour</option>
                                    <option value="Nghỉ Phép">Nghỉ Phép</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Ngôn ngữ thành thạo</label>
                                <input type="text" class="form-control" name="NgonNgu" placeholder="Anh, Pháp, Trung..." required>
                            </div>
                             <div class="col-md-6">
                                <label class="form-label fw-bold">Tình trạng sức khoẻ</label>
                                <input type="text" class="form-control" name="TinhTrangSucKhoe" placeholder="" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Kinh nghiệm làm việc</label>
                                <textarea class="form-control" name="KinhNghiem" rows="3" placeholder="Mô tả sơ lược kinh nghiệm..."></textarea>
                            </div>
                        </div>

                        <input type="hidden" name="Sotour" value="0">
                        <input type="hidden" name="DanhGia" value="0">

                        <div class="text-end mt-4">
                            <a href="index.php?controller=person" class="btn btn-light m-0">Quay lại</a>
                            <button type="submit" class="btn bg-gradient-primary m-0 ms-2">Lưu nhân sự</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>