<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm nhân sự</title>
    <style>
        .form-control {
            border: 1.5px solid #bfbfbf !important;
            padding: 10px 12px;
            border-radius: 6px;
        }

        .form-control:focus {
            border-color: #f16363ff !important;
            box-shadow: 0 0 0 0.15rem rgba(241, 99, 99, 0.25);
        }

        .form-control::placeholder {
            color: #b6b6b6 !important;
            opacity: 0.7 !important;
        }

        input[type="file"]::file-selector-button {
            border-radius: 6px;
            border: none;
            padding: 6px 12px;
            background-color: #e8e8e8;
        }

        input[type="file"] {
            color: #9f9f9f;
            opacity: 0.8;
        }
    </style>
</head>

<body>
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Thêm nhân sự</h6>
                    </div>
                </div>
                <div class="card-body px-4 py-4">
                    <form action="index.php?action=addPerson" method="POST" enctype="multipart/form-data">
                        <div class="row g-4">

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Họ và tên</label>
                                <input type="text" class="form-control" name="HoTen" placeholder="Nhập tên" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Ngày sinh</label>
                                <input type="date" class="form-control" name="NgaySinh" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Ảnh đại diện</label>
                                <input type="file" class="form-control" name="AnhDaiDien" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Số điện thoại</label>
                                <input type="number" class="form-control" name="ThongTinLienHe" placeholder="Nhập số điện thoại" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Ngôn ngữ</label>
                                <input type="text" class="form-control" name="NgonNgu" placeholder="Ví dụ: Tiếng Anh, Tiếng Trung" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Kinh nghiệm</label>
                                <input type="text" class="form-control" name="KinhNghiem" placeholder="Ví dụ: 2 năm" required>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Tình trạng sức khoẻ</label>
                                <input type="text" class="form-control" name="TinhTrangSucKhoe" placeholder="Tình trạng sức khoẻ hiện tại" required>
                            </div>

                        </div>

                        <button type="submit" class="btn btn-primary mt-4 px-4">Submit</button>
                    </form>

                </div>

            </div>
        </div>
    </div>
</body>

</html>