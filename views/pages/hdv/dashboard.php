<?php
$dangDi = [];
$sapDi = [];
$daDi = [];
$now = time();

if (!empty($myTours)) {
    foreach ($myTours as $tour) {
        $start = strtotime($tour['ngay_khoi_hanh']);
        $end = strtotime($tour['ngay_ket_thuc']);

        if ($now < $start) {
            $sapDi[] = $tour; 
        } elseif ($now >= $start && $now <= $end) {
            $dangDi[] = $tour; 
        } else {
            $daDi[] = $tour; 
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lịch Làm Việc HDV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .nav-tabs .nav-link.active {
            font-weight: bold;
            border-top: 3px solid #198754;
            color: #198754;
        }
        .nav-tabs .nav-link {
            color: #555;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
            transition: 0.3s;
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-success mb-4 shadow">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#"><i class="bi bi-person-badge-fill"></i> HDV DASHBOARD</a>
            <div class="d-flex align-items-center">
                <span class="navbar-text me-3 text-white">
                    Xin chào, <strong><?= $_SESSION['user']['ho_ten'] ?? 'HDV' ?></strong>
                </span>
                <a href="<?= BASE_URL ?>routes/index.php?action=logout" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Đăng xuất
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        
        <ul class="nav nav-tabs mb-4" id="tourTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="ongoing-tab" data-bs-toggle="tab" data-bs-target="#ongoing" type="button" role="tab">
                    🚀 Đang Đi <span class="badge bg-success rounded-pill ms-1"><?= count($dangDi) ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming" type="button" role="tab">
                    ⏳ Sắp Đi <span class="badge bg-primary rounded-pill ms-1"><?= count($sapDi) ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="past-tab" data-bs-toggle="tab" data-bs-target="#past" type="button" role="tab">
                    🏁 Đã Đi <span class="badge bg-secondary rounded-pill ms-1"><?= count($daDi) ?></span>
                </button>
            </li>
        </ul>

        <div class="tab-content" id="tourTabsContent">
            
            <div class="tab-pane fade show active" id="ongoing" role="tabpanel">
                <?php if (empty($dangDi)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-calendar-x display-4"></i>
                        <p class="mt-2">Hiện tại không có tour nào đang diễn ra.</p>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($dangDi as $tour): ?>
                            <?php renderTourCard($tour, 'success', 'Đang diễn ra', 'ongoing'); ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="tab-pane fade" id="upcoming" role="tabpanel">
                <?php if (empty($sapDi)): ?>
                    <div class="text-center py-5 text-muted">
                        <p>Không có lịch trình sắp tới.</p>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($sapDi as $tour): ?>
                            <?php renderTourCard($tour, 'primary', 'Sắp khởi hành', 'upcoming'); ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="tab-pane fade" id="past" role="tabpanel">
                <?php if (empty($daDi)): ?>
                    <div class="text-center py-5 text-muted">
                        <p>Chưa có lịch sử tour.</p>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($daDi as $tour): ?>
                            <?php renderTourCard($tour, 'secondary', 'Đã hoàn thành', 'past'); ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Cập nhật hàm render để xử lý nút bấm theo trạng thái
function renderTourCard($tour, $color, $statusLabel, $type = 'ongoing') {
    // Mặc định cho Đang đi
    $btnLabel = 'Quản Lý & Điểm Danh';
    $btnIcon = 'bi-pencil-square';
    
    // Đổi chữ nếu là Sắp đi hoặc Đã đi
    if ($type === 'upcoming') {
        $btnLabel = 'Xem Chi Tiết';
        $btnIcon = 'bi-eye';
    } elseif ($type === 'past') {
        $btnLabel = 'Xem Lại Lịch Trình';
        $btnIcon = 'bi-clock-history';
    }
    ?>
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100 shadow-sm border-0 card-hover">
            <div class="card-header bg-<?= $color ?> text-white d-flex justify-content-between align-items-center">
                <span class="badge bg-white text-<?= $color ?>"><?= $statusLabel ?></span>
                <small>#<?= $tour['id'] ?></small>
            </div>
            <div class="card-body">
                <h5 class="card-title text-dark fw-bold mb-3"><?= $tour['ten_tour'] ?></h5>
                
                <ul class="list-unstyled small text-muted">
                    <li class="mb-2"><i class="bi bi-clock"></i> Thời lượng: <strong><?= $tour['so_ngay'] ?> ngày</strong></li>
                    <li class="mb-2"><i class="bi bi-calendar-event"></i> Đi: <span class="text-dark fw-bold"><?= date('d/m/Y H:i', strtotime($tour['ngay_khoi_hanh'])) ?></span></li>
                    <li class="mb-2"><i class="bi bi-calendar-check"></i> Về: <span class="text-dark fw-bold"><?= date('d/m/Y H:i', strtotime($tour['ngay_ket_thuc'])) ?></span></li>
                    <li class="mb-2"><i class="bi bi-geo-alt"></i> Tập trung: <?= $tour['diem_tap_trung'] ?></li>
                    <li><i class="bi bi-people"></i> Khách: <strong><?= $tour['so_cho_da_dat'] ?></strong>/<?= $tour['so_cho_toi_da'] ?></li>
                </ul>

                <div class="d-grid mt-4">
                    <a href="<?= BASE_URL ?>routes/index.php?action=hdv-tour-detail&id=<?= $tour['id'] ?>" 
                       class="btn btn-outline-<?= $color ?> fw-bold">
                        <i class="bi <?= $btnIcon ?>"></i> <?= $btnLabel ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>