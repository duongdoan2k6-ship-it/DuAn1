<!-- Xuân Nam -->

<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                    <h6 class="text-white text-capitalize ps-3 mb-0"><?php echo $pageTitle; ?></h6>

                    <a href="index.php?action=formAddPerson" class="btn bg-gradient-dark me-3 mb-0">
                        <i class="material-icons text-sm"></i>&nbsp;&nbsp;Thêm Nhân sự
                    </a>
                </div>
            </div>
            <div class="card-body px-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tên HDV</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Ngày Sinh</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Ảnh</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Số Điện thoại</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Ngôn ngữ</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kinh nghiệm</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tình trang sức khoẻ</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Thao tác</th>
                                <th class="text-secondary opacity-7"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($person as $item) { ?>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"><?= $item['MaHDV'] ?></th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2"><?= $item['HoTen'] ?></th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2"><?= $item['NgaySinh'] ?></th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2"><img src="<?= $item['AnhDaiDien'] ?>" alt="Ảnh đại diện"></th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2"><?= $item['ThongTinLienHe'] ?></th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2"><?= $item['NgonNgu'] ?></th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2"><?= $item['KinhNghiem'] ?></th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2"><?= $item['TinhTrangSucKhoe'] ?></th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        <a class="btn btn-warning btn-sm" href="index.php?controller=person&action=edit&id=<?= $item['MaHDV'] ?>">Sửa</a>
                                        <a class="btn btn-danger btn-sm" href="index.php?controller=person&action=delete&id=<?= $item['MaHDV'] ?>">Xoá</a>
                                    </th>
                                    <th class="text-secondary opacity-7"></th>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>