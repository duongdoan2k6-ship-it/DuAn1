<div class="row">
    <div class="col-6 mx-auto">
        <div class="card my-4">
            <div class="card-header bg-gradient-primary text-white">
                <h5 class="mb-0"><?= htmlspecialchars($pageTitle ?? 'Cập nhật Báo cáo', ENT_QUOTES, 'UTF-8') ?></h5>
            </div>

            <div class="card-body">
                <form method="POST" action="index.php?action=update-baocao">
                    <input type="hidden" name="BaoCaoID"
                           value="<?= (int)($Baocao['BaoCaoID'] ?? 0) ?>">

                    <div class="mb-3">
                        <label class="form-label">Mã Lịch Khởi Hành</label>
                        <input type="text" name="MaLichKhoiHanh" class="form-control"
                               value="<?= htmlspecialchars($Baocao['MaLichKhoiHanh'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Doanh Thu</label>
                        <input type="number" name="DoanhThu" class="form-control" min="0" step="0.01"
                               value="<?= htmlspecialchars($Baocao['DoanhThu'] ?? 0, ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Chi Phí</label>
                        <input type="number" name="ChiPhi" class="form-control" min="0" step="0.01"
                               value="<?= htmlspecialchars($Baocao['ChiPhi'] ?? 0, ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ngày Tạo Báo Cáo</label>
                        <input type="datetime-local" name="NgayTaoBaoCao" class="form-control"
                               value="<?= htmlspecialchars(date('Y-m-d\TH:i', strtotime($Baocao['NgayTaoBaoCao'] ?? 'now')), ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>

                    <button type="submit" class="btn bg-gradient-success me-2 mb-0">
                        Cập nhật báo cáo
                    </button>

                    <a href="index.php?action=list-baocao" class="btn btn-secondary mb-0">
                        Hủy
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>