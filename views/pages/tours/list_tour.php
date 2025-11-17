
 
<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-capitalize ps-3"><?php echo $pageTitle; ?></h6>
                    <a href="index.php?action=add-tour" class="btn bg-gradient-dark me-3 mb-0">
                        <i class="material-icons text-sm"></i>&nbsp;&nbsp;Thêm Tour
                    </a>
                </div>
            </div>
            <div class="card-body px-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tên Tour & Nơi khởi hành</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Loại Tour</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Thời Lượng</th>
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
                                                <p class="text-xs text-secondary mb-0"><?php echo htmlspecialchars($tour['DiaDiemKhoiHanh']); ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0"><?php echo htmlspecialchars($tour['TenLoai']); ?></p>
                                    </td>
                                    <td>
                                        <span class="text-secondary text-xs font-weight-bold"><?php echo htmlspecialchars($tour['ThoiLuong']); ?></span>
                                    </td>
                                    <td class="align-middle">
                                        <a href="#" class="text-secondary font-weight-bold text-xs">
                                            Sửa
                                        </a>
                                        |
                                        <a href="#" class="text-danger font-weight-bold text-xs">
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