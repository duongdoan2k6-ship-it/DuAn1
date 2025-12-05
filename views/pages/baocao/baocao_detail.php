<div class="row">
    <div class="col-8 mx-auto">
        <div class="card my-4">

            <!-- HEADER -->
            <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <?= htmlspecialchars($pageTitle ?? 'Chi tiết Báo cáo tài chính', ENT_QUOTES, 'UTF-8') ?>
                </h5>

                <a href="index.php?action=list-baocao" class="btn btn-light btn-sm">
                    Quay lại danh sách
                </a>
            </div>

            <!-- BODY -->
            <div class="card-body">
                <?php
                $doanhThu = (float)($Baocao['DoanhThu'] ?? 0);
                $chiPhi   = (float)($Baocao['ChiPhi'] ?? 0);
                $loiNhuan = (float)($Baocao['LoiNhuan'] ?? 0);

                $marginPct = $doanhThu > 0 ? round($loiNhuan / $doanhThu * 100, 2) : 0;

                $statusTxt = $loiNhuan > 0 ? 'Lãi' : ($loiNhuan < 0 ? 'Lỗ' : 'Hòa vốn');
                $statusCls = $loiNhuan > 0 ? 'text-success' : ($loiNhuan < 0 ? 'text-danger' : 'text-muted');
                ?>

                <dl class="row mb-0">
                    <!-- ID -->
                    <dt class="col-sm-4">Báo cáo ID</dt>
                    <dd class="col-sm-8">
                        <?= (int)($Baocao['BaoCaoID'] ?? 0) ?>
                    </dd>

                    <!-- Mã lịch KH -->
                    <dt class="col-sm-4">Mã lịch khởi hành</dt>
                    <dd class="col-sm-8">
                        <?= htmlspecialchars($Baocao['MaLichKhoiHanh'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </dd>

                    <!-- Doanh thu -->
                    <dt class="col-sm-4">Doanh thu</dt>
                    <dd class="col-sm-8">
                        <?= number_format($doanhThu, 0, ',', '.') ?> đ
                    </dd>

                    <!-- Chi phí -->
                    <dt class="col-sm-4">Chi phí</dt>
                    <dd class="col-sm-8">
                        <?= number_format($chiPhi, 0, ',', '.') ?> đ
                    </dd>

                    <!-- Lợi nhuận -->
                    <dt class="col-sm-4">Lợi nhuận</dt>
                    <dd class="col-sm-8 <?= $statusCls ?>">
                        <?= number_format($loiNhuan, 0, ',', '.') ?> đ
                        (<?= $statusTxt ?>)
                    </dd>

                    <!-- Biên lợi nhuận -->
                    <dt class="col-sm-4">Biên lợi nhuận</dt>
                    <dd class="col-sm-8">
                        <?= $marginPct ?> %
                    </dd>

                    <!-- Ngày tạo -->
                    <dt class="col-sm-4">Ngày tạo báo cáo</dt>
                    <dd class="col-sm-8">
                        <?= htmlspecialchars($Baocao['NgayTaoBaoCao'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </dd>
</dl>
            </div>
        </div>
    </div>
</div>
