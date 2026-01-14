<?php
// Helper format tiền tệ (nếu chưa có trong helper chung)
if (!function_exists('currency_format')) {
    function currency_format($number) {
        if ($number === null) return '0 ₫';
        return number_format($number, 0, ',', '.') . ' ₫';
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Báo Cáo Thống Kê</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php?action=admin-dashboard">
                <i class="fas fa-arrow-left me-2"></i> ADMIN PANEL
            </a>
            <div class="d-flex align-items-center">
                <span class="navbar-text text-white fw-bold">
                    <i class="fas fa-chart-pie me-1"></i> BÁO CÁO THỐNG KÊ
                </span>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        
        <div class="card shadow mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-primary"><i class="fas fa-filter"></i> Bộ Lọc Thời Gian</h6>
            </div>
            <div class="card-body">
                <form action="index.php" method="GET" class="row g-3 align-items-end">
                    <input type="hidden" name="action" value="admin-statistics">
                    
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-muted">Từ ngày:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                            <input type="date" name="from_date" class="form-control" value="<?= $fromDate ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-muted">Đến ngày:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                            <input type="date" name="to_date" class="form-control" value="<?= $toDate ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100 fw-bold">
                            <i class="fas fa-search me-1"></i> Xem Báo Cáo
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3 shadow h-100">
                    <div class="card-header fw-bold border-success">
                        <i class="fas fa-file-invoice-dollar"></i> TỔNG DOANH THU
                    </div>
                    <div class="card-body">
                        <h2 class="card-title fw-bold"><?= currency_format($overallStats['doanh_thu']) ?></h2>
                        <p class="card-text small op-8">Dựa trên các booking đã xác nhận.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-white bg-danger mb-3 shadow h-100">
                    <div class="card-header fw-bold border-danger">
                        <i class="fas fa-wallet"></i> TỔNG CHI PHÍ
                    </div>
                    <div class="card-body">
                        <h2 class="card-title fw-bold"><?= currency_format($overallStats['chi_phi']) ?></h2>
                        <p class="card-text small op-8">Chi trả cho NCC (Khách sạn, Xe...).</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-dark bg-info mb-3 shadow h-100 bg-opacity-25" style="background-color: #0dcaf0 !important;">
                    <div class="card-header fw-bold border-info text-white">
                        <i class="fas fa-chart-line"></i> LỢI NHUẬN RÒNG
                    </div>
                    <div class="card-body text-white">
                        <h2 class="card-title fw-bold"><?= currency_format($overallStats['loi_nhuan']) ?></h2>
                        <p class="card-text small">Hiệu quả kinh doanh thực tế.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 fw-bold text-primary"><i class="fas fa-chart-bar"></i> Biểu Đồ Hiệu Quả Kinh Doanh (Top Tours)</h6>
            </div>
            <div class="card-body">
                <div style="height: 350px;">
                    <canvas id="efficiencyChart"></canvas>
                </div>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-primary"><i class="fas fa-table"></i> Chi Tiết Theo Từng Tour</h6>
                <button class="btn btn-sm btn-outline-success" onclick="window.print()">
                    <i class="fas fa-print"></i> In Báo Cáo
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>ID</th>
                                <th class="text-start">Tên Tour</th>
                                <th>Số Chuyến</th>
                                <th>Doanh Thu</th>
                                <th>Chi Phí</th>
                                <th>Lợi Nhuận</th>
                                <th>Tỷ Suất LN</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($tourStats)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="fas fa-box-open fa-2x mb-2 d-block"></i>
                                        Không có dữ liệu tour trong giai đoạn này.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($tourStats as $stat): 
                                    // Tính màu sắc cho lợi nhuận
                                    $profitClass = $stat['loi_nhuan'] >= 0 ? 'text-success' : 'text-danger';
                                    
                                    // Tính tỷ suất lợi nhuận
                                    $hieuSuat = ($stat['doanh_thu'] > 0) ? round(($stat['loi_nhuan'] / $stat['doanh_thu']) * 100, 1) : 0;
                                    $badgeColor = 'bg-secondary';
                                    if ($hieuSuat >= 20) $badgeColor = 'bg-success';
                                    elseif ($hieuSuat > 0) $badgeColor = 'bg-warning text-dark';
                                    else $badgeColor = 'bg-danger';
                                ?>
                                    <tr>
                                        <td class="text-center fw-bold text-muted">#<?= $stat['tour_id'] ?></td>
                                        <td>
                                            <div class="fw-bold text-primary"><?= htmlspecialchars($stat['ten_tour']) ?></div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary rounded-pill"><?= $stat['so_chuyen_di'] ?></span>
                                        </td>
                                        <td class="text-end pe-4 text-success fw-bold">
                                            <?= currency_format($stat['doanh_thu']) ?>
                                        </td>
                                        <td class="text-end pe-4 text-danger">
                                            <?= currency_format($stat['chi_phi']) ?>
                                        </td>
                                        <td class="text-end pe-4 fw-bold <?= $profitClass ?>">
                                            <?= currency_format($stat['loi_nhuan']) ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge <?= $badgeColor ?>">
                                                <?= $hieuSuat ?>%
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <?php if (!empty($tourStats)): ?>
                        <tfoot class="table-light fw-bold">
                            <tr class="text-end">
                                <td colspan="3" class="text-center text-uppercase">Tổng cộng</td>
                                <td class="pe-4 text-success"><?= currency_format($overallStats['doanh_thu']) ?></td>
                                <td class="pe-4 text-danger"><?= currency_format($overallStats['chi_phi']) ?></td>
                                <td class="pe-4 text-primary"><?= currency_format($overallStats['loi_nhuan']) ?></td>
                                <td></td>
                            </tr>
                        </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white text-end">
                <small class="text-muted">Dữ liệu được cập nhật đến <?= date('H:i d/m/Y') ?></small>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('efficiencyChart').getContext('2d');
            
            const chartLabels = <?= $chartData['labels'] ?>;
            const chartRevenue = <?= $chartData['revenue'] ?>;
            const chartProfit = <?= $chartData['profit'] ?>;

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Doanh Thu',
                        data: chartRevenue,
                        backgroundColor: '#198754', // Màu xanh Success của Bootstrap
                        borderColor: '#157347',
                        borderWidth: 1,
                        borderRadius: 3
                    }, {
                        label: 'Lợi Nhuận',
                        data: chartProfit,
                        backgroundColor: '#0dcaf0', // Màu xanh Info của Bootstrap
                        borderColor: '#0aa2c0',
                        borderWidth: 1,
                        borderRadius: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let value = context.raw;
                                    return context.dataset.label + ': ' + new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return new Intl.NumberFormat('vi-VN', { compact: 'short' }).format(value) + 'đ';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>