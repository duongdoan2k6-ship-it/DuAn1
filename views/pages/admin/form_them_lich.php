<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thêm Lịch Khởi Hành</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/confirmDate/confirmDate.css">
</head>

<body class="bg-light">
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-danger" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-octagon-fill fs-4 me-2"></i>
                <div>
                    <strong>Rất tiếc!</strong> <?= htmlspecialchars($_GET['error']) ?>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-9">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">➕ Thêm Lịch Khởi Hành</h4>
                        <a href="<?= BASE_URL ?>routes/index.php?action=admin-dashboard" class="btn btn-sm btn-light">Quay lại</a>
                    </div>
                    <div class="card-body">
                        <form id="formLich" action="<?= BASE_URL ?>routes/index.php?action=admin-store-lich" method="POST">

                            <div class="row">
                                <div class="col-md-6 border-end">
                                    <h5 class="text-primary border-bottom pb-2">1. Thông tin Lịch trình</h5>

                                    <div class="mb-3">
                                        <label class="fw-bold">Chọn Tour <span class="text-danger">*</span></label>
                                        <select name="tour_id" id="tour_select" class="form-select" required>
                                            <option value="" data-days="0">-- Chọn tour --</option>
                                            <?php foreach ($tours as $t): ?>
                                                <option value="<?= $t['id'] ?>" data-days="<?= $t['so_ngay'] ?>">
                                                    <?= $t['ten_tour'] ?> (<?= $t['so_ngay'] ?> ngày)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="fw-bold">Ngày Giờ Khởi Hành <span class="text-danger">*</span></label>
                                            <small class="text-muted d-block mb-1">(Phải cách hôm nay ít nhất 3 ngày)</small>
                                            <input type="text" id="ngay_khoi_hanh" name="ngay_khoi_hanh"
                                                class="form-control datetimepicker" placeholder="Chọn ngày đi..." required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="fw-bold">Ngày Giờ Kết Thúc</label>
                                            <small class="text-muted d-block mb-1">(Tự động tính toán)</small>
                                            <input type="text" id="ngay_ket_thuc" name="ngay_ket_thuc"
                                                class="form-control datetimepicker"
                                                placeholder=""
                                                style="background-color: #e9ecef; cursor: not-allowed;"
                                                readonly required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="fw-bold">Điểm Tập Trung / Đón Khách</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                            <input type="text" name="diem_tap_trung" class="form-control" placeholder="VD: Nhà Hát Lớn, 05:00 AM" required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="fw-bold">Số chỗ tối đa</label>
                                        <input type="number" name="so_cho_toi_da" class="form-control" value="40" min="1" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <h5 class="text-primary border-bottom pb-2">2. Phân Bổ Nhân Sự</h5>
                                    
                                    <div class="alert alert-info py-2" style="font-size: 0.9rem;">
                                        <i class="bi bi-info-circle"></i> Vui lòng chọn ngày khởi hành trước.
                                    </div>

                                    <div class="mb-3">
                                        <label class="fw-bold text-success">Hướng Dẫn Viên (Chính)</label>
                                        <select name="hdv_id" id="hdv_select" class="form-select" required disabled>
                                            <option value="">-- Vui lòng chọn ngày trước --</option>
                                            <?php foreach ($listHDV as $g): 
                                                if ($g['trang_thai'] !== 'SanSang' && $g['trang_thai'] !== 'DangBan') continue;
                                            ?>
                                                <option value="<?= $g['id'] ?>" data-name="<?= $g['ho_ten'] ?> (<?= $g['sdt'] ?>)">
                                                    <?= $g['ho_ten'] ?> (<?= $g['sdt'] ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="fw-bold text-secondary">Tài Xế</label>
                                        <select name="taixe_id" id="taixe_select" class="form-select" required disabled>
                                            <option value="">-- Vui lòng chọn ngày trước --</option>
                                            <?php foreach ($listTaiXe as $g): 
                                                if ($g['trang_thai'] !== 'SanSang' && $g['trang_thai'] !== 'DangBan') continue;
                                            ?>
                                                <option value="<?= $g['id'] ?>" data-name="<?= $g['ho_ten'] ?> (<?= $g['sdt'] ?>)">
                                                    <?= $g['ho_ten'] ?> (<?= $g['sdt'] ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="fw-bold">Ghi chú Nhân sự (Biển số/Phụ xe...)</label>
                                        <textarea name="ghi_chu_nhan_su" class="form-control" rows="5"
                                            placeholder="- Biển số: 29B-12345&#10;- Phụ xe: Trần Văn B..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <div class="d-flex justify-content-end gap-2">
                                <a href="<?= BASE_URL ?>routes/index.php?action=admin-dashboard" class="btn btn-secondary">Hủy bỏ</a>
                                <button type="submit" class="btn btn-success px-4 fw-bold">💾 Tạo Lịch Trình</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/vn.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const schedules = <?= json_encode($futureSchedules ?? []) ?>;

            const baseConfig = {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                altInput: true,
                altFormat: "d/m/Y H:i",
                time_24hr: true,
                locale: "vn"
            };
            const startConfig = {
                ...baseConfig,
                minDate: "<?= date('Y-m-d', strtotime('+4 days')) ?>"
            };
            const endConfig = {
                ...baseConfig,
                clickOpens: false,
                allowInput: false
            };

            const fp_start = flatpickr("#ngay_khoi_hanh", startConfig);
            const fp_end = flatpickr("#ngay_ket_thuc", endConfig);
            const tourSelect = document.getElementById('tour_select');
            const hdvSelect = document.getElementById('hdv_select');
            const taixeSelect = document.getElementById('taixe_select');

            // 2. THÊM ĐOẠN VALIDATION NÀY
            const form = document.getElementById('formLich');
            form.addEventListener('submit', function(e) {
                const startDate = document.getElementById('ngay_khoi_hanh').value;
                if (!startDate) {
                    e.preventDefault(); // Ngừng gửi form
                    alert('Vui lòng chọn Ngày Giờ Khởi Hành!');
                    fp_start.open(); // Tự động mở lịch lên cho người dùng chọn
                }
            });

            // Logic cũ giữ nguyên
            function checkBusy(staffId, start, end) {
                const checkStart = new Date(start);
                const checkEnd = new Date(end);

                for (let s of schedules) {
                    if (s.nhan_vien_id == staffId) {
                        const busStart = new Date(s.ngay_khoi_hanh);
                        const busyEnd = new Date(s.ngay_ket_thuc);
                        if (checkStart < busyEnd && checkEnd > busStart) {
                            return s.ten_tour;
                        }
                    }
                }
                return null;
            }

            function updateStaffAvailability() {
                const startDateStr = document.getElementById('ngay_khoi_hanh').value;
                const endDateStr = document.getElementById('ngay_ket_thuc').value;

                if (!startDateStr || !endDateStr) {
                    hdvSelect.disabled = true;
                    taixeSelect.disabled = true;
                    hdvSelect.firstElementChild.textContent = "-- Vui lòng chọn ngày trước --";
                    taixeSelect.firstElementChild.textContent = "-- Vui lòng chọn ngày trước --";
                    return;
                }

                hdvSelect.disabled = false;
                taixeSelect.disabled = false;
                hdvSelect.firstElementChild.textContent = "-- Chọn Hướng Dẫn Viên --";
                taixeSelect.firstElementChild.textContent = "-- Chọn Tài Xế --";

                // Xử lý HDV
                Array.from(hdvSelect.options).forEach(opt => {
                    if (!opt.value) return; 
                    const busyTour = checkBusy(opt.value, startDateStr, endDateStr);
                    const originalName = opt.getAttribute('data-name');
                    if (busyTour) {
                        opt.textContent = "⛔ " + originalName + " (Bận: " + busyTour + ")";
                        opt.disabled = true;
                        opt.style.color = '#dc3545';
                        opt.style.fontWeight = 'bold';
                    } else {
                        opt.textContent = originalName;
                        opt.disabled = false;
                        opt.style.color = '';
                        opt.style.fontWeight = '';
                    }
                });

                // Xử lý Tài Xế
                Array.from(taixeSelect.options).forEach(opt => {
                    if (!opt.value) return;
                    const busyTour = checkBusy(opt.value, startDateStr, endDateStr);
                    const originalName = opt.getAttribute('data-name');
                    if (busyTour) {
                        opt.textContent = "⛔ " + originalName + " (Bận: " + busyTour + ")";
                        opt.disabled = true;
                        opt.style.color = '#dc3545';
                        opt.style.fontWeight = 'bold';
                    } else {
                        opt.textContent = originalName;
                        opt.disabled = false;
                        opt.style.color = '';
                        opt.style.fontWeight = '';
                    }
                });
            }

            function calculateEndDate() {
                const startDateStr = document.getElementById('ngay_khoi_hanh').value;
                if (!startDateStr) return;
                const selectedOption = tourSelect.options[tourSelect.selectedIndex];
                const days = parseInt(selectedOption.getAttribute('data-days')) || 0;

                if (days > 0) {
                    const startDate = new Date(startDateStr);
                    const endDate = new Date(startDate);
                    endDate.setDate(endDate.getDate() + (days - 1));
                    endDate.setHours(17, 0, 0, 0);
                    fp_end.setDate(endDate);
                }
                updateStaffAvailability();
            }

            document.getElementById('ngay_khoi_hanh').addEventListener('change', calculateEndDate);
            tourSelect.addEventListener('change', calculateEndDate);
        });
    </script>
</body>

</html>