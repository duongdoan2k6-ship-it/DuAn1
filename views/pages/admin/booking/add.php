<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center px-4">
                        <h6 class="text-white text-capitalize mb-0">Thêm Mới Booking</h6>
                        <a href="?action=list-booking" class="btn btn-sm btn-light text-primary mb-0">
                            <i class="material-symbols-rounded text-sm me-1">Quay lại</i>
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <form action="?action=store-booking" method="POST" id="bookingForm">

                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="mb-3 text-info">1. Thông tin Khách hàng</h5>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Họ tên khách hàng <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-outline">
                                        <input type="text" name="HoTen" class="form-control" required placeholder="Nhập họ tên đầy đủ">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                                        <div class="input-group input-group-outline">
                                            <input type="text" name="SoDienThoai" class="form-control" required placeholder="09xxxx">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Email</label>
                                        <div class="input-group input-group-outline">
                                            <input type="email" name="Email" class="form-control" placeholder="example@gmail.com">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Địa chỉ</label>
                                    <div class="input-group input-group-outline">
                                        <input type="text" name="DiaChi" class="form-control" placeholder="Địa chỉ liên hệ">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Ghi chú</label>
                                    <div class="input-group input-group-outline">
                                        <textarea name="GhiChu" class="form-control" rows="2" placeholder="Ghi chú thêm (ăn kiêng, đón rước...)"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 border-start">
                                <h5 class="mb-3 text-info">2. Chọn Lịch Trình</h5>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Chọn Tour & Ngày đi <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-outline">
                                        <select name="MaLichKhoiHanh" id="selectLich" class="form-control" required>
                                            <option value="" data-price-adult="0" data-price-child="0" data-seats-left="0">-- Chọn lịch khởi hành --</option>

                                            <?php if (!empty($listLich)): ?>
                                                <?php foreach ($listLich as $sche): ?>
                                                    <?php
                                                    $conTrong = $sche['SoChoToiDa'] - $sche['SoChoDaDat'];
                                                    $giaNL = number_format($sche['GiaNguoiLon']);
                                                    $ngayDi = date('d/m/Y', strtotime($sche['NgayKhoiHanh']));
                                                    ?>
                                                    <option value="<?= $sche['MaLichKhoiHanh'] ?>"
                                                        data-price-adult="<?= $sche['GiaNguoiLon'] ?>"
                                                        data-price-child="<?= $sche['GiaTreEm'] ?>"
                                                        data-seats-left="<?= $conTrong ?>"
                                                        <?= ($conTrong <= 0) ? 'disabled' : '' ?>>

                                                        <?= $sche['TenTour'] ?> - <?= $ngayDi ?>
                                                        (<?= $giaNL ?>đ) - Còn <?= $conTrong ?> chỗ
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option value="" disabled>Không có lịch khởi hành nào sắp tới</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>

                                    <div id="seatInfoBox" class="d-flex align-items-center mt-2 p-2 border rounded bg-white shadow-sm d-none">
                                        <i id="statusIcon" class="material-symbols-rounded me-2">
                                            <div>
                                                <span class="text-xs text-secondary text-uppercase fw-bold">Tình trạng chỗ:</span>
                                                <span id="statusText" class="fw-bold fs-6 ms-1"></span>
                                            </div>
                                        </i>

                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Người lớn</label>
                                        <div class="input-group input-group-outline">
                                            <input type="number" name="SoLuongNguoiLon" id="inputNL" class="form-control fw-bold" value="1" min="1" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Trẻ em</label>
                                        <div class="input-group input-group-outline">
                                            <input type="number" name="SoLuongTreEm" id="inputTE" class="form-control fw-bold" value="0" min="0">
                                        </div>
                                    </div>
                                </div>

                                <div id="limitAlert" class="alert alert-danger text-white text-xs mb-3 d-none">
                                    <i class="material-symbols-rounded text-xs me-1">block</i>
                                    Không thể thêm! Lịch trình này chỉ còn tối đa <b id="limitNumber"></b> chỗ.
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold text-danger">TỔNG TIỀN TẠM TÍNH</label>
                                    <div class="input-group input-group-outline bg-light">
                                        <input type="text" id="displayTongTien" class="form-control fw-bold text-danger fs-4 text-center" readonly value="0 VNĐ">
                                        <input type="hidden" name="TongTien" id="inputTongTien" value="0">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12 text-end">
                                <a href="?action=list-booking" class="btn btn-light border">Hủy bỏ</a>
                                <button type="submit" class="btn btn-primary bg-gradient-primary">
                                    <i class="material-symbols-rounded text-sm me-1"></i> Xác Nhận Đặt Tour
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const selectLich = document.getElementById('selectLich');
    const inputNL = document.getElementById('inputNL');
    const inputTE = document.getElementById('inputTE');
    const displayTongTien = document.getElementById('displayTongTien');
    const inputTongTien = document.getElementById('inputTongTien');

    // Các element hiển thị thông tin
    const seatInfoBox = document.getElementById('seatInfoBox');
    const statusText = document.getElementById('statusText');
    const statusIcon = document.getElementById('statusIcon');
    const limitAlert = document.getElementById('limitAlert');
    const limitNumber = document.getElementById('limitNumber');

    // Biến lưu giới hạn ghế hiện tại của Tour đang chọn
    let currentMaxSeats = 0;

    function updateFormLogic(e) {
        // 1. Lấy thông tin từ Option được chọn
        const selectedOption = selectLich.options[selectLich.selectedIndex];

        const priceAdult = parseFloat(selectedOption.getAttribute('data-price-adult')) || 0;
        const priceChild = parseFloat(selectedOption.getAttribute('data-price-child')) || 0;

        // Nếu thay đổi dropdown, cập nhật lại Max Seats
        if (e && e.target === selectLich) {
            currentMaxSeats = parseInt(selectedOption.getAttribute('data-seats-left')) || 0;
        }

        // 2. Lấy số lượng nhập vào
        let qtyA = parseInt(inputNL.value) || 0;
        let qtyC = parseInt(inputTE.value) || 0;
        let currentTotal = qtyA + qtyC;

        // 3. LOGIC CHẶN NHẬP QUÁ CHỖ
        if (currentMaxSeats > 0 && currentTotal > currentMaxSeats) {
            limitAlert.classList.remove('d-none');
            limitNumber.innerText = currentMaxSeats;

            // Tự động sửa về mức tối đa
            if (e && e.target === inputNL) {
                inputNL.value = currentMaxSeats - qtyC;
                qtyA = parseInt(inputNL.value);
            } else if (e && e.target === inputTE) {
                inputTE.value = currentMaxSeats - qtyA;
                qtyC = parseInt(inputTE.value);
            } else {
                // Trường hợp đổi từ Tour nhiều chỗ -> Tour ít chỗ
                if (qtyA > currentMaxSeats) {
                    inputNL.value = currentMaxSeats;
                    inputTE.value = 0;
                } else {
                    inputTE.value = currentMaxSeats - qtyA;
                }
                qtyA = parseInt(inputNL.value);
                qtyC = parseInt(inputTE.value);
            }
            // Cập nhật lại tổng sau khi sửa
            currentTotal = qtyA + qtyC;
        } else {
            limitAlert.classList.add('d-none');
        }

        // 4. HIỂN THỊ TRẠNG THÁI GHẾ (REALTIME)
        if (selectedOption.value !== "") {
            seatInfoBox.classList.remove('d-none');

            // Tính số ghế còn dư sau khi trừ đi số khách đang nhập
            let remainingSeats = currentMaxSeats - currentTotal;

            if (remainingSeats > 0) {
                statusText.innerText = "Còn dư " + remainingSeats + " ghế";
                statusText.className = "fw-bold text-success fs-6 ms-1";
                statusIcon.className = "material-symbols-rounded text-success me-2";
            } else {
                statusText.innerText = "Đã hết ghế trống";
                statusText.className = "fw-bold text-danger fs-6 ms-1";
                statusIcon.className = "material-symbols-rounded text-danger me-2";
            }
        } else {
            seatInfoBox.classList.add('d-none');
        }

        // 5. TÍNH TIỀN
        const total = (qtyA * priceAdult) + (qtyC * priceChild);

        displayTongTien.value = new Intl.NumberFormat('vi-VN').format(total) + ' VNĐ';
        if (inputTongTien) inputTongTien.value = total;
    }

    // Gán sự kiện
    selectLich.addEventListener('change', updateFormLogic);
    inputNL.addEventListener('input', updateFormLogic);
    inputTE.addEventListener('input', updateFormLogic);
</script>