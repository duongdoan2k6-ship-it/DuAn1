<div class="row">
    <div class="col-6 mx-auto">
        <div class="card my-4">
            <div class="card-header bg-gradient-primary text-white">
                <h5 class="mb-0"><?= htmlspecialchars($pageTitle ?? 'Thêm mới Báo cáo', ENT_QUOTES, 'UTF-8') ?></h5>
            </div>

            <div class="card-body">
                <?php if (!empty($error)) : ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>

                <form method="POST" action="index.php?action=add-baocao">
                    <div class="mb-3">
                        <label class="form-label">Mã Lịch Khởi Hành</label>
                        <input type="text" name="MaLichKhoiHanh" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Doanh Thu</label>
                        <input type="number" name="DoanhThu" class="form-control" min="0" step="0.01" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Chi Phí</label>
                        <input type="number" name="ChiPhi" class="form-control" min="0" step="0.01" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ngày Tạo Báo Cáo</label>
                        <input type="datetime-local" name="NgayTaoBaoCao" class="form-control" required>
                    </div>

                    <button type="submit" class="btn bg-gradient-success me-2 mb-0">
                        Thêm báo cáo
                    </button>

                    <a href="index.php?action=list-baocao" class="btn btn-secondary mb-0">
                        Hủy
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>