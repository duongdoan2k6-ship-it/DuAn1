<!DOCTYPE html>
<html lang="vi">

<head>
    <title>Cập Nhật Tour</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="bg-light">
    <div class="container mt-4 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="text-warning fw-bold">Xem chi tiết & Cập Nhật Tour: <?= htmlspecialchars($tour['ten_tour']) ?></h3>
            <a href="index.php?action=admin-tours" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại danh sách
            </a>
        </div>
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form action="index.php?action=admin-tour-update" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $tour['id'] ?>">
                    <input type="hidden" name="anh_cu" value="<?= $tour['anh_tour'] ?>">

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
                                    <label class="form-label fw-bold">Tên Tour</label>
                                    <input type="text" name="ten_tour" class="form-control" value="<?= htmlspecialchars($tour['ten_tour']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Loại Tour</label>
                                    <select name="loai_tour_id" class="form-select">
                                        <?php foreach ($categories as $cate): ?>
                                            <option value="<?= $cate['id'] ?>" <?= $cate['id'] == $tour['loai_tour_id'] ? 'selected' : '' ?>>
                                                <?= $cate['ten_loai'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Giá Người Lớn</label>
                                    <input type="number" name="gia_nguoi_lon" class="form-control" value="<?= $tour['gia_nguoi_lon'] ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Giá Trẻ Em</label>
                                    <input type="number" name="gia_tre_em" class="form-control" value="<?= $tour['gia_tre_em'] ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Số ngày</label>
                                    <input type="number" name="so_ngay" id="so_ngay" class="form-control" value="<?= $tour['so_ngay'] ?>" required min="1" oninput="renderItinerary()">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Giới thiệu ngắn</label>
                                    <textarea name="gioi_thieu" class="form-control" rows="3"><?= htmlspecialchars($tour['gioi_thieu']) ?></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Lịch trình tóm tắt</label>
                                    <input type="text" name="lich_trinh_tom_tat" class="form-control" value="<?= htmlspecialchars($tour['lich_trinh']) ?>">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Ảnh đại diện hiện tại</label>
                                    <div>
                                        <?php if ($tour['anh_tour']): ?>
                                            <img src="assets/uploads/<?= $tour['anh_tour'] ?>" width="150" class="img-thumbnail mb-2">
                                        <?php endif; ?>
                                    </div>
                                    <label class="form-label small">Chọn ảnh mới nếu muốn thay đổi:</label>
                                    <input type="file" name="anh_tour" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="itinerary">
                            <div class="card border-0">
                                <div class="card-body p-0">
                                    <div id="itinerary-container">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="policy">
                            <div class="row g-3">
                                <div class="col-md-6"><label class="form-label fw-bold">Giá bao gồm</label><textarea name="bao_gom" class="form-control" rows="5"><?= $tour['bao_gom'] ?? '' ?></textarea></div>
                                <div class="col-md-6"><label class="form-label fw-bold">Giá KHÔNG bao gồm</label><textarea name="khong_bao_gom" class="form-control" rows="5"><?= $tour['khong_bao_gom'] ?? '' ?></textarea></div>
                                <div class="col-md-6"><label class="form-label fw-bold">Chính sách Hoàn/Hủy</label><textarea name="chinh_sach_huy" class="form-control" rows="5"><?= $tour['chinh_sach_huy'] ?? '' ?></textarea></div>
                                <div class="col-md-6"><label class="form-label fw-bold">Lưu ý</label><textarea name="luu_y" class="form-control" rows="5"><?= $tour['luu_y'] ?? '' ?></textarea></div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="suppliers">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="50">Chọn</th>
                                            <th>Tên NCC</th>
                                            <th>Dịch vụ</th>
                                            <th>Ghi chú</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($allSuppliers)): ?>
                                            <?php foreach ($allSuppliers as $s):
                                                $isChecked = in_array($s['id'], $selectedSupplierIds) ? 'checked' : '';
                                                $noteVal = $selectedSupplierNotes[$s['id']] ?? '';
                                            ?>
                                                <tr>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="checkbox" name="suppliers[]" value="<?= $s['id'] ?>" <?= $isChecked ?>>
                                                    </td>
                                                    <td class="fw-bold"><?= $s['ten_ncc'] ?></td>
                                                    <td><span class="badge bg-secondary"><?= $s['dich_vu'] ?></span></td>
                                                    <td><input type="text" name="suppliers_note[<?= $s['id'] ?>]" class="form-control form-control-sm" value="<?= htmlspecialchars($noteVal) ?>"></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="gallery">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Thêm ảnh mới vào Album</label>
                                <input type="file" name="gallery[]" class="form-control" multiple accept="image/*">
                            </div>

                            <div class="row mt-3">
                                <p class="fw-bold">Ảnh hiện tại trong Album:</p>
                                <?php if (empty($gallery)): ?>
                                    <p class="text-muted fst-italic">Chưa có ảnh nào trong album.</p>
                                <?php else: ?>
                                    <?php foreach ($gallery as $img): ?>
                                        <div class="col-md-3 col-6 mb-4 text-center position-relative">
                                            <div class="card h-100 shadow-sm">
                                                <img src="assets/uploads/<?= $img['image_url'] ?>" class="card-img-top" style="height: 150px; object-fit: cover;">
                                                <div class="card-body p-2">
                                                    <a href="index.php?action=admin-tour-delete-image&image_id=<?= $img['id'] ?>&tour_id=<?= $tour['id'] ?>"
                                                        class="btn btn-danger btn-sm w-100"
                                                        onclick="return confirm('Bạn chắc chắn muốn xóa ảnh này?')">
                                                        <i class="bi bi-trash"></i> Xóa ảnh
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-end border-top pt-3">
                        <a href="index.php?action=admin-tours" class="btn btn-secondary me-2">Hủy bỏ</a>
                        <button type="submit" class="btn btn-warning px-4 fw-bold">
                            <i class="bi bi-save"></i> Cập nhật Tour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // 1. Chuyển dữ liệu PHP ($itinerary) sang mảng JavaScript
    // Mảng này chứa dữ liệu cũ: [{ngay_thu: 1, tieu_de: '...', noi_dung: '...'}, ...]
    const existingData = <?= json_encode($itinerary) ?>;

    function renderItinerary() {
        const days = parseInt(document.getElementById('so_ngay').value) || 0;
        const container = document.getElementById('itinerary-container');
        
        // Lưu lại dữ liệu đang nhập trên màn hình (để khi admin đổi số ngày, text không bị mất)
        // Đây là logic nâng cao giúp trải nghiệm mượt mà hơn
        const currentInputs = {};
        const titleInputs = document.getElementsByName('itinerary_title[]');
        const contentInputs = document.getElementsByName('itinerary_content[]');
        
        for (let i = 0; i < titleInputs.length; i++) {
            currentInputs[i] = {
                title: titleInputs[i].value,
                content: contentInputs[i].value
            };
        }

        container.innerHTML = ''; // Xóa trắng để vẽ lại

        if (days > 0) {
            for (let i = 0; i < days; i++) {
                // Logic ưu tiên dữ liệu:
                // 1. Dữ liệu đang nhập dở trên màn hình (currentInputs)
                // 2. Dữ liệu cũ từ Database (existingData)
                // 3. Rỗng (nếu là ngày mới thêm)
                
                let valTitle = '';
                let valContent = '';

                // Nếu đang có dữ liệu nhập dở thì ưu tiên lấy
                if (currentInputs[i]) {
                    valTitle = currentInputs[i].title;
                    valContent = currentInputs[i].content;
                } 
                // Nếu không, kiểm tra xem có dữ liệu cũ trong DB không
                // Lưu ý: existingData có thể không sắp xếp theo index, nên cần map kỹ nếu data phức tạp.
                // Nhưng với logic insert vòng lặp của ta thì index i chính là existingData[i]
                else if (existingData[i]) {
                    valTitle = existingData[i].tieu_de;
                    valContent = existingData[i].noi_dung;
                }

                const html = `
                    <div class="card mb-3 border bg-light shadow-sm">
                        <div class="card-header py-2 fw-bold text-primary">
                            Ngày ${i + 1}
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <label class="form-label small fw-bold">Tiêu đề</label>
                                <input type="text" name="itinerary_title[]" class="form-control bg-white" 
                                       value="${valTitle}" placeholder="Tiêu đề ngày ${i+1}..." required>
                            </div>
                            <div class="mb-0">
                                <label class="form-label small fw-bold">Nội dung chi tiết</label>
                                <textarea name="itinerary_content[]" class="form-control bg-white" rows="3" 
                                          placeholder="Nội dung hoạt động...">${valContent}</textarea>
                            </div>
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', html);
            }
        } else {
            container.innerHTML = '<div class="alert alert-warning">Vui lòng nhập số ngày hợp lệ.</div>';
        }
    }

    // Chạy ngay khi trang vừa load để hiển thị dữ liệu cũ
    document.addEventListener("DOMContentLoaded", function() {
        renderItinerary();
    });
</script>
</body>

</html>