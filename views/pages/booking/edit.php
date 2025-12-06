<?php
// 1. LOGIC KIỂM TRA QUYỀN SỬA
$allowEdit = ($booking['MaTrangThai'] == 1);
$disabled = $allowEdit ? '' : 'disabled';

// 2. LOGIC TÍNH GIỚI HẠN CHỖ NGỒI
$maxSeats = $booking['SoChoToiDa'] ?? 0;
$bookedSeats = $booking['SoChoDaDat'] ?? 0;
$currentGuests = $booking['SoLuongKhach'] ?? 0;

// Số ghế trống thực tế trên xe (cho người khác)
$seatsLeftOnBus = $maxSeats - $bookedSeats; 
// Giới hạn tối đa riêng cho đơn này (= Ghế trống + Ghế đơn này đang giữ)
$maxLimitForThisBooking = $seatsLeftOnBus + $currentGuests; 

// Màu sắc hiển thị ban đầu
$seatColor = 'text-success';
if ($seatsLeftOnBus <= 0) $seatColor = 'text-danger';
elseif ($seatsLeftOnBus < 5) $seatColor = 'text-warning';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-warning shadow-warning border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center px-4">
                        <h6 class="text-white text-capitalize mb-0">
                            <?= $allowEdit ? 'Cập Nhật Đơn Hàng' : 'Chi Tiết Đơn Hàng' ?> #<?= $booking['MaDatTour'] ?>
                        </h6>
                        <a href="?action=list-booking" class="btn btn-sm btn-light text-warning fw-bold mb-0">
                            <i class="material-symbols-rounded text-sm me-1">arrow_back</i> Quay lại
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    
                    <?php if (!$allowEdit): ?>
                        <div class="alert alert-danger text-white mb-4" role="alert">
                            <i class="material-symbols-rounded text-sm me-1">lock</i>
                            Đơn hàng này đã hoàn tất hoặc đã hủy. Bạn không thể chỉnh sửa thông tin được nữa.
                        </div>
                    <?php endif; ?>

                    <form action="?action=update-booking" method="POST">
                        <input type="hidden" name="id" value="<?= $booking['MaDatTour'] ?>">

                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="mb-3 text-warning">1. Thông tin đơn hàng</h5>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Trạng thái hiện tại</label>
                                    <div class="input-group input-group-outline bg-white is-filled">
                                        <select name="MaTrangThai" class="form-control fw-bold text-primary" <?= $disabled ?>>
                                            <?php foreach ($listTrangThai as $tt): ?>
                                                <option value="<?= $tt['MaTrangThai'] ?>" 
                                                    <?= ($booking['MaTrangThai'] == $tt['MaTrangThai']) ? 'selected' : '' ?>>
                                                    <?= $tt['TenTrangThai'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <?php if ($allowEdit): ?>
                                        <small class="text-muted fst-italic">Bạn có thể chuyển sang "Đã xác nhận" hoặc "Đã hủy".</small>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Tên khách hàng</label>
                                    <div class="input-group input-group-outline is-filled">
                                        <input type="text" name="TenKhachHang" class="form-control" 
                                               value="<?= $booking['TenKhachHang'] ?>" required <?= $disabled ?>>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Thông tin liên hệ</label>
                                    <div class="input-group input-group-outline is-filled">
                                        <input type="text" name="LienHeKhachHang" class="form-control" 
                                               value="<?= $booking['LienHeKhachHang'] ?>" required <?= $disabled ?>>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Ghi chú</label>
                                    <div class="input-group input-group-outline is-filled">
                                        <textarea name="GhiChu" class="form-control" rows="3" <?= $disabled ?>><?= $booking['GhiChu'] ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 border-start bg-light rounded p-3">
                                <h5 class="mb-3 text-secondary">2. Thông tin Tour & Số lượng</h5>
                                
                                <div class="mb-3">
                                    <label class="fw-bold text-xs text-uppercase text-secondary">Tên Tour</label>
                                    <p class="fw-bold text-dark mb-1"><?= $booking['TenTour'] ?></p>
                                    <input type="hidden" name="MaLichKhoiHanh" value="<?= $booking['MaLichKhoiHanh'] ?>">

                                    <div class="d-flex align-items-center mt-2 p-2 border rounded bg-white shadow-sm">
                                        <i id="statusIcon" class="material-symbols-rounded <?= $seatColor ?> me-2">event_seat</i>
                                        <div>
                                            <span class="text-xs text-secondary text-uppercase fw-bold">Tình trạng chỗ:</span>
                                            <span id="statusText" class="fw-bold <?= $seatColor ?> fs-6 ms-1">
                                                <?= ($seatsLeftOnBus > 0) ? "Còn dư $seatsLeftOnBus ghế" : "Đã hết ghế trống" ?>
                                            </span>
                                            <div class="text-xs text-muted">
                                                (Đơn này đang giữ: <?= $currentGuests ?> ghế)
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" id="priceAdult" value="<?= $booking['GiaNguoiLon'] ?>">
                                <input type="hidden" id="priceChild" value="<?= $booking['GiaTreEm'] ?>">
                                <input type="hidden" id="maxLimit" value="<?= $maxLimitForThisBooking ?>">

                                <div class="row mb-3">
                                    <div class="col-6">
                                        <label class="fw-bold text-xs text-uppercase text-secondary">Người lớn</label>
                                        <input type="number" name="SoLuongNguoiLon" id="inputAdult" class="form-control border px-2 bg-white fw-bold" 
                                               value="<?= $booking['SoLuongNguoiLon'] ?? $booking['SoLuongKhach'] ?>" min="1" required <?= $disabled ?>>
                                        <small class="text-muted text-xs">Giá: <?= number_format($booking['GiaNguoiLon']) ?></small>
                                    </div>
                                    <div class="col-6">
                                        <label class="fw-bold text-xs text-uppercase text-secondary">Trẻ em</label>
                                        <input type="number" name="SoLuongTreEm" id="inputChild" class="form-control border px-2 bg-white fw-bold" 
                                               value="<?= $booking['SoLuongTreEm'] ?? 0 ?>" min="0" <?= $disabled ?>>
                                        <small class="text-muted text-xs">Giá: <?= number_format($booking['GiaTreEm']) ?></small>
                                    </div>
                                </div>

                                <div id="limitAlert" class="alert alert-danger text-white text-xs mb-3 d-none">
                                    <i class="material-symbols-rounded text-xs me-1">block</i>
                                    Không thể thêm! Chuyến đi chỉ còn tối đa <b><?= $maxLimitForThisBooking ?></b> chỗ (bao gồm chỗ của bạn).
                                </div>

                                <hr>

                                <div class="mb-3 text-end">
                                    <label class="fw-bold text-xs text-uppercase text-secondary">Tổng Tiền Cập Nhật</label>
                                    <h3 class="text-danger fw-bold" id="displayTotal"><?= number_format($booking['TongTien']) ?> VNĐ</h3>
                                    <input type="hidden" name="TongTien" id="inputTotal" value="<?= $booking['TongTien'] ?>">
                                </div>
                            </div>
                        </div>

                        <?php if ($allowEdit): ?>
                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-warning bg-gradient-warning text-white w-100 fw-bold">
                                    <i class="material-symbols-rounded text-sm me-1">save</i> CẬP NHẬT THÔNG TIN
                                </button>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const inputAdult = document.getElementById('inputAdult');
    const inputChild = document.getElementById('inputChild');
    const priceAdult = parseFloat(document.getElementById('priceAdult').value) || 0;
    const priceChild = parseFloat(document.getElementById('priceChild').value) || 0;
    const maxLimit   = parseInt(document.getElementById('maxLimit').value) || 0; 
    
    const displayTotal = document.getElementById('displayTotal');
    const inputTotal = document.getElementById('inputTotal');
    const limitAlert = document.getElementById('limitAlert');
    
    // Các phần tử hiển thị trạng thái ghế
    const statusText = document.getElementById('statusText');
    const statusIcon = document.getElementById('statusIcon');

    function updateFormLogic(e) {
        let qtyA = parseInt(inputAdult.value) || 0;
        let qtyC = parseInt(inputChild.value) || 0;
        let currentTotal = qtyA + qtyC;

        // 1. LOGIC CHẶN NHẬP LIỆU QUÁ GIỚI HẠN
        if (currentTotal > maxLimit) {
            limitAlert.classList.remove('d-none');
            
            // Tự động sửa về mức tối đa
            if (e && e.target === inputAdult) {
                inputAdult.value = maxLimit - qtyC;
                qtyA = parseInt(inputAdult.value);
            } else if (e && e.target === inputChild) {
                inputChild.value = maxLimit - qtyA;
                qtyC = parseInt(inputChild.value);
            }
            // Tính lại tổng sau khi sửa
            currentTotal = qtyA + qtyC;
        } else {
            limitAlert.classList.add('d-none');
        }

        // 2. LOGIC CẬP NHẬT SỐ GHẾ DƯ REALTIME (MỚI)
        // Số ghế dư = Giới hạn tối đa - Tổng số khách đang nhập
        let remainingSeats = maxLimit - currentTotal;
        
        if (remainingSeats > 0) {
            statusText.innerText = "Còn dư " + remainingSeats + " ghế";
            statusText.className = "fw-bold text-success fs-6 ms-1";
            statusIcon.className = "material-symbols-rounded text-success me-2";
        } else {
            statusText.innerText = "Đã hết ghế trống";
            statusText.className = "fw-bold text-danger fs-6 ms-1";
            statusIcon.className = "material-symbols-rounded text-danger me-2";
        }

        // 3. LOGIC TÍNH TIỀN
        let total = (qtyA * priceAdult) + (qtyC * priceChild);
        displayTotal.innerText = new Intl.NumberFormat('vi-VN').format(total) + ' VNĐ';
        inputTotal.value = total;
    }

    // Gắn sự kiện
    if(inputAdult && inputChild) {
        inputAdult.addEventListener('input', updateFormLogic);
        inputChild.addEventListener('input', updateFormLogic);
    }
</script>