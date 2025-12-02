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
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Thông tin HDV</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Giới Tính</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Ngày Sinh</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Liên hệ</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Địa Chỉ</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">CCCD</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Loại du lịch</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Trạng Thái</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Ngôn Ngữ</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kinh Nghiệm</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Sức Khỏe</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Số Tour đã đi</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Đánh Giá</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Thao Tác</th>
                                <th class="text-secondary opacity-7"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($person as $item) { ?>
                                <tr>
                                    <td class="align-middle">
                                        <p class="text-xs font-weight-bold mb-0 ps-3"><?= $item['MaHDV'] ?></p>
                                    </td>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div>
                                                <img src="uploads/<?= $item['AnhDaiDien'] ?>" class="avatar avatar-sm me-3 border-radius-lg" alt="user1" style="object-fit: cover;">
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm"><?= $item['HoTen'] ?></h6>
                                                <p class="text-xs text-secondary mb-0"><?= $item['MaThe'] ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle text-sm">
                                        <span class="badge badge-sm bg-gradient-<?= $item['GioiTinh'] == 'Nam' ? 'info' : ($item['GioiTinh'] == 'Nữ' ? 'danger' : 'secondary') ?>">
                                            <?= $item['GioiTinh'] ?>
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="text-secondary text-xs font-weight-bold"><?= date('d/m/Y', strtotime($item['NgaySinh'])) ?></span>
                                    </td>
                                    <td class="align-middle">
                                        <p class="text-xs font-weight-bold mb-0"><?= $item['SDT'] ?></p>
                                        <p class="text-xs text-secondary mb-0"><?= $item['Email'] ?></p>
                                    </td>
                                    <td class="align-middle">
                                        <span class="text-secondary text-xs font-weight-bold d-inline-block text-truncate" style="max-width: 150px;" title="<?= $item['DiaChi'] ?>">
                                            <?= $item['DiaChi'] ?>
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="text-secondary text-xs font-weight-bold"><?= $item['CCCD'] ?></span>
                                    </td>
                                    <td class="align-middle">
                                        <p class="text-xs font-weight-bold mb-0"><?= $item['LoaiHDV'] ?></p>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <?php
                                        $statusColor = 'secondary';
                                        if ($item['TrangThai'] == 'Rảnh') $statusColor = 'success';
                                        if ($item['TrangThai'] == 'Bận' || $item['TrangThai'] == 'Đang đi tour') $statusColor = 'warning';
                                        if ($item['TrangThai'] == 'Nghỉ Phép') $statusColor = 'danger';
                                        ?>
                                        <span class="badge badge-sm bg-gradient-<?= $statusColor ?>"><?= $item['TrangThai'] ?></span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="text-secondary text-xs font-weight-bold"><?= $item['NgonNgu'] ?></span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="text-secondary text-xs d-inline-block text-truncate" style="max-width: 120px;" title="<?= $item['KinhNghiem'] ?>">
                                            <?= $item['KinhNghiem'] ?>
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="text-secondary text-xs font-weight-bold"><?= $item['TinhTrangSucKhoe'] ?></span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold"><?= $item['Sotour'] ?></span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold"><?= $item['DanhGia'] ?> / 5</span>
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
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>