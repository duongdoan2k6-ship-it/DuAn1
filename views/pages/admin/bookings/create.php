<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Thêm Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .form-section-title {
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 10px;
            margin-bottom: 20px;
            color: #d63384; /* Màu thương hiệu hoặc màu nổi */
            font-weight: bold;
        }
    </style>
</head>
<body class="bg-light">
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4 shadow-sm">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-primary shadow-primary border-radius-lg pt-4 pb-3 rounded-3">
                        <h6 class="text-white text-capitalize ps-3">
                            <i class="bi bi-calendar-plus"></i> Tạo Booking Mới & Danh Sách Đoàn
                        </h6>
                    </div>
                </div>
                <div class="card-body px-4 pb-4">
                    <form action="index.php?action=admin-booking-store" method="POST" id="bookingForm">
                        
                        <h6 class="form-section-title text-primary"><i class="bi bi-info-circle"></i> I. Thông Tin Chuyến Đi & Người Đại Diện</h6>
                        
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Chọn Tour - Lịch Khởi Hành (*)</label>
                                <select name="lich_khoi_hanh_id" class="form-select border border-secondary p-2" required>
                                    <option value="">-- Chọn chuyến đi --</option>
                                    <?php if(isset($schedules) && is_array($schedules)): ?>
                                        <?php foreach ($schedules as $item): ?>
                                            <option value="<?= $item['id'] ?>">
                                                <?= $item['ten_tour'] ?> | 
                                                <?= date('d/m/Y', strtotime($item['ngay_khoi_hanh'])) ?> | 
                                                (Còn <?= $item['so_cho_toi_da'] - $item['so_cho_da_dat'] ?> chỗ)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Họ tên người đặt (Đại diện) (*)</label>
                                <input type="text" name="ten_nguoi_dat" class="form-control border border-secondary p-2" placeholder="Ví dụ: Nguyễn Văn A" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">SĐT Liên Hệ (*)</label>
                                <input type="text" name="sdt_lien_he" class="form-control border border-secondary p-2" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">Email</label>
                                <input type="email" name="email_lien_he" class="form-control border border-secondary p-2">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="form-section-title text-primary mb-0 border-0 p-0"><i class="bi bi-people"></i> II. Danh Sách Thành Viên Đoàn</h6>
                            <button type="button" class="btn btn-sm btn-success" onclick="addMemberRow()">
                                <i class="bi bi-person-plus-fill"></i> Thêm khách
                            </button>
                        </div>

                        <div class="table-responsive mb-4">
                            <table class="table table-bordered align-middle" id="membersTable">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th style="width: 5%">STT</th>
                                        <th style="width: 25%">Họ và Tên (*)</th>
                                        <th style="width: 15%">Giới tính</th>
                                        <th style="width: 15%">Loại khách</th>
                                        <th style="width: 15%">Ngày sinh</th>
                                        <th style="width: 20%">Ghi chú (Ăn chay, dị ứng...)</th>
                                        <th style="width: 5%">Xóa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="member-row">
                                        <td class="text-center fw-bold">1</td>
                                        <td><input type="text" name="members[0][name]" class="form-control form-control-sm border ps-2" placeholder="Nhập họ tên" required></td>
                                        <td>
                                            <select name="members[0][gender]" class="form-select form-select-sm border ps-2">
                                                <option value="Nam">Nam</option>
                                                <option value="Nu">Nữ</option>
                                                <option value="Khac">Khác</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="members[0][type]" class="form-select form-select-sm border ps-2" onchange="updateTotal()">
                                                <option value="NguoiLon">Người lớn</option>
                                                <option value="TreEm">Trẻ em</option>
                                            </select>
                                        </td>
                                        <td><input type="date" name="members[0][dob]" class="form-control form-control-sm border ps-2"></td>
                                        <td><input type="text" name="members[0][note]" class="form-control form-control-sm border ps-2"></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger disabled border-0" disabled><i class="bi bi-trash"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="row bg-white border rounded p-3 mb-4 mx-1">
                            <div class="col-md-8 d-flex align-items-center">
                                <div class="fs-5">
                                    <strong>Tổng cộng:</strong> 
                                    <span class="badge bg-info text-dark mx-1" id="display_adults">1 Người lớn</span>
                                    <span class="badge bg-warning text-dark mx-1" id="display_children">0 Trẻ em</span>
                                </div>
                                <input type="hidden" name="so_nguoi_lon" id="inp_so_nguoi_lon" value="1">
                                <input type="hidden" name="so_tre_em" id="inp_so_tre_em" value="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-danger">Tổng Tiền Dự Kiến (VNĐ)</label>
                                <input type="number" name="tong_tien" class="form-control border border-danger p-2 fw-bold text-danger" placeholder="Nhập số tiền...">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="index.php?action=admin-bookings" class="btn btn-secondary"><i class="bi bi-arrow-return-left"></i> Quay lại</a>
                            <button type="submit" class="btn btn-primary btn-lg px-5"><i class="bi bi-check-circle"></i> XÁC NHẬN ĐẶT TOUR</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let memberIndex = 1; // Bắt đầu từ 1 vì 0 là dòng đầu tiên

    function addMemberRow() {
        const tableBody = document.querySelector('#membersTable tbody');
        const row = document.createElement('tr');
        row.classList.add('member-row');
        
        row.innerHTML = `
            <td class="text-center fw-bold row-index"></td>
            <td><input type="text" name="members[${memberIndex}][name]" class="form-control form-control-sm border ps-2" placeholder="Nhập họ tên" required></td>
            <td>
                <select name="members[${memberIndex}][gender]" class="form-select form-select-sm border ps-2">
                    <option value="Nam">Nam</option>
                    <option value="Nu">Nữ</option>
                    <option value="Khac">Khác</option>
                </select>
            </td>
            <td>
                <select name="members[${memberIndex}][type]" class="form-select form-select-sm border ps-2" onchange="updateTotal()">
                    <option value="NguoiLon">Người lớn</option>
                    <option value="TreEm">Trẻ em</option>
                </select>
            </td>
            <td><input type="date" name="members[${memberIndex}][dob]" class="form-control form-control-sm border ps-2"></td>
            <td><input type="text" name="members[${memberIndex}][note]" class="form-control form-control-sm border ps-2"></td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="removeMemberRow(this)"><i class="bi bi-trash-fill"></i></button>
            </td>
        `;
        
        tableBody.appendChild(row);
        memberIndex++;
        updateRowNumbers();
        updateTotal();
    }

    function removeMemberRow(btn) {
        btn.closest('tr').remove();
        updateRowNumbers();
        updateTotal();
    }

    // Cập nhật số thứ tự (STT) sau khi xóa
    function updateRowNumbers() {
        const rows = document.querySelectorAll('.member-row');
        rows.forEach((row, index) => {
            row.querySelector('.row-index').textContent = index + 1;
        });
    }

    // Tính tổng người lớn / trẻ em
    function updateTotal() {
        let adults = 0;
        let children = 0;
        
        const selects = document.querySelectorAll('select[name*="[type]"]');
        selects.forEach(select => {
            if(select.value === 'NguoiLon') adults++;
            else children++;
        });

        // Hiển thị ra màn hình
        document.getElementById('display_adults').textContent = `${adults} Người lớn`;
        document.getElementById('display_children').textContent = `${children} Trẻ em`;

        // Gán vào input hidden để gửi đi
        document.getElementById('inp_so_nguoi_lon').value = adults;
        document.getElementById('inp_so_tre_em').value = children;
    }

    // Chạy lần đầu để đánh số STT dòng 1
    updateRowNumbers();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>