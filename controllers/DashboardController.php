<?php
class DashboardController extends BaseController
{
    // Trang Dashboard chính
    // File: controllers/DashboardController.php

public function index()
{
    // 1. Chặn nếu không phải admin
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header('Location: ' . BASE_URL . 'routes/index.php?action=login');
        exit;
    }

    // 2. Lấy số liệu
    $lichModel = new LichKhoiHanhModel();
    $totalTours = $lichModel->countAll();

    $listTours = $lichModel->getAllToursAdmin();
    
    $currentTime = time();
    $processedTours = [];

    foreach ($listTours as $tour) {
        $startTime = strtotime($tour['ngay_khoi_hanh']);
        $endTime   = strtotime($tour['ngay_ket_thuc']);

        $oneDayBefore = $startTime - 86400; 
        if ($currentTime >= $oneDayBefore && $tour['so_cho_da_dat'] < 10 && $tour['trang_thai'] !== 'Huy') {
            $lichModel->updateStatus($tour['id'], 'Huy'); 
            $tour['trang_thai'] = 'Huy'; 
        }
        if ($currentTime > $endTime && $tour['trang_thai'] !== 'HoanThanh' && $tour['trang_thai'] !== 'Huy') {
            $lichModel->updateStatus($tour['id'], 'HoanThanh'); 
            $tour['trang_thai'] = 'HoanThanh';
        }
        $percent = ($tour['so_cho_toi_da'] > 0) ? ($tour['so_cho_da_dat'] / $tour['so_cho_toi_da']) * 100 : 0;
        $tour['view_percent'] = $percent;
        $tour['view_progress_color'] = $percent >= 100 ? 'bg-danger' : 'bg-success';
        
        if ($tour['trang_thai'] === 'Huy') {
            $tour['view_badge'] = ['bg' => 'secondary', 'label' => 'Đã hủy', 'icon' => ''];
        } elseif ($currentTime >= $startTime && $currentTime <= $endTime && $tour['trang_thai'] !== 'Huy') {
            $tour['view_badge'] = ['bg' => 'primary', 'label' => 'Đang đi', 'icon' => '<i class="fas fa-plane"></i>'];
        } elseif ($tour['trang_thai'] === 'HoanThanh' || $currentTime > $endTime) {
             $tour['view_badge'] = ['bg' => 'dark', 'label' => 'Hoàn thành', 'icon' => ''];
        } elseif ($tour['so_cho_da_dat'] >= $tour['so_cho_toi_da']) {
            $tour['view_badge'] = ['bg' => 'danger', 'label' => 'Đã đầy', 'icon' => ''];
        } else {
            $tour['view_badge'] = ['bg' => 'success', 'label' => 'Đang nhận khách', 'icon' => ''];
        }
        
        $tour['can_edit_cancel'] = ($currentTime < $startTime && $tour['trang_thai'] !== 'Huy');
        $tour['can_delete'] = ($currentTime > $endTime || $tour['trang_thai'] === 'Huy');

        $processedTours[] = $tour;
    }
    $this->render('pages/admin/dashboard', [
        'totalTours' => $totalTours,
        'listTours'  => $processedTours 
    ]);
}
    public function create() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: ' . BASE_URL . 'routes/index.php?action=login');
            exit;
        }

        $lichModel = new LichKhoiHanhModel();
        $tours = $lichModel->getAllToursList();
        $guides = $lichModel->getAllHDVList(); 

        $this->render('pages/admin/form_them_lich', [
            'tours' => $tours,
            'guides' => $guides 
        ]);
    }

    // 2. Xử lý lưu dữ liệu
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $lichModel = new LichKhoiHanhModel();

            $tour_id = $_POST['tour_id'];
            $ngay_di = $_POST['ngay_khoi_hanh'];
            $ngay_ve = $_POST['ngay_ket_thuc']; // Lấy từ input readonly của form

            // --- [THÊM MỚI] VALIDATE NGÀY KHỞI HÀNH (BACKEND) ---
            // Mục đích: Chặn trường hợp user cố tình sửa HTML để chọn ngày sớm hơn
            // Logic: Ngày đi phải lớn hơn hoặc bằng (Hôm nay + 3 ngày)
            $minTimestamp = strtotime(date('Y-m-d') . ' +3 days'); 
            $inputTimestamp = strtotime($ngay_di);

            if ($inputTimestamp < $minTimestamp) {
                echo "<script>
                    alert('Lỗi: Ngày khởi hành phải cách hiện tại ít nhất 3 ngày để có thời gian chuẩn bị!'); 
                    window.history.back();
                </script>";
                return;
            }

            if (strtotime($ngay_ve) < strtotime($ngay_di)) {
                echo "<script>alert('Lỗi: Ngày kết thúc phải sau hoặc bằng Ngày khởi hành!'); window.history.back();</script>";
                return;
            }

            $data = [
                'tour_id' => $tour_id,
                'ngay_khoi_hanh' => $ngay_di,
                'ngay_ket_thuc' => $ngay_ve,
                'so_cho_toi_da' => $_POST['so_cho_toi_da'],
                'diem_tap_trung' => $_POST['diem_tap_trung']
            ];

            if ($lichModel->insert($data)) {
                header('Location: ' . BASE_URL . 'routes/index.php?action=admin-dashboard&msg=success');
            } else {
                echo "Lỗi khi thêm mới!";
            }
        }
    }

    // 3. Hiển thị form sửa
    public function edit() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') exit;

        $id = $_GET['id'] ?? 0;
        $lichModel = new LichKhoiHanhModel();
        
        $lich = $lichModel->getDetail($id);
        
        if (!$lich) {
            die("Không tìm thấy lịch trình này!");
        }

        $tours = $lichModel->getAllToursList();
        $hdvs  = $lichModel->getAllHDVList();

        $this->render('pages/admin/form_sua_lich', [
            'lich'  => $lich,
            'tours' => $tours,
            'hdvs'  => $hdvs
        ]);
    }

    // 4. Xử lý cập nhật (ĐÃ FIX: Thêm Logic An Toàn)
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_GET['id'] ?? 0;
            $lichModel = new LichKhoiHanhModel();

            // Lấy dữ liệu từ form
            $ngay_di = $_POST['ngay_khoi_hanh'];
            $ngay_ve = $_POST['ngay_ket_thuc'];
            $so_cho_moi = (int)$_POST['so_cho_toi_da'];

            // [FIX 1] Validate Ngày
            if (strtotime($ngay_ve) < strtotime($ngay_di)) {
                echo "<script>alert('Lỗi: Ngày kết thúc phải sau hoặc bằng Ngày khởi hành!'); window.history.back();</script>";
                return;
            }

            // [FIX 2] Validate Số chỗ: Không được giảm thấp hơn số khách đã đặt
            $currentLich = $lichModel->getDetail($id);
            if ($currentLich) {
                $so_da_dat = (int)$currentLich['so_cho_da_dat'];
                if ($so_cho_moi < $so_da_dat) {
                    echo "<script>
                        alert('KHÔNG THỂ CẬP NHẬT!\\n\\nSố chỗ mới ($so_cho_moi) nhỏ hơn số khách đã đặt ($so_da_dat).\\nVui lòng hủy bớt vé trước khi giảm số chỗ.');
                        window.history.back();
                    </script>";
                    return;
                }
            }
            
            $data = [
                'tour_id' => $_POST['tour_id'],
                'ngay_khoi_hanh' => $ngay_di,
                'ngay_ket_thuc' => $ngay_ve,
                'so_cho_toi_da' => $so_cho_moi,
                'diem_tap_trung' => $_POST['diem_tap_trung'], // Cần đảm bảo View có input name này
                'trang_thai' => $_POST['trang_thai']
            ];

            if ($lichModel->update($id, $data)) {
                header('Location: ' . BASE_URL . 'routes/index.php?action=admin-dashboard&msg=updated');
            } else {
                echo "Lỗi khi cập nhật!";
            }
        }
    }

    // 5. Xóa lịch (ĐÃ FIX: Chặn xóa nếu có khách)
    public function delete() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: ' . BASE_URL . 'routes/index.php?action=login');
            exit;
        }

        $id = $_GET['id'] ?? 0;
        if ($id) {
            $lichModel = new LichKhoiHanhModel();
            
            // [FIX] Kiểm tra xem tour này có khách không
            $lich = $lichModel->getDetail($id);
            if ($lich && $lich['so_cho_da_dat'] > 0) {
                 echo "<script>
                    alert('CẢNH BÁO: Không thể xóa lịch trình này!\\n\\nĐang có " . $lich['so_cho_da_dat'] . " khách đã đặt tour. Xóa sẽ làm mất dữ liệu quan trọng.');
                    window.location.href = '" . BASE_URL . "routes/index.php?action=admin-dashboard';
                </script>";
                exit;
            }

            if ($lichModel->delete($id)) {
                header('Location: ' . BASE_URL . 'routes/index.php?action=admin-dashboard&msg=deleted');
            } else {
                echo "Lỗi: Không thể xóa (Có thể tour này đã có khách đặt hoặc dữ liệu liên quan!)";
            }
        } else {
            header('Location: ' . BASE_URL . 'routes/index.php?action=admin-dashboard');
        }
    }

    // ... (Giữ nguyên các hàm services, storeService, updateService, deleteService, staffAssignment, storeStaff, deleteStaff phía dưới) ...
    // Bạn chỉ cần copy đè phần trên của file cũ là được.
    
    // ==========================================================
    // MODULE QUẢN LÝ DỊCH VỤ (ĐIỀU HÀNH) - Giữ nguyên
    // ==========================================================
    
    public function services() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') exit;

        $id = $_GET['id'] ?? 0; 
        $lichModel = new LichKhoiHanhModel();
        
        $lich = $lichModel->getDetail($id);
        if (!$lich) die('Không tìm thấy lịch khởi hành!');

        $services = $lichModel->getServices($id); 
        $suppliers = (new SupplierModel())->getAll(); 
        
        $this->render('pages/admin/quan_ly_dich_vu', [
            'lich' => $lich,
            'services' => $services,
            'suppliers' => $suppliers
        ]);
    }

    public function storeService() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $lichModel = new LichKhoiHanhModel();
            
            $lich_id = $_POST['lich_id'];
            $ngay_sd = $_POST['ngay_su_dung'];
            
            $lich = $lichModel->getDetail($lich_id);
            if ($lich) {
                $start = strtotime($lich['ngay_khoi_hanh']);
                $end = strtotime($lich['ngay_ket_thuc']);
                $current = strtotime($ngay_sd);

                if ($current < $start || $current > $end) {
                    echo "<script>alert('Lỗi: Ngày sử dụng dịch vụ phải nằm trong thời gian tour diễn ra!'); window.history.back();</script>";
                    return;
                }
            }

            $data = [
                'lich_id' => $lich_id,
                'ncc_id' => $_POST['ncc_id'],
                'loai_dv' => $_POST['loai_dich_vu'],
                'ngay_sd' => $ngay_sd,
                'sl' => $_POST['so_luong'],
                'ghi_chu' => $_POST['ghi_chu']
            ];
            
            $lichModel->addService($data);
            header('Location: ' . BASE_URL . 'routes/index.php?action=admin-schedule-services&id=' . $lich_id);
        }
    }

    public function deleteService() {
        $id = $_GET['id'];
        $lich_id = $_GET['lich_id'];
        (new LichKhoiHanhModel())->deleteService($id);
        header('Location: ' . BASE_URL . 'routes/index.php?action=admin-schedule-services&id=' . $lich_id);
    }

    public function updateService() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $lichModel = new LichKhoiHanhModel();
            
            $id = $_POST['id'];             
            $lich_id = $_POST['lich_id'];   
            $ngay_sd = $_POST['ngay_su_dung'];

            $lich = $lichModel->getDetail($lich_id);
            if ($lich) {
                $start = strtotime($lich['ngay_khoi_hanh']);
                $end = strtotime($lich['ngay_ket_thuc']);
                $current = strtotime($ngay_sd);

                if ($current < $start || $current > $end) {
                    echo "<script>alert('Lỗi: Ngày sử dụng dịch vụ phải nằm trong thời gian tour diễn ra!'); window.history.back();</script>";
                    return;
                }
            }

            $data = [
                'ncc_id' => $_POST['ncc_id'],
                'loai_dv' => $_POST['loai_dich_vu'],
                'ngay_sd' => $ngay_sd,
                'sl' => $_POST['so_luong'],
                'ghi_chu' => $_POST['ghi_chu']
            ];

            if ($lichModel->updateService($id, $data)) {
                header('Location: ' . BASE_URL . 'routes/index.php?action=admin-schedule-services&id=' . $lich_id . '&msg=service_updated');
            } else {
                echo "Lỗi khi cập nhật dịch vụ!";
            }
        }
    }

    // ==========================================================
    // MODULE MỚI: QUẢN LÝ PHÂN BỔ NHÂN SỰ (HDV, TÀI XẾ...)
    // ==========================================================

    public function staffAssignment() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') exit;

        $lichId = $_GET['id'] ?? null;
        if (!$lichId) die("Thiếu ID Lịch trình.");

        $lichModel = new LichKhoiHanhModel();
        
        // Lấy thông tin lịch
        $lich = $lichModel->getDetail($lichId);
        if (!$lich) die("Lịch trình không tồn tại.");

        // Lấy danh sách nhân sự đã phân bổ cho lịch này
        $assignedStaff = $lichModel->getAssignedStaff($lichId);

        // Lấy danh sách toàn bộ nhân sự để chọn
        $allStaff = $lichModel->getAllNhanVienList();
        
        $this->render('pages/admin/quan_ly_nhan_su', [
            'lich' => $lich,
            'assignedStaff' => $assignedStaff,
            'allStaff' => $allStaff,
        ]);
    }

    public function storeStaff() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $lichModel = new LichKhoiHanhModel();
            
            $lichId = $_POST['lich_id'];
            $nhanVienId = $_POST['nhan_vien_id'];
            $vaiTro = $_POST['vai_tro'];

            // A. Lấy thông tin cần thiết
            $lich = $lichModel->getDetail($lichId);
            $allStaff = $lichModel->getAllNhanVienList();
            
            // Tìm thông tin nhân viên đang chọn để biết Phân loại (HDV/TaiXe/HauCan)
            $staffInfo = null;
            foreach ($allStaff as $s) {
                if ($s['id'] == $nhanVienId) {
                    $staffInfo = $s;
                    break;
                }
            }

            if (!$lich || !$staffInfo) {
                echo "<script>alert('Dữ liệu không hợp lệ!'); window.history.back();</script>";
                return;
            }

            // B. Kiểm tra logic nghiệp vụ
            // Logic 1: Kiểm tra trùng lịch (Chỉ áp dụng cho HDV và Tài Xế)
            if ($staffInfo['phan_loai_nhan_su'] !== 'HauCan') {
                $isFree = $lichModel->checkStaffAvailability(
                    $nhanVienId, 
                    $lich['ngay_khoi_hanh'], 
                    $lich['ngay_ket_thuc'], 
                    $staffInfo['phan_loai_nhan_su']
                );

                if (!$isFree) {
                    echo "<script>alert('Lỗi: Nhân sự này ĐÃ CÓ LỊCH trong thời gian này!'); window.history.back();</script>";
                    return;
                }
            }

            if ($vaiTro === 'HDV_chinh') {
                $assigned = $lichModel->getAssignedStaff($lichId);
                foreach ($assigned as $a) {
                    if ($a['vai_tro'] === 'HDV_chinh') {
                        echo "<script>alert('Lỗi: Chuyến đi này đã có HDV Chính rồi!'); window.history.back();</script>";
                        return;
                    }
                }
            }

            if ($lichModel->assignStaff($lichId, $nhanVienId, $vaiTro)) {
                header('Location: ' . BASE_URL . 'routes/index.php?action=admin-schedule-staff&id=' . $lichId . '&msg=assigned');
            } else {
                echo "Lỗi hệ thống khi lưu!";
            }
        }
    }

    public function deleteStaff() {
        $id = $_GET['id'];
        $lichId = $_GET['lich_id'];

        $lichModel = new LichKhoiHanhModel();
        $lichModel->unassignStaff($id);

        header('Location: ' . BASE_URL . 'routes/index.php?action=admin-schedule-staff&id=' . $lichId . '&msg=deleted');
    }
}
?>