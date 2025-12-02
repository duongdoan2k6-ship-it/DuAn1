<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">


                <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                    <h6 class="text-white text-capitalize ps-3 mb-0"><?php echo $pageTitle; ?></h6>

                    <a href="index.php?action=add-tour" class="btn bg-gradient-dark me-3 mb-0">
                        <i class="material-icons text-sm"></i>&nbsp;&nbsp;Thêm Tour
                    </a>
                </div>


            </div>
            <div class="card-body px-0 pb-2">


                <table class="table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Tên Tour </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Loại Tour</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Thời Lượng</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Giá Tour</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Trạng Thái</th>
                            <th class="text-secondary opacity-7"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tours as $tour): ?>
                            <tr>
                                <td>
                                    <div class="d-flex px-2 py-1">
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm"><?php echo htmlspecialchars($tour['TenTour']); ?></h6>
                                            <p class="text-xs text-secondary mb-0"> Điểm khởi hành: <?php echo htmlspecialchars($tour['DiaDiemKhoiHanh']); ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0"><?php echo htmlspecialchars($tour['TenLoai']); ?></p>
                                </td>
                                <td>
                                    <span class="text-secondary text-xs font-weight-bold"><?php echo htmlspecialchars($tour['ThoiLuong']); ?></span>
                                </td>
                                <td>
                                    <span class="text-secondary text-xs font-weight-bold">
                                        <?= number_format($tour['GiaTour'], 0, ',', '.') ?> VNĐ
                                    </span>
                                </td>

                                <td>
                                    <?php
                                    $status = $tour['TrangThai'];
                                    $text = $status == 1 ? "Hoạt động" : "tạm dừng";
                                    $color = $status == 1 ? "success" : "danger";
                                    ?>
                                    <span class="badge bg-<?php echo $color; ?>">
                                        <?php echo $text; ?>
                                    </span>
                                </td>
                                <td class="align-middle">
                                    <a href="index.php?action=edit-tour&id=<?= $tour['MaTour'] ?>" class="btn btn-sm btn-warning">Sửa</a>
                                    <a href="index.php?action=delete-tour&id=<?= $tour['MaTour'] ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Bạn có chắc muốn xóa tour này?');">
                                        Xóa
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>



            </div>
        </div>
    </div>
</div>
</div>