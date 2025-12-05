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
                    <form action="?route=store-booking" method="POST" id="bookingForm">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="mb-3 text-info">1. Thông tin Khách hàng</h5>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Tên khách hàng <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-outline">
                                        <input type="text" name="TenKhachHang" class="form-control" required placeholder="Nhập họ tên đầy đủ">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Liên hệ (SĐT/Email) <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-outline">
                                        <input type="text" name="LienHeKhachHang" class="form-control" required placeholder="Nhập số điện thoại hoặc email">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Ghi chú</label>
                                    <div class="input-group input-group-outline">
                                        <textarea name="GhiChu" class="form-control" rows="3" placeholder="Ghi chú thêm (ăn kiêng, đón rước...)"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 border-start">
                                <h5 class="mb-3 text-info">2. Chọn Lịch Trình</h5>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Chọn Tour & Ngày đi <span class="text-danger">*</span></label>
<div class="input-group input-group-outline">
                                        <select name="MaLichKhoiHanh" id="selectLich" class="form-control" required>
                                            <option value="" data-price="0">-- Chọn lịch khởi hành --</option>
                                            
                                            <?php if (!empty($schedules)): ?>
                                                <?php foreach ($schedules as $sche): ?>
                                                    <option value="<?= $sche['MaLichKhoiHanh'] ?>" 
                                                            data-price="<?= $sche['GiaNguoiLon'] ?>">
                                                        <?= $sche['TenTour'] ?> - Khởi hành: <?= date('d/m/Y', strtotime($sche['NgayKhoiHanh'])) ?> 
                                                        (Giá: <?= number_format($sche['GiaNguoiLon']) ?>đ)
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option value="" disabled>Không có lịch khởi hành nào sắp tới</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Số lượng khách <span class="text-danger">*</span></label>
                                        <div class="input-group input-group-outline">
                                            <input type="number" name="SoLuongKhach" id="inputSoLuong" class="form-control" value="1" min="1" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Giá tạm tính (VNĐ)</label>
                                        <div class="input-group input-group-outline bg-light">
                                            <input type="text" name="TongTienDisplay" id="displayTongTien" class="form-control fw-bold text-danger" readonly value="0">
                                            <input type="hidden" name="TongTien" id="inputTongTien" value="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12 text-end">
<a href="?route=list-booking" class="btn btn-light border">Hủy bỏ</a>
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
    const inputSoLuong = document.getElementById('inputSoLuong');
    const displayTongTien = document.getElementById('displayTongTien');
    const inputTongTien = document.getElementById('inputTongTien');

    function calculateTotal() {
        const selectedOption = selectLich.options[selectLich.selectedIndex];
        const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
        const quantity = parseInt(inputSoLuong.value) || 0;
        const total = price * quantity;
        displayTongTien.value = new Intl.NumberFormat('vi-VN').format(total) + ' VNĐ';

        inputTongTien.value = total;
    }

    selectLich.addEventListener('change', calculateTotal);
    inputSoLuong.addEventListener('input', calculateTotal);
</script>