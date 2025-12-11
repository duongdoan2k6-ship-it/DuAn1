<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Chi Tiết Chuyến Đi & Điểm Danh</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .timeline-item {
            border-left: 3px solid #ced4da;
            padding-left: 1rem;
            margin-left: 0.5rem;
            position: relative;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -8px;
            top: 0;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            background: #28a745;
            border: 2px solid #fff;
        }

        .timeline-day {
            margin-left: -20px;
            margin-top: -8px;
            font-weight: bold;
            color: #28a745;
        }

        .log-image {
            max-width: 150px;
            height: auto;
            border-radius: 5px;
            margin-top: 10px;
            border: 1px solid #ddd;
        }
    </style>
</head>

<body class="bg-light">

    <nav class="navbar navbar-dark bg-success mb-4">
        <div class="container">
            <a class="navbar-brand" href="<?= BASE_URL ?>routes/index.php?action=hdv-dashboard">
                <i class="bi bi-arrow-left"></i> Quay lại Danh sách Tour
            </a>
            <span class="navbar-text text-white fw-bold fs-5">
                Chuyến: <?= $tourInfo['ten_tour'] ?> (<?= date('d/m/Y', strtotime($tourInfo['ngay_khoi_hanh'])) ?>)
            </span>
        </div>
    </nav>

    <div class="container">

        <?php if (isset($_GET['status'])): ?>
            <?php if ($_GET['status'] == 'success'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill"></i> Đã lưu trạng thái điểm danh thành công!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($_GET['status'] == 'log_success'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-journal-check"></i> Đã thêm nhật ký thành công!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($_GET['status'] == 'log_error'): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> Có lỗi xảy ra khi thêm nhật ký.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($_GET['status'] == 'phien_deleted'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-trash"></i> Đã xóa phiên điểm danh thành công.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($_GET['status'] == 'note_success'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-save"></i> Cập nhật yêu cầu đặc biệt thành công!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        <?php endif; ?>


        <div class="card shadow">
            <div class="card-header p-0 pt-1 border-bottom-0">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info-tab-pane" type="button" role="tab">
                            <i class="bi bi-info-circle-fill"></i> Thông tin chung
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="itinerary-tab" data-bs-toggle="tab" data-bs-target="#itinerary-tab-pane" type="button" role="tab">
                            <i class="bi bi-calendar-range-fill"></i> Lịch trình dự kiến
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="passenger-tab" data-bs-toggle="tab" data-bs-target="#passenger-tab-pane" type="button" role="tab">
                            <i class="bi bi-people-fill"></i> Danh sách Khách
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="diary-tab" data-bs-toggle="tab" data-bs-target="#diary-tab-pane" type="button" role="tab">
                            <i class="bi bi-journal-text"></i> <strong>Nhật ký Tour</strong>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="attendance-tab" data-bs-toggle="tab" data-bs-target="#attendance-tab-pane" type="button" role="tab">
                            <i class="bi bi-clipboard-check-fill"></i> <strong>Điểm danh đoàn</strong>
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="myTabContent">

                    <div class="tab-pane fade show active" id="info-tab-pane" role="tabpanel" tabindex="0">
                        <h5 class="text-primary mb-3">Chi tiết chuyến đi</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item"><strong>Mã chuyến:</strong> <?= $tourInfo['id'] ?></li>
                                    <li class="list-group-item"><strong>Ngày khởi hành:</strong> <?= date('d/m/Y', strtotime($tourInfo['ngay_khoi_hanh'])) ?></li>
                                    <li class="list-group-item"><strong>Ngày kết thúc:</strong> <?= date('d/m/Y', strtotime($tourInfo['ngay_ket_thuc'])) ?></li>
                                    <li class="list-group-item"><strong>Địa điểm tập trung:</strong> <?= $tourInfo['diem_tap_trung'] ?></li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item"><strong>Tổng số chỗ:</strong> <?= $tourInfo['so_cho_toi_da'] ?></li>
                                    <li class="list-group-item"><strong>Số chỗ đã đặt:</strong> <span class="badge bg-success fs-6"><?= $tourInfo['so_cho_da_dat'] ?></span></li>
                                    <li class="list-group-item"><strong>Trạng thái:</strong> <span class="badge bg-warning text-dark"><?= $tourInfo['trang_thai'] ?></span></li>
                                    <li class="list-group-item"><strong>Lưu ý chung:</strong> <?= $tourInfo['luu_y'] ?: 'Không có.' ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="itinerary-tab-pane" role="tabpanel" tabindex="0">
                        <?php if (empty($itineraries)): ?>
                            <div class="alert alert-warning text-center">
                                Tour này chưa có lịch trình chi tiết theo ngày.
                            </div>
                        <?php else: ?>
                            <div class="list-group">
                                <?php foreach ($itineraries as $item): ?>
                                    <div class="timeline-item pb-4">
                                        <h6 class="timeline-day">Ngày <?= $item['ngay_thu'] ?></h6>
                                        <h5 class="mt-2 text-dark fw-bold"><?= $item['tieu_de'] ?></h5>
                                        <p class="text-muted small mb-1"><i class="bi bi-clock"></i> <?= $item['thoi_gian'] ?? 'Cả ngày' ?></p>
                                        <div class="text-break"><?= nl2br(htmlspecialchars($item['noi_dung'])) ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="tab-pane fade" id="passenger-tab-pane" role="tabpanel" tabindex="0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 5%;">STT</th>
                                        <th style="width: 20%;">Họ Tên Khách</th>
                                        <th style="width: 15%;">Thông Tin / SĐT</th>
                                        <th style="width: 30%;">Yêu Cầu Đặc Biệt</th> <th style="width: 15%;">Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($passengers)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-4">Chưa có khách nào đặt tour này.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($passengers as $index => $p): ?>
                                            <tr>
                                                <td class="text-center"><?= $index + 1 ?></td>
                                                <td>
                                                    <strong><?= $p['ho_ten_khach'] ?></strong><br>
                                                    <small class="text-muted"><?= $p['gioi_tinh'] ?> - <?= $p['loai_khach'] ?></small>
                                                </td>
                                                <td>
                                                    <i class="bi bi-telephone"></i> <strong><?= $p['sdt_lien_he'] ?></strong><br>
                                                    <small class="text-muted">Người đặt: <?= $p['ten_nguoi_dat'] ?></small>
                                                </td>

                                                <td class="bg-light">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <span class="text-secondary small me-2" id="note-<?= $p['id_khach'] ?>">
                                                            <?php if (!empty($p['ghi_chu_dac_biet'])): ?>
                                                                <i class="bi bi-exclamation-circle-fill text-warning"></i> <?= htmlspecialchars($p['ghi_chu_dac_biet']) ?>
                                                            <?php else: ?>
                                                                <em class="text-muted">Chưa có ghi chú</em>
                                                            <?php endif; ?>
                                                        </span>
                                                        <button type="button" class="btn btn-sm btn-outline-info border-0"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editNoteModal"
                                                            data-khach-id="<?= $p['id_khach'] ?>"
                                                            data-khach-ten="<?= htmlspecialchars($p['ho_ten_khach']) ?>"
                                                            data-ghi-chu="<?= htmlspecialchars($p['ghi_chu_dac_biet'] ?? '') ?>"
                                                            title="Chỉnh sửa yêu cầu">
                                                            <i class="bi bi-pencil-square fs-6"></i>
                                                        </button>
                                                    </div>
                                                </td>

                                                <td class="text-center">
                                                    <?php if ($p['trang_thai_diem_danh'] == 1): ?>
                                                        <span class="badge bg-success">Đã tham gia</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Chưa xác nhận</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="diary-tab-pane" role="tabpanel" tabindex="0">
                        <div class="row">
                            <div class="col-md-5 mb-4">
                                <div class="card border-primary h-100">
                                    <div class="card-header bg-primary text-white">
                                        <i class="bi bi-pen-fill"></i> Viết Nhật Ký Mới
                                    </div>
                                    <div class="card-body">
                                        <form action="<?= BASE_URL ?>routes/index.php?action=hdv-add-nhat-ky" method="POST" enctype="multipart/form-data">
                                            <input type="hidden" name="lich_khoi_hanh_id" value="<?= $lichId ?>">

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Tiêu đề (Ngày/Sự kiện) <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="tieu_de" placeholder="VD: Ngày 1 - Đón khách tại sân bay" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Diễn biến thực tế <span class="text-danger">*</span></label>
                                                <textarea class="form-control" name="noi_dung" rows="4" placeholder="Mô tả chi tiết hoạt động..." required></textarea>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-danger">Sự cố (nếu có)</label>
                                                <textarea class="form-control" name="su_co" rows="2" placeholder="Khách quên đồ, xe hỏng..."></textarea>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-info">Phản hồi của khách</label>
                                                <textarea class="form-control" name="phan_hoi_khach" rows="2" placeholder="Khách khen/chê gì..."></textarea>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Hình ảnh thực tế</label>
                                                <input type="file" class="form-control" name="hinh_anh" accept="image/*">
                                            </div>

                                            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-send-fill"></i> Lưu Nhật Ký</button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-7">
                                <h5 class="text-secondary mb-3"><i class="bi bi-clock-history"></i> Lịch sử Nhật Ký</h5>
                                <?php if (empty($nhatKyList)): ?>
                                    <div class="alert alert-light text-center border">
                                        Chưa có nhật ký nào được ghi lại. Hãy bắt đầu viết ngay!
                                    </div>
                                <?php else: ?>
                                    <div class="vstack gap-3">
                                        <?php foreach ($nhatKyList as $log): ?>
                                            <div class="card shadow-sm border-start border-4 border-info position-relative">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <h5 class="card-title text-primary m-0"><?= htmlspecialchars($log['tieu_de']) ?></h5>
                                                        <div>
                                                            <small class="text-muted me-2"><?= date('H:i d/m/Y', strtotime($log['thoi_gian_tao'])) ?></small>
                                                            <a href="<?= BASE_URL ?>routes/index.php?action=hdv-edit-nhat-ky&id=<?= $log['id'] ?>"
                                                                class="btn btn-sm btn-outline-warning" title="Sửa">
                                                                <i class="bi bi-pencil-square"></i>
                                                            </a>
                                                            <a href="<?= BASE_URL ?>routes/index.php?action=hdv-delete-nhat-ky&id=<?= $log['id'] ?>&lich_id=<?= $lichId ?>"
                                                                class="btn btn-sm btn-outline-danger"
                                                                onclick="return confirm('Bạn có chắc chắn muốn xóa nhật ký này?');" title="Xóa">
                                                                <i class="bi bi-trash-fill"></i>
                                                            </a>
                                                        </div>
                                                    </div>

                                                    <p class="card-text text-dark"><?= nl2br(htmlspecialchars($log['noi_dung'])) ?></p>

                                                    <?php if (!empty($log['su_co'])): ?>
                                                        <div class="alert alert-danger py-2 px-3 small mb-2">
                                                            <strong><i class="bi bi-exclamation-octagon"></i> Sự cố:</strong> <?= htmlspecialchars($log['su_co']) ?>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if (!empty($log['phan_hoi_khach'])): ?>
                                                        <div class="alert alert-info py-2 px-3 small mb-2">
                                                            <strong><i class="bi bi-chat-quote"></i> Khách phản hồi:</strong> <?= htmlspecialchars($log['phan_hoi_khach']) ?>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if (!empty($log['hinh_anh'])): ?>
                                                        <a href="<?= BASE_URL . 'public/' . $log['hinh_anh'] ?>" target="_blank">
                                                            <img src="<?= BASE_URL . 'public/' . $log['hinh_anh'] ?>" class="log-image" alt="Ảnh nhật ký">
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="attendance-tab-pane" role="tabpanel" tabindex="0">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="text-primary m-0">Danh sách các đợt điểm danh</h5>
                            <form action="<?= BASE_URL ?>routes/index.php?action=hdv-create-phien-dd" method="POST" class="d-flex">
                                <input type="hidden" name="lich_khoi_hanh_id" value="<?= $lichId ?>">
                                <input type="text" name="tieu_de" class="form-control me-2" placeholder="VD: Tập trung ăn trưa..." required>
                                <button type="submit" class="btn btn-success text-nowrap"><i class="bi bi-plus-lg"></i> Tạo phiên mới</button>
                            </form>
                        </div>

                        <?php if (empty($phienDiemDanhList)): ?>
                            <div class="alert alert-warning text-center">Chưa có phiên điểm danh nào. Hãy tạo phiên đầu tiên!</div>
                        <?php else: ?>
                            <div class="list-group">
                                <?php foreach ($phienDiemDanhList as $phien): ?>
                                    <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1 fw-bold"><?= htmlspecialchars($phien['tieu_de']) ?></h6>
                                            <small class="text-muted"><i class="bi bi-clock"></i> <?= date('H:i d/m/Y', strtotime($phien['thoi_gian_tao'])) ?></small>
                                        </div>
                                        <div>
                                            <a href="<?= BASE_URL ?>routes/index.php?action=hdv-view-diem-danh&lich_id=<?= $lichId ?>&phien_id=<?= $phien['id'] ?>"
                                                class="btn btn-sm btn-primary">
                                                <i class="bi bi-eye"></i> Chi tiết & Điểm danh
                                            </a>
                                            <a href="<?= BASE_URL ?>routes/index.php?action=hdv-delete-phien-dd&lich_id=<?= $lichId ?>&phien_id=<?= $phien['id'] ?>"
                                                class="btn btn-sm btn-outline-danger ms-1"
                                                onclick="return confirm('Xóa phiên điểm danh này? Dữ liệu chi tiết sẽ mất!');">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editNoteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="bi bi-person-fill"></i> Yêu Cầu Đặc Biệt của Khách</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?= BASE_URL ?>routes/index.php?action=hdv-update-khach-note" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="lich_id" value="<?= $lichId ?>">
                        <input type="hidden" name="khach_id" id="modal-khach-id">

                        <p>Cập nhật yêu cầu cho khách: <strong id="modal-khach-ten" class="text-primary"></strong></p>

                        <div class="form-group">
                            <label for="modal-ghi-chu" class="fw-bold mb-2">Nội dung yêu cầu:</label>
                            <textarea class="form-control" name="ghi_chu" id="modal-ghi-chu" rows="4"
                                placeholder="Ví dụ: Ăn chay, dị ứng hải sản, cần hỗ trợ y tế,..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-info fw-bold"><i class="bi bi-save"></i> Lưu Yêu Cầu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            var editNoteModal = document.getElementById('editNoteModal');
            if (editNoteModal) {
                editNoteModal.addEventListener('show.bs.modal', function(event) {
                    var button = event.relatedTarget;
                    var khachId = button.getAttribute('data-khach-id');
                    var khachTen = button.getAttribute('data-khach-ten');
                    var ghiChu = button.getAttribute('data-ghi-chu');

                    editNoteModal.querySelector('#modal-khach-id').value = khachId;
                    editNoteModal.querySelector('#modal-khach-ten').textContent = khachTen;
                    editNoteModal.querySelector('#modal-ghi-chu').value = ghiChu;
                });
            }

            var hash = window.location.hash;
            if (hash) {
                if (hash === '#v-pills-passengers-tab') {
                    var triggerEl = document.querySelector('#myTab button[data-bs-target="#passenger-tab-pane"]');
                    if (triggerEl) {
                        bootstrap.Tab.getInstance(triggerEl) || new bootstrap.Tab(triggerEl).show();
                    }
                }
            }
        });
    </script>
</body>

</html>