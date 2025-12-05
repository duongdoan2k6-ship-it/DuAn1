<!-- Dashboard Báo cáo tài chính -->
<div class="row">
    <div class="col-12">

        <!-- Card tiêu đề -->
        <div class="card my-4">
            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                <h6 class="text-white text-capitalize ps-3 mb-0">
                    <?= htmlspecialchars($pageTitle ?? 'Quản lý Báo cáo tài chính', ENT_QUOTES, 'UTF-8') ?>
                </h6>

                <a href="index.php?action=add-baocao"
                    class="btn bg-gradient-success me-3 mb-0">
                    Thêm báo cáo
                </a>
            </div>

            <div class="card-body px-4 pb-4">

                <!-- FORM LỌC -->
                <form class="row g-3 mb-4" method="GET" action="index.php">
                    <input type="hidden" name="action" value="list-baocao">

                    <!-- Từ ngày -->
                    <div class="col-md-3">
                        <label class="form-label">Từ ngày</label>
                        <input type="date"
                            name="from_date"
                            class="form-control"
                            value="<?= htmlspecialchars($filters['from_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>

                    <!-- Đến ngày -->
                    <div class="col-md-3">
                        <label class="form-label">Đến ngày</label>
                        <input type="date"
                            name="to_date"
                            class="form-control"
                            value="<?= htmlspecialchars($filters['to_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>

                    <!-- Mã lịch khởi hành -->
                    <div class="col-md-3">
                        <label class="form-label">Mã Lịch khởi hành</label>
                        <input type="text"
                            name="MaLichKhoiHanh"
                            class="form-control"
                            placeholder="VD: 1, 2, 3..."
                            value="<?= htmlspecialchars($filters['MaLichKhoiHanh'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>

                    <!-- Buttons -->
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn bg-gradient-primary me-2">Lọc</button>

                        <a href="index.php?action=list-baocao"
                            class="btn btn-secondary me-2">
                            Xóa lọc
                        </a>

                        <!-- Xuất Excel -->
                        <a href="index.php?action=export-baocao
                            &from_date=<?= urlencode($filters['from_date'] ?? '') ?>
                            &to_date=<?= urlencode($filters['to_date'] ?? '') ?>
&MaLichKhoiHanh=<?= urlencode($filters['MaLichKhoiHanh'] ?? '') ?>"
                            class="btn btn-outline-success">
                            Xuất Excel
                        </a>
                    </div>
                </form>

                <!-- THỐNG KÊ -->
                <div class="row mb-4">
                    <!-- Tổng doanh thu -->
                    <div class="col-md-3">
                        <div class="card shadow-sm p-3 border-left-primary">
                            <span class="text-sm text-muted">Tổng doanh thu</span>
                            <h5 class="text-success mt-1">
                                <?= number_format($summary['total_revenue'] ?? 0, 0, ',', '.') ?> đ
                            </h5>
                        </div>
                    </div>

                    <!-- Tổng chi phí -->
                    <div class="col-md-3">
                        <div class="card shadow-sm p-3 border-left-danger">
                            <span class="text-sm text-muted">Tổng chi phí</span>
                            <h5 class="text-danger mt-1">
                                <?= number_format($summary['total_cost'] ?? 0, 0, ',', '.') ?> đ
                            </h5>
                        </div>
                    </div>

                    <!-- Tổng lợi nhuận -->
                    <div class="col-md-3">
                        <div class="card shadow-sm p-3 border-left-success">
                            <span class="text-sm text-muted">Tổng lợi nhuận</span>
                            <h5 class="text-success mt-1">
                                <?= number_format($summary['total_profit'] ?? 0, 0, ',', '.') ?> đ
                            </h5>
                        </div>
                    </div>

                    <!-- Số báo cáo lãi/lỗ -->
                    <div class="col-md-3">
                        <div class="card shadow-sm p-3 border-left-info">
                            <span class="text-sm text-muted">Số báo cáo lãi / lỗ</span>
                            <h5 class="mt-1">
                                <?= ($summary['profit_count'] ?? 0) ?> lãi /
                                <?= ($summary['loss_count'] ?? 0) ?> lỗ
                            </h5>
                        </div>
                    </div>
                </div>

                <!-- BIỂU ĐỒ THEO LỊCH KHỞI HÀNH -->
                <div class="row mb-5">
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h6 class="text-muted mb-3">Biểu đồ doanh thu - chi phí - lợi nhuận theo lịch khởi hành</h6>
                                <canvas id="baocaoChart" height="150"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- BIỂU ĐỒ THEO NGÀY -->
                <div class="row mb-5">
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h6 class="text-muted mb-3">Biểu đồ theo ngày tạo báo cáo</h6>
                                <canvas id="baocaoDateChart" height="150"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DANH SÁCH BÁO CÁO -->
                <div class="table-responsive">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th>Báo cáo ID</th>
                                <th>Mã Lịch KH</th>
                                <th>Doanh thu</th>
                                <th>Chi phí</th>
                                <th>Lợi nhuận</th>
                                <th>Trạng thái</th>
                                <th>Ngày tạo</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (!empty($Baocao)) : ?>
                                <?php foreach ($Baocao as $row) : ?>
                                    <?php
                                    $profit = (float)$row['LoiNhuan'];
                                    $status = $profit > 0
                                        ? "<span class='text-success'>Lãi</span>"
                                        : ($profit < 0
                                            ? "<span class='text-danger'>Lỗ</span>"
                                            : "<span class='text-muted'>Hòa vốn</span>");
                                    ?>
                                    <tr>
                                        <td><?= $row['BaoCaoID'] ?></td>
                                        <td><?= $row['MaLichKhoiHanh'] ?></td>
                                        <td><?= number_format($row['DoanhThu'], 0, ',', '.') ?></td>
                                        <td><?= number_format($row['ChiPhi'], 0, ',', '.') ?></td>
                                        <td><?= number_format($row['LoiNhuan'], 0, ',', '.') ?></td>
                                        <td><?= $status ?></td>
                                        <td><?= htmlspecialchars($row['NgayTaoBaoCao']) ?></td>

                                        <td>
                                            <a href="index.php?action=detail-baocao&id=<?= $row['BaoCaoID'] ?>"
                                                class="btn btn-info btn-sm me-1">Chi tiết</a>

                                            <a href="index.php?action=edit-baocao&id=<?= $row['BaoCaoID'] ?>"
                                                class="btn btn-secondary btn-sm me-1">Sửa</a>
                                            <a href="index.php?action=delete-baocao&id=<?= $row['BaoCaoID'] ?>"
                                                onclick="return confirm('Bạn chắc chắn muốn xóa?');"
                                                class="btn btn-danger btn-sm">Xóa</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        Chưa có báo cáo nào.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>
</div>


<!-- CHART.JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?php

$labels   = [];
$revenues = [];
$costs    = [];
$profits  = [];

foreach ($chartData as $row) {
    $labels[]   = "Lịch " . $row['MaLichKhoiHanh'];
    $revenues[] = (float)$row['total_revenue'];
    $costs[]    = (float)$row['total_cost'];
    $profits[]  = (float)$row['total_profit'];
}

// Dataset theo ngày
$dateLabels   = [];
$dateRevenues = [];
$dateCosts    = [];
$dateProfits  = [];

foreach ($chartByDateData as $row) {
    $dateLabels[]   = date("d/m/Y", strtotime($row['report_date']));
    $dateRevenues[] = (float)$row['total_revenue'];
    $dateCosts[]    = (float)$row['total_cost'];
    $dateProfits[]  = (float)$row['total_profit'];
}
?>

<script>
    (function() {

        // =============================
        // BIỂU ĐỒ THEO LỊCH KH
        // =============================
        new Chart(
            document.getElementById('baocaoChart'), {
                type: 'bar',
                data: {
                    labels: <?= json_encode($labels, JSON_UNESCAPED_UNICODE) ?>,
                    datasets: [{
                            label: "Doanh thu",
                            data: <?= json_encode($revenues) ?>,
                            backgroundColor: "rgba(75, 192, 192, 0.6)"
                        },
                        {
                            label: "Chi phí",
                            data: <?= json_encode($costs) ?>,
                            backgroundColor: "rgba(255, 99, 132, 0.6)"
                        },
                        {
                            label: "Lợi nhuận",
                            data: <?= json_encode($profits) ?>,
                            backgroundColor: "rgba(54, 162, 235, 0.6)"
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            }
        );

        // =============================
        // BIỂU ĐỒ THEO NGÀY
        // =============================
        new Chart(
            document.getElementById('baocaoDateChart'), {
                type: 'line',
                data: {
                    labels: <?= json_encode($dateLabels, JSON_UNESCAPED_UNICODE) ?>,
                    datasets: [{
                            label: "Doanh thu",
                            data: <?= json_encode($dateRevenues) ?>,
                            borderColor: "rgba(75,192,192,1)",
                            tension: 0.2
                        },
                        {
                            label: "Chi phí",
                            data: <?= json_encode($dateCosts) ?>,
                            borderColor: "rgba(255,99,132,1)",
                            tension: 0.2
                        },
                        {
                            label: "Lợi nhuận",
                            data: <?= json_encode($dateProfits) ?>,
                            borderColor: "rgba(54,162,235,1)",
                            tension: 0.2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            }
        );

    })();
</script>