<?php
// Lấy năm hiện tại một lần
$current_year = date('Y');
?>

<div class="container-fluid py-4">

    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
            <h6 class="m-0 font-weight-bold text-white text-uppercase">
                <i class="fas fa-users me-2"></i> DANH SÁCH KHÁCH TRONG ĐOÀN
            </h6>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" width="100%" cellspacing="0">
                    <thead class="bg-light text-secondary">
                        <tr class="text-uppercase text-xs font-weight-bolder" style="font-size: 0.85rem;">
                            <th class="py-3 text-center">Đoàn</th>         <th class="py-3 ps-3">STT</th>                 <th class="py-3 ps-3">Mã Đơn</th>
                            <th class="py-3">Trưởng Đoàn / Liên Hệ</th>
                            <th class="py-3">Thông Tin Khách Hàng</th>
                            <th class="text-center py-3">Giới Tính</th>
                            <th class="text-center py-3">Đối Tượng</th>
                            <th class="py-3">Giấy Tờ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stt_doan = 1;         // Biến đếm STT Đoàn
                        $stt_thanh_vien = 1;   // Biến đếm STT Thành viên
                        $current_tour = null;
                        
                        if (!empty($list_khach)):
                            foreach ($list_khach as $row):
                                // --- LOGIC TÍNH TUỔI ---
                                $nam_sinh = !empty($row['NamSinh']) ? $row['NamSinh'] : $current_year;
                                $age = $current_year - $nam_sinh;
                                $is_child = ($age < 11);
                        ?>
                                <tr>
                                    <?php if ($current_tour != $row['MaDatTour']): ?>
                                        <td class="align-top bg-light text-center pt-3 fw-bold" style="width: 50px;">
                                            <?php echo $stt_doan; ?>
                                        </td>

                                        <td class="align-top bg-light ps-3 pt-3" style="width: 100px;">
                                            <span class="badge bg-primary rounded-pill shadow-sm">
                                                #<?php echo $row['MaDatTour']; ?>
                                            </span>
                                        </td>
                                        
                                        <td class="align-top bg-light pt-3" style="border-right: 2px solid #fff;">
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold text-dark mb-1">
                                                    <i class="fas fa-flag text-warning me-1"></i> <?php echo $row['TruongDoan']; ?>
                                                </span>
                                                <?php if (!empty($row['SDT_LienHe'])): ?>
                                                <a href="tel:<?php echo $row['SDT_LienHe']; ?>" class="text-decoration-none text-muted small mb-2">
                                                    <i class="fas fa-phone-alt me-1 text-success"></i> <?php echo $row['SDT_LienHe']; ?>
                                                </a>
                                                <?php endif; ?>
                                                <div class="mt-2 text-muted small">(Chế độ chỉ xem)</div>
                                            </div>
                                        </td>
                                        
                                        <?php $current_tour = $row['MaDatTour']; ?>
                                        <?php $stt_doan++; $stt_thanh_vien = 1; // TĂNG ĐOÀN & RESET THÀNH VIÊN ?>
                                    <?php else: ?>
                                        <td class="bg-light border-0"></td> 
                                        <td class="bg-light border-0"></td> 
                                        <td class="bg-light border-0" style="border-right: 2px solid #fff;"></td>
                                    <?php endif; ?>

                                    <td class="text-center fw-bold small">
                                        <?php echo $stt_thanh_vien; ?>.
                                    </td>

                                    <td class="py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle d-flex justify-content-center align-items-center me-3 <?php echo $is_child ? 'bg-warning' : 'bg-info'; ?> text-white" 
                                                 style="width: 35px; height: 35px; min-width: 35px;">
                                                <i class="fas <?php echo $is_child ? 'fa-baby' : 'fa-user'; ?>"></i>
                                            </div>
                                            <div>
                                                <p class="mb-0 fw-bold text-dark"><?php echo $row['TenKhach']; ?></p>
                                                <small class="text-muted">Năm sinh: <?php echo $row['NamSinh']; ?></small>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="text-center">
                                        <?php if ($row['GioiTinh'] == 'Nam'): ?>
                                            <span class="text-primary fw-bold" title="Nam"><i class="fas fa-mars fa-lg"></i> Nam</span>
                                        <?php else: ?>
                                            <span class="text-danger fw-bold" title="Nữ"><i class="fas fa-venus fa-lg"></i> Nữ</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-center">
                                        <?php if ($is_child): ?>
                                            <span class="badge bg-warning text-dark rounded-pill px-3 py-2 shadow-sm">
                                                <i class="fas fa-child me-1"></i> Trẻ em (<?php echo $age; ?>t)
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-success rounded-pill px-3 py-2 shadow-sm opacity-75">
                                                Người lớn
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php if (!empty($row['SoCMND_HoChieu'])): ?>
                                            <span class="text-secondary font-monospace bg-light px-2 py-1 rounded small">
                                                <i class="far fa-id-card me-1"></i> <?php echo $row['SoCMND_HoChieu']; ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted small">---</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                </tr>
                            <?php $stt_thanh_vien++; ?>
                            <?php endforeach;
                        else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted"> 
                                    <i class="fas fa-search fa-3x mb-3 text-gray-300"></i><br>
                                    <span class="h6">Không tìm thấy dữ liệu</span><br>
                                    <small>Chưa có khách nào trong lịch trình được phân công.</small>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>

</style>