<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thêm Lịch Khởi Hành</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/confirmDate/confirmDate.css">
</head>

<body class="bg-light">
    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-9">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">➕ Thêm Lịch Khởi Hành</h4>
                        <a href="<?= BASE_URL ?>routes/index.php?action=admin-dashboard" class="btn btn-sm btn-light">Quay lại</a>
                    </div>
                    <div class="card-body">
                        <form action="<?= BASE_URL ?>routes/index.php?action=admin-store-lich" method="POST">

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
                                            <label class="fw-bold">Ngày Giờ Khởi Hành</label>
                                            <small class="text-muted d-block mb-1">(Phải cách hôm nay ít nhất 3 ngày)</small>
                                            <input type="text" id="ngay_khoi_hanh" name="ngay_khoi_hanh" 
                                                   class="form-control datetimepicker" placeholder="Chọn ngày đi..." required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="fw-bold">Ngày Giờ Kết Thúc</label>
                                            <small class="text-muted d-block mb-1">(Tự động tính toán)</small>
                                            <input type="text" id="ngay_ket_thuc" name="ngay_ket_thuc" 
                                                   class="form-control datetimepicker" 
                                                   placeholder="Chọn tour và ngày đi..." 
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
                                    
                                    <div class="mb-3">
                                        <label class="fw-bold">Hướng Dẫn Viên (Chính)</label>
                                        <select name="hdv_id" class="form-select">
                                            <option value="">-- Chưa phân công --</option>
                                            <?php foreach ($guides as $g): ?>
                                                <option value="<?= $g['id'] ?>">
                                                    <?= $g['ho_ten'] ?> 
                                                    (<?= $g['phan_loai'] == 'NoiDia' ? 'Nội địa' : 'Quốc tế' ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="form-text text-muted fst-italic">
                                            * Bạn có thể phân công thêm Tài xế/Hậu cần sau khi tạo xong.
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="fw-bold">Ghi chú Nhân sự (Tài xế/Hậu cần)</label>
                                        <textarea name="ghi_chu_nhan_su" class="form-control" rows="6"
                                            placeholder="- Tài xế: Nguyễn Văn A (09xxxx)&#10;- Biển số: 29B-12345&#10;- Phụ xe: Trần Văn B..."></textarea>
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
            // Cấu hình cơ bản
            const baseConfig = {
                enableTime: true,       
                dateFormat: "Y-m-d H:i", 
                altInput: true,         
                altFormat: "d/m/Y H:i", 
                time_24hr: true,        
                locale: "vn"           
            };

            // 1. Cấu hình NGÀY ĐI: Chặn ngày quá khứ + 3 ngày
            const startConfig = {
                ...baseConfig,
                // Dùng PHP để in ra ngày tối thiểu (Hiện tại + 3 ngày)
                minDate: "<?= date('Y-m-d', strtotime('+4 days')) ?>" 
            };

            // 2. Cấu hình NGÀY VỀ: Tắt chức năng mở lịch (chỉ hiển thị)
            const endConfig = {
                ...baseConfig,
                clickOpens: false, // Quan trọng: Không cho người dùng bấm mở lịch
                allowInput: false  // Không cho nhập tay
            };

            // Khởi tạo Flatpickr
            const fp_start = flatpickr("#ngay_khoi_hanh", startConfig);
            const fp_end = flatpickr("#ngay_ket_thuc", endConfig);

            // --- TÍNH NĂNG TỰ ĐỘNG TÍNH NGÀY VỀ ---
            const tourSelect = document.getElementById('tour_select');
            
            function calculateEndDate() {
                // Lấy ngày đi đang chọn
                const startDateStr = document.getElementById('ngay_khoi_hanh').value;
                if (!startDateStr) return;

                // Lấy số ngày của tour từ attribute data-days
                const selectedOption = tourSelect.options[tourSelect.selectedIndex];
                const days = parseInt(selectedOption.getAttribute('data-days')) || 0;

                if (days > 0) {
                    const startDate = new Date(startDateStr);

                    const endDate = new Date(startDate);
                    endDate.setDate(endDate.getDate() + (days - 1)); 

                    endDate.setHours(17, 0, 0, 0);

                    fp_end.setDate(endDate);
                }
            }
            document.getElementById('ngay_khoi_hanh').addEventListener('change', calculateEndDate);
            tourSelect.addEventListener('change', calculateEndDate);
        });
    </script>
</body>
</html>