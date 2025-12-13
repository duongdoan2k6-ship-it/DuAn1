<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

        /* Style cho Card trên Mobile */
        .passenger-card {
            border-left: 5px solid #0d6efd; /* Mặc định màu xanh */
            transition: transform 0.2s;
        }
        
        .passenger-card.has-note {
            border-left-color: #dc3545; /* Màu đỏ nếu có lưu ý đặc biệt */
            background-color: #fff8f8;
        }

        .passenger-card:active {
            transform: scale(0.98);
        }

        .mobile-label {
            font-weight: bold;
            color: #6c757d;
            font-size: 0.9em;
        }
    </style>
</head>

<body class="bg-light">

    <nav class="navbar navbar-dark bg-success mb-4 sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand text-truncate" style="max-width: 70%;" href="<?= BASE_URL ?>routes/index.php?action=hdv-dashboard">
                <i class="bi bi-arrow-left"></i> 
                <span class="fs-6">Back</span>
            </a>
            <span class="navbar-text text-white fw-bold fs-6 text-truncate" style="max-width: 30%;">
                <?= $tourInfo['ten_tour'] ?>
            </span>
        </div>
    </nav>

    <div class="container pb-5">

        <?php if (isset($_GET['status'])): ?>
            <?php 
                $alertClass = 'alert-success';
                $msg = 'Thao tác thành công!';
                if($_GET['status'] == 'log_error' || $_GET['status'] == 'note_error') {
                    $alertClass = 'alert-danger';
                    $msg = 'Có lỗi xảy ra!';
                }
                // Mapping tin nhắn cụ thể (Rút gọn cho code ngắn)
                $msgs = [
                    'success' => 'Đã lưu điểm danh!',
                    'log_success' => 'Đã thêm nhật ký!',
                    'phien_deleted' => 'Đã xóa phiên điểm danh.',
                    'note_success' => 'Đã cập nhật yêu cầu đặc biệt!',
                ];
                if(isset($msgs[$_GET['status']])) $msg = $msgs[$_GET['status']];
            ?>
            <div class="alert <?= $alertClass ?> alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill"></i> <?= $msg ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-header p-0 pt-1 border-bottom-0 bg-white">
                <ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info-tab-pane" type="button"><i class="bi bi-info-circle"></i><span class="d-none d-md-inline"> Thông tin</span></button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="itinerary-tab" data-bs-toggle="tab" data-bs-target="#itinerary-tab-pane" type="button"><i class="bi bi-map"></i><span class="d-none d-md-inline"> Lịch trình</span></button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="passenger-tab" data-bs-toggle="tab" data-bs-target="#passenger-tab-pane" type="button"><i class="bi bi-people"></i><span class="d-none d-md-inline"> Khách</span></button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="diary-tab" data-bs-toggle="tab" data-bs-target="#diary-tab-pane" type="button"><i class="bi bi-journal-text"></i><span class="d-none d-md-inline"> Nhật ký</span></button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="attendance-tab" data-bs-toggle="tab" data-bs-target="#attendance-tab-pane" type="button"><i class="bi bi-check2-square"></i><span class="d-none d-md-inline"> Điểm danh</span></button>
                    </li>
                </ul>
            </div>
            <div class="card-body p-3">
                <div class="tab-content" id="myTabContent">

                    <div class="tab-pane fade show active" id="info-tab-pane" role="tabpanel" tabindex="0">
                        <h6 class="text-uppercase text-muted small fw-bold mb-3">Thông tin chuyến đi</h6>
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-1"><span class="mobile-label">Ngày đi:</span> <?= date('d/m/Y', strtotime($tourInfo['ngay_khoi_hanh'])) ?></p>
                                    <p class="mb-1"><span class="mobile-label">Ngày về:</span> <?= date('d/m/Y', strtotime($tourInfo['ngay_ket_thuc'])) ?></p>
                                    <p class="mb-0"><span class="mobile-label">Tập trung:</span> <?= $tourInfo['diem_tap_trung'] ?></p>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-1"><span class="mobile-label">Sĩ số:</span> <span class="badge bg-primary"><?= $tourInfo['so_cho_da_dat'] ?>/<?= $tourInfo['so_cho_toi_da'] ?></span></p>
                                    <p class="mb-0"><span class="mobile-label">Lưu ý:</span> <span class="text-danger"><?= $tourInfo['luu_y'] ?: 'Không có' ?></span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="itinerary-tab-pane" role="tabpanel" tabindex="0">
                        <?php if (empty($itineraries)): ?>
                            <div class="text-center text-muted py-5">Chưa có lịch trình chi tiết.</div>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($itineraries as $item): ?>
                                    <div class="timeline-item pb-4">
                                        <h6 class="timeline-day">Ngày <?= $item['ngay_thu'] ?></h6>
                                        <h5 class="mt-1 text-dark fw-bold fs-6"><?= $item['tieu_de'] ?></h5>
                                        <div class="text-muted small"><?= nl2br(htmlspecialchars($item['noi_dung'])) ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="tab-pane fade" id="passenger-tab-pane" role="tabpanel" tabindex="0">
                        
                        <div class="d-none d-md-block table-responsive">
                            <table class="table table-hover align-middle table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 5%;">STT</th>
                                        <th style="width: 25%;">Họ Tên</th>
                                        <th style="width: 20%;">Liên hệ</th>
                                        <th style="width: 35%;">Yêu Cầu Đặc Biệt</th>
                                        <th style="width: 15%;">Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($passengers)): ?>
                                        <tr><td colspan="5" class="text-center">Chưa có khách.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($passengers as $index => $p): ?>
                                            <tr>
                                                <td class="text-center"><?= $index + 1 ?></td>
                                                <td>
                                                    <div class="fw-bold">
                                                        <?= $p['ho_ten_khach'] ?>
                                                        <?php if(!empty($p['ghi_chu_dac_biet'])): ?>
                                                            <i class="bi bi-exclamation-triangle-fill text-danger" title="Có lưu ý đặc biệt"></i>
                                                        <?php endif; ?>
                                                    </div>
                                                    <small class="text-muted"><?= $p['gioi_tinh'] ?> - <?= $p['loai_khach'] ?></small>
                                                </td>
                                                <td><a href="tel:<?= $p['sdt_lien_he'] ?>" class="text-decoration-none"><?= $p['sdt_lien_he'] ?></a></td>
                                                <td class="<?= !empty($p['ghi_chu_dac_biet']) ? 'table-warning' : '' ?>">
                                                    <div class="d-flex justify-content-between">
                                                        <span><?= $p['ghi_chu_dac_biet'] ?: '<em class="text-muted small">Không</em>' ?></span>
                                                        <button class="btn btn-sm btn-link p-0" 
                                                            data-bs-toggle="modal" data-bs-target="#editNoteModal"
                                                            data-khach-id="<?= $p['id_khach'] ?>"
                                                            data-khach-ten="<?= htmlspecialchars($p['ho_ten_khach']) ?>"
                                                            data-ghi-chu="<?= htmlspecialchars($p['ghi_chu_dac_biet'] ?? '') ?>">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($p['trang_thai_diem_danh'] == 1): ?>
                                                        <span class="badge bg-success">Đã check-in</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Chưa check-in</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-md-none">
                            <?php if (empty($passengers)): ?>
                                <div class="text-center text-muted">Chưa có khách nào.</div>
                            <?php else: ?>
                                <?php foreach ($passengers as $index => $p): ?>
                                    <div class="card passenger-card mb-3 shadow-sm <?= !empty($p['ghi_chu_dac_biet']) ? 'has-note' : '' ?>">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <h6 class="card-title fw-bold mb-0 text-primary">
                                                        <?= $index + 1 ?>. <?= $p['ho_ten_khach'] ?>
                                                        <?php if(!empty($p['ghi_chu_dac_biet'])): ?>
                                                            <i class="bi bi-exclamation-triangle-fill text-danger fs-6 animate-flicker"></i>
                                                        <?php endif; ?>
                                                    </h6>
                                                    <small class="text-muted"><?= $p['gioi_tinh'] ?> | <?= $p['loai_khach'] ?></small>
                                                </div>
                                                <?php if ($p['trang_thai_diem_danh'] == 1): ?>
                                                    <span class="badge bg-success"><i class="bi bi-check-lg"></i></span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="row g-2 mb-2">
                                                <div class="col-12">
                                                    <i class="bi bi-telephone text-muted"></i> 
                                                    <a href="tel:<?= $p['sdt_lien_he'] ?>" class="text-decoration-none fw-bold text-dark"><?= $p['sdt_lien_he'] ?></a>
                                                </div>
                                            </div>

                                            <div class="bg-light p-2 rounded border <?= !empty($p['ghi_chu_dac_biet']) ? 'border-warning bg-warning-subtle' : '' ?>">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="small">
                                                        <strong class="text-secondary">Yêu cầu:</strong><br>
                                                        <span class="<?= !empty($p['ghi_chu_dac_biet']) ? 'text-danger fw-bold' : 'text-muted' ?>">
                                                            <?= $p['ghi_chu_dac_biet'] ?: 'Không có' ?>
                                                        </span>
                                                    </div>
                                                    <button class="btn btn-outline-primary btn-sm rounded-circle" style="width: 32px; height: 32px;"
                                                        data-bs-toggle="modal" data-bs-target="#editNoteModal"
                                                        data-khach-id="<?= $p['id_khach'] ?>"
                                                        data-khach-ten="<?= htmlspecialchars($p['ho_ten_khach']) ?>"
                                                        data-ghi-chu="<?= htmlspecialchars($p['ghi_chu_dac_biet'] ?? '') ?>">
                                                        <i class="bi bi-pencil-fill"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="diary-tab-pane" role="tabpanel" tabindex="0">
                        <button class="btn btn-primary w-100 mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#formNhatKy">
                            <i class="bi bi-plus-circle"></i> Viết Nhật Ký Mới
                        </button>
                        
                        <div class="collapse mb-4" id="formNhatKy">
                            <div class="card card-body bg-light">
                                <form action="<?= BASE_URL ?>routes/index.php?action=hdv-add-nhat-ky" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="lich_khoi_hanh_id" value="<?= $lichId ?>">
                                    <div class="mb-2">
                                        <input type="text" class="form-control fw-bold" name="tieu_de" placeholder="Tiêu đề (VD: Ngày 1)" required>
                                    </div>
                                    <div class="mb-2">
                                        <textarea class="form-control" name="noi_dung" rows="3" placeholder="Nội dung..." required></textarea>
                                    </div>
                                    <div class="mb-2">
                                        <input type="text" class="form-control border-danger" name="su_co" placeholder="Sự cố (nếu có)">
                                    </div>
                                    <div class="mb-2">
                                        <input type="text" class="form-control border-info" name="phan_hoi_khach" placeholder="Phản hồi khách">
                                    </div>
                                    <div class="mb-2">
                                        <input type="file" class="form-control" name="hinh_anh" accept="image/*">
                                    </div>
                                    <button type="submit" class="btn btn-success w-100">Lưu</button>
                                </form>
                            </div>
                        </div>

                        <?php if (!empty($nhatKyList)): ?>
                            <?php foreach ($nhatKyList as $log): ?>
                                <div class="card mb-3 shadow-sm border-start border-4 border-info">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="fw-bold text-primary mb-1"><?= htmlspecialchars($log['tieu_de']) ?></h6>
                                            <div class="dropdown">
                                                <button class="btn btn-link btn-sm text-muted p-0" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item" href="<?= BASE_URL ?>routes/index.php?action=hdv-edit-nhat-ky&id=<?= $log['id'] ?>">Sửa</a></li>
                                                    <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>routes/index.php?action=hdv-delete-nhat-ky&id=<?= $log['id'] ?>&lich_id=<?= $lichId ?>" onclick="return confirm('Xóa?')">Xóa</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <small class="text-muted d-block mb-2"><?= date('H:i d/m/Y', strtotime($log['thoi_gian_tao'])) ?></small>
                                        <p class="card-text mb-2 text-secondary"><?= nl2br(htmlspecialchars($log['noi_dung'])) ?></p>
                                        
                                        <?php if (!empty($log['su_co'])): ?>
                                            <div class="alert alert-danger py-1 px-2 small mb-1"><i class="bi bi-exclamation-circle"></i> <?= htmlspecialchars($log['su_co']) ?></div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($log['hinh_anh'])): ?>
                                            <img src="<?= BASE_URL . 'public/' . $log['hinh_anh'] ?>" class="img-fluid rounded mt-2" style="max-height: 150px; width: auto;">
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center text-muted">Chưa có nhật ký.</div>
                        <?php endif; ?>
                    </div>

                    <div class="tab-pane fade" id="attendance-tab-pane" role="tabpanel" tabindex="0">
                        <form action="<?= BASE_URL ?>routes/index.php?action=hdv-create-phien-dd" method="POST" class="input-group mb-3">
                            <input type="hidden" name="lich_khoi_hanh_id" value="<?= $lichId ?>">
                            <input type="text" name="tieu_de" class="form-control" placeholder="Tên phiên (VD: Ăn sáng)" required>
                            <button type="submit" class="btn btn-success"><i class="bi bi-plus-lg"></i></button>
                        </form>

                        <div class="list-group">
                            <?php foreach ($phienDiemDanhList as $phien): ?>
                                <a href="<?= BASE_URL ?>routes/index.php?action=hdv-view-diem-danh&lich_id=<?= $lichId ?>&phien_id=<?= $phien['id'] ?>" 
                                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3">
                                    <div>
                                        <div class="fw-bold"><?= htmlspecialchars($phien['tieu_de']) ?></div>
                                        <small class="text-muted"><?= date('H:i d/m', strtotime($phien['thoi_gian_tao'])) ?></small>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-chevron-right text-muted me-3"></i>
                                        <a href="<?= BASE_URL ?>routes/index.php?action=hdv-delete-phien-dd&lich_id=<?= $lichId ?>&phien_id=<?= $phien['id'] ?>" 
                                           class="text-danger z-2 position-relative" onclick="return confirm('Xóa?')"><i class="bi bi-trash"></i></a>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editNoteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning-subtle">
                    <h5 class="modal-title fs-6 fw-bold text-dark"><i class="bi bi-pencil-square"></i> Cập nhật yêu cầu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?= BASE_URL ?>routes/index.php?action=hdv-update-khach-note" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="lich_id" value="<?= $lichId ?>">
                        <input type="hidden" name="khach_id" id="modal-khach-id">
                        <p class="mb-2">Khách: <strong id="modal-khach-ten" class="text-primary"></strong></p>
                        <div class="form-group">
                            <textarea class="form-control" name="ghi_chu" id="modal-ghi-chu" rows="3" placeholder="Nhập yêu cầu đặc biệt..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer p-2">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary btn-sm">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Xử lý Modal hiển thị đúng dữ liệu
            var editNoteModal = document.getElementById('editNoteModal');
            editNoteModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var khachId = button.getAttribute('data-khach-id');
                var khachTen = button.getAttribute('data-khach-ten');
                var ghiChu = button.getAttribute('data-ghi-chu');

                editNoteModal.querySelector('#modal-khach-id').value = khachId;
                editNoteModal.querySelector('#modal-khach-ten').textContent = khachTen;
                editNoteModal.querySelector('#modal-ghi-chu').value = ghiChu;
            });

            // Tự động mở tab nếu URL có hash (ví dụ sau khi lưu xong reload lại trang)
            var hash = window.location.hash;
            if (hash) {
                // Mapping hash với tab button
                var triggerEl = null;
                if (hash === '#v-pills-passengers-tab') triggerEl = document.querySelector('#passenger-tab');
                // Thêm các mapping khác nếu cần
                
                if (triggerEl) {
                    var tab = new bootstrap.Tab(triggerEl);
                    tab.show();
                }
            }
        });
    </script>
</body>
</html>