<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Chuyến Đi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .timeline-item { border-left: 3px solid #ced4da; padding-left: 1rem; margin-left: 0.5rem; position: relative; }
        .timeline-item::before { content: ''; position: absolute; left: -8px; top: 0; width: 15px; height: 15px; border-radius: 50%; background: #28a745; border: 2px solid #fff; }
        .timeline-day { margin-left: -20px; margin-top: -8px; font-weight: bold; color: #28a745; }
        .section-header { background-color: #f8f9fa; border-bottom: 2px solid #e9ecef; font-weight: bold; color: #198754; text-transform: uppercase; font-size: 0.9rem; }
        .passenger-card { border-left: 5px solid #0d6efd; transition: transform 0.2s; }
        .passenger-card.has-note { border-left-color: #dc3545; background-color: #fff8f8; }
        .mobile-label { font-weight: bold; color: #6c757d; font-size: 0.9em; }
        #btn-back-to-top { position: fixed; bottom: 20px; right: 20px; display: none; z-index: 1000; }
    </style>
</head>

<body class="bg-light">

    <nav class="navbar navbar-dark bg-success mb-4 sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand text-truncate" style="max-width: 70%;" href="<?= BASE_URL ?>routes/index.php?action=hdv-dashboard">
                <i class="bi bi-arrow-left"></i> <span class="fs-6">Quay lại</span>
            </a>
            <span class="navbar-text text-white fw-bold fs-6 text-truncate" style="max-width: 30%;">
                <?= $tourInfo['ten_tour'] ?>
            </span>
        </div>
    </nav>

    <div class="container pb-5">
        
        <?php if (!$isEditable): ?>
            <div class="alert alert-warning border-start border-4 border-warning shadow-sm" role="alert">
                <i class="bi bi-lock-fill me-2"></i> 
                <strong>Không thể chỉnh sửa vì Tour đã hoàn thành hoặc chưa diễn ra</strong>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['status'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill"></i> Thao tác thành công!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm mb-4">
            <div class="card-header section-header"><i class="bi bi-info-circle-fill me-2"></i> 1. Thông tin chuyến đi</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <div class="bg-light p-3 rounded h-100 border">
                            <p class="mb-1"><span class="mobile-label">Ngày đi:</span> <?= date('d/m/Y', strtotime($tourInfo['ngay_khoi_hanh'])) ?></p>
                            <p class="mb-1"><span class="mobile-label">Ngày về:</span> <?= date('d/m/Y', strtotime($tourInfo['ngay_ket_thuc'])) ?></p>
                            <p class="mb-0"><span class="mobile-label">Tập trung:</span> <?= $tourInfo['diem_tap_trung'] ?></p>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="bg-light p-3 rounded h-100 border">
                            <p class="mb-1"><span class="mobile-label">Sĩ số:</span> <span class="badge bg-primary fs-6"><?= $tourInfo['so_cho_da_dat'] ?>/<?= $tourInfo['so_cho_toi_da'] ?></span></p>
                            <p class="mb-0"><span class="mobile-label">Lưu ý:</span> <span class="text-danger fw-bold"><?= $tourInfo['luu_y'] ?: 'Không có' ?></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header section-header"><i class="bi bi-map-fill me-2"></i> 2. Lịch trình chi tiết</div>
            <div class="card-body">
                <?php if (empty($itineraries)): ?>
                    <div class="text-center text-muted py-3">Chưa có lịch trình chi tiết.</div>
                <?php else: ?>
                    <div class="ms-2 mt-2">
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
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header section-header"><span><i class="bi bi-check2-square me-2"></i> 3. Quản lý Điểm danh</span></div>
            <div class="card-body">
                
                <?php if ($isEditable): ?>
                    <form action="<?= BASE_URL ?>routes/index.php?action=hdv-create-phien-dd" method="POST" class="input-group mb-3">
                        <input type="hidden" name="lich_khoi_hanh_id" value="<?= $lichId ?>">
                        <input type="text" name="tieu_de" class="form-control" placeholder="Tên phiên mới (VD: Ăn trưa)..." required>
                        <button type="submit" class="btn btn-success"><i class="bi bi-plus-lg"></i> Tạo</button>
                    </form>
                <?php endif; ?>

                <div class="list-group">
                    <?php if (empty($phienDiemDanhList)): ?>
                        <div class="text-center text-muted py-2 fst-italic">Chưa có phiên điểm danh nào.</div>
                    <?php else: ?>
                        <?php foreach ($phienDiemDanhList as $phien): ?>
                            <a href="<?= BASE_URL ?>routes/index.php?action=hdv-view-diem-danh&lich_id=<?= $lichId ?>&phien_id=<?= $phien['id'] ?>" 
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3">
                                <div>
                                    <div class="fw-bold text-dark d-flex align-items-center">
                                        <i class="bi bi-clipboard-check me-2"></i> <?= htmlspecialchars($phien['tieu_de']) ?>
                                        <?php 
                                            $coMat = $phien['co_mat'] ?? 0;
                                            $tongSo = $phien['tong_so'] ?? 0;
                                            $badgeColor = ($coMat == $tongSo && $tongSo > 0) ? 'bg-success' : 'bg-warning text-dark';
                                        ?>
                                        <span class="badge <?= $badgeColor ?> ms-2" style="font-size: 0.75rem;"><?= $coMat ?>/<?= $tongSo ?></span>
                                    </div>
                                    <small class="text-muted">Tạo lúc: <?= date('H:i d/m', strtotime($phien['thoi_gian_tao'])) ?></small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="btn btn-sm btn-outline-primary me-3 border-0">Chi tiết <i class="bi bi-chevron-right"></i></span>
                                    
                                    <?php if ($isEditable): ?>
                                        <object><a href="<?= BASE_URL ?>routes/index.php?action=hdv-delete-phien-dd&lich_id=<?= $lichId ?>&phien_id=<?= $phien['id'] ?>" 
                                           class="text-danger p-2" onclick="return confirm('Xóa phiên này?')" title="Xóa"><i class="bi bi-trash"></i></a></object>
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header section-header"><i class="bi bi-people-fill me-2"></i> 4. Danh sách hành khách</div>
            <div class="card-body p-0 p-md-3">
                <div class="d-none d-md-block table-responsive">
                    <table class="table table-hover align-middle table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th style="width: 25%;">Họ Tên</th>
                                <th style="width: 20%;">Liên hệ</th>
                                <th style="width: 35%;">Ghi chú / Yêu cầu</th>
                                <th style="width: 15%;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($passengers as $index => $p): ?>
                                <tr>
                                    <td class="text-center"><?= $index + 1 ?></td>
                                    <td>
                                        <div class="fw-bold"><?= $p['ho_ten_khach'] ?></div>
                                        <small class="text-muted"><?= $p['gioi_tinh'] ?> - <?= $p['loai_khach'] ?></small>
                                    </td>
                                    <td><a href="tel:<?= $p['sdt_lien_he'] ?>" class="text-decoration-none"><?= $p['sdt_lien_he'] ?></a></td>
                                    <td class="<?= !empty($p['ghi_chu_dac_biet']) ? 'table-warning' : '' ?>">
                                        <?= $p['ghi_chu_dac_biet'] ?: '<em class="text-muted small">Không</em>' ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($isEditable): ?>
                                            <button class="btn btn-sm btn-outline-primary" 
                                                data-bs-toggle="modal" data-bs-target="#editNoteModal"
                                                data-khach-id="<?= $p['id_khach'] ?>"
                                                data-khach-ten="<?= htmlspecialchars($p['ho_ten_khach']) ?>"
                                                data-ghi-chu="<?= htmlspecialchars($p['ghi_chu_dac_biet'] ?? '') ?>">
                                                <i class="bi bi-pencil-square"></i> Note
                                            </button>
                                        <?php else: ?>
                                            <span class="text-muted"><i class="bi bi-lock"></i></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                </div>
        </div>

        <div class="card shadow-sm mb-5">
            <div class="card-header section-header"><i class="bi bi-journal-text me-2"></i> 5. Nhật ký hành trình</div>
            <div class="card-body">
                
                <?php if ($isEditable): ?>
                    <button class="btn btn-outline-primary w-100 mb-3 dashed-border" type="button" data-bs-toggle="collapse" data-bs-target="#formNhatKy">
                        <i class="bi bi-plus-circle"></i> Viết Nhật Ký Mới
                    </button>
                    
                    <div class="collapse mb-4" id="formNhatKy">
                        <div class="card card-body bg-light border-0">
                            <form action="<?= BASE_URL ?>routes/index.php?action=hdv-add-nhat-ky" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="lich_khoi_hanh_id" value="<?= $lichId ?>">
                                <div class="mb-2"><input type="text" class="form-control fw-bold" name="tieu_de" placeholder="Tiêu đề (VD: Ngày 1)..." required></div>
                                <div class="mb-2"><textarea class="form-control" name="noi_dung" rows="3" placeholder="Nội dung..." required></textarea></div>
                                <div class="row g-2 mb-2">
                                    <div class="col-6"><input type="text" class="form-control border-danger" name="su_co" placeholder="Sự cố (nếu có)"></div>
                                    <div class="col-6"><input type="text" class="form-control border-info" name="phan_hoi_khach" placeholder="Phản hồi khách"></div>
                                </div>
                                <div class="mb-2"><input type="file" class="form-control" name="hinh_anh" accept="image/*"></div>
                                <button type="submit" class="btn btn-success w-100">Lưu Nhật Ký</button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>

                <?php foreach ($nhatKyList as $log): ?>
                    <div class="card mb-3 shadow-sm border-start border-4 border-info">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between">
                                <h6 class="fw-bold text-primary mb-1"><?= htmlspecialchars($log['tieu_de']) ?></h6>
                                
                                <?php if ($isEditable): ?>
                                    <div class="dropdown">
                                        <button class="btn btn-link btn-sm text-muted p-0" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="<?= BASE_URL ?>routes/index.php?action=hdv-edit-nhat-ky&id=<?= $log['id'] ?>">Sửa</a></li>
                                            <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>routes/index.php?action=hdv-delete-nhat-ky&id=<?= $log['id'] ?>&lich_id=<?= $lichId ?>" onclick="return confirm('Xóa?')">Xóa</a></li>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <small class="text-muted d-block mb-2"><i class="bi bi-clock"></i> <?= date('H:i d/m/Y', strtotime($log['thoi_gian_tao'])) ?></small>
                            <p class="card-text mb-2 text-secondary"><?= nl2br(htmlspecialchars($log['noi_dung'])) ?></p>
                            <?php if (!empty($log['su_co'])): ?>
                                <div class="alert alert-danger py-1 px-2 small mb-1"><i class="bi bi-exclamation-circle"></i> <strong>Sự cố:</strong> <?= htmlspecialchars($log['su_co']) ?></div>
                            <?php endif; ?>
                            <?php if (!empty($log['hinh_anh'])): ?>
                                <img src="<?= BASE_URL . 'public/' . $log['hinh_anh'] ?>" class="img-fluid rounded mt-2 border" style="max-height: 200px; width: auto;">
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>

    <?php if ($isEditable): ?>
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
    <?php endif; ?>

    <button type="button" class="btn btn-dark btn-lg rounded-circle shadow" id="btn-back-to-top">
        <i class="bi bi-arrow-up"></i>
    </button>

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

            let mybutton = document.getElementById("btn-back-to-top");
            window.onscroll = function () {
                if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                    mybutton.style.display = "block";
                } else {
                    mybutton.style.display = "none";
                }
            };
            mybutton.addEventListener("click", function () {
                document.body.scrollTop = 0;
                document.documentElement.scrollTop = 0;
            });
        });
    </script>
</body>
</html>