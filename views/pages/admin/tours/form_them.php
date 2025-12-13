<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Thêm Tour Mới</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">
<div class="container mt-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="text-primary fw-bold">➕ Thêm Tour Mới</h3>
        <a href="index.php?action=admin-tours" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <form action="index.php?action=admin-tour-store" method="POST" enctype="multipart/form-data">
                
                <ul class="nav nav-tabs" id="tourTab" role="tablist">
                    <li class="nav-item"><button class="nav-link active fw-bold" data-bs-toggle="tab" data-bs-target="#general" type="button">Thông tin chung</button></li>
                    <li class="nav-item"><button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#itinerary" type="button">Lịch trình</button></li>
                    <li class="nav-item"><button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#policy" type="button">Chính sách</button></li>
                    <li class="nav-item"><button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#suppliers" type="button">Đối tác & NCC</button></li>
                    <li class="nav-item"><button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#gallery" type="button">Thư viện ảnh</button></li>
                </ul>

                <div class="tab-content mt-4">
                    
                    <div class="tab-pane fade show active" id="general">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tên Tour <span class="text-danger">*</span></label>
                                <input type="text" name="ten_tour" class="form-control" required placeholder="VD: Hà Giang Mùa Hoa...">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Loại Tour</label>
                                <select name="loai_tour_id" class="form-select">
                                    <?php foreach ($categories as $cate): ?>
                                        <option value="<?= $cate['id'] ?>"><?= $cate['ten_loai'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Giá Người Lớn <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="gia_nguoi_lon" class="form-control" required min="0">
                                    <span class="input-group-text">VNĐ</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Giá Trẻ Em <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="gia_tre_em" class="form-control" required min="0">
                                    <span class="input-group-text">VNĐ</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Thời gian (Ngày) <span class="text-danger">*</span></label>
                                <input type="number" name="so_ngay" class="form-control" required min="1" value="1">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Giới thiệu ngắn</label>
                                <textarea name="gioi_thieu" class="form-control" rows="3" placeholder="Mô tả hấp dẫn về tour..."></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Lịch trình tóm tắt</label>
                                <input type="text" name="lich_trinh_tom_tat" class="form-control" placeholder="VD: Ngày 1: Hà Nội - Sapa. Ngày 2: Fansipan...">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Ảnh đại diện (Thumbnail)</label>
                                <input type="file" name="anh_tour" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="itinerary">
                        <div id="itinerary-wrapper">
                            </div>
                        <button type="button" class="btn btn-outline-success mt-3 fw-bold" onclick="addDay()">
                            <i class="bi bi-plus-circle"></i> Thêm Ngày
                        </button>
                    </div>

                    <div class="tab-pane fade" id="policy">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Giá bao gồm</label>
                                <textarea name="bao_gom" class="form-control" rows="5" placeholder="- Xe đưa đón&#10;- Ăn uống..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Giá KHÔNG bao gồm</label>
                                <textarea name="khong_bao_gom" class="form-control" rows="5" placeholder="- Thuế VAT&#10;- Tiền tip..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Chính sách Hoàn/Hủy</label>
                                <textarea name="chinh_sach_huy" class="form-control" rows="5" placeholder="- Hủy trước 5 ngày..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Lưu ý</label>
                                <textarea name="luu_y" class="form-control" rows="5" placeholder="- Mang theo CMND..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="suppliers">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Tick chọn các nhà cung cấp (Khách sạn, Nhà xe...) sẽ phục vụ cho tour này.
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50" class="text-center">Chọn</th>
                                        <th>Tên Nhà Cung Cấp</th>
                                        <th>Dịch vụ</th>
                                        <th>Ghi chú sử dụng (Cho tour này)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($suppliers)): ?>
                                        <?php foreach($suppliers as $s): ?>
                                        <tr>
                                            <td class="text-center">
                                                <input class="form-check-input" type="checkbox" name="suppliers[]" value="<?= $s['id'] ?>">
                                            </td>
                                            <td class="fw-bold"><?= $s['ten_ncc'] ?></td>
                                            <td><span class="badge bg-secondary"><?= $s['dich_vu'] ?></span></td>
                                            <td>
                                                <input type="text" name="suppliers_note[<?= $s['id'] ?>]" class="form-control form-control-sm" placeholder="VD: 02 phòng Twin...">
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="4" class="text-center text-muted">Chưa có nhà cung cấp nào. Hãy thêm ở menu Đối tác.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="gallery">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Upload Album ảnh (Chọn nhiều)</label>
                            <input type="file" name="gallery[]" class="form-control" multiple accept="image/*">
                            <div class="form-text">Giữ phím Ctrl để chọn nhiều ảnh cùng lúc.</div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-end border-top pt-3">
                    <a href="index.php?action=admin-tours" class="btn btn-secondary me-2">Hủy bỏ</a>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">
                        <i class="bi bi-save"></i> Lưu Tour Mới
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let dayCount = 0;
    
    // Tự động thêm ngày 1 khi mở form
    document.addEventListener("DOMContentLoaded", function() {
        addDay();
    });

    function addDay() {
        dayCount++;
        const html = `
            <div class="card mb-3 border bg-light">
                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                    <strong>Ngày ${dayCount}</strong>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Tiêu đề (VD: Hà Nội - Sapa)</label>
                        <input type="text" name="itinerary_title[]" class="form-control bg-white" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold">Nội dung chi tiết</label>
                        <textarea name="itinerary_content[]" class="form-control bg-white" rows="3"></textarea>
                    </div>
                </div>
            </div>`;
        document.getElementById('itinerary-wrapper').insertAdjacentHTML('beforeend', html);
    }
</script>
</body>
</html>