<?php
class BookingController extends BaseController {

    // Danh sách booking
    public function index() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: ' . BASE_URL . 'routes/index.php?action=login');
            exit;
        }

        $bookingModel = new BookingModel();
        $bookings = $bookingModel->getAllBookings();

        $this->render('pages/admin/bookings', ['bookings' => $bookings]);
    }

    // --- [ĐÃ SỬA] Xử lý đổi trạng thái & Ghi lịch sử ---
    public function updateStatus() {
        // 1. Kiểm tra quyền Admin
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: ' . BASE_URL . 'routes/index.php?action=login');
            exit;
        }

        $id = $_GET['id'] ?? 0;
        $status = $_GET['status'] ?? '';

        // 2. Lấy tên admin từ Session (Dựa vào AuthController: $_SESSION['user'] chứa thông tin admin)
        $adminName = $_SESSION['user']['ho_ten'] ?? 'Administrator'; 

        // Các trạng thái hợp lệ
        $validStatus = ['ChoXacNhan', 'DaXacNhan', 'DaThanhToan', 'Huy'];

        if ($id && in_array($status, $validStatus)) {
            $bookingModel = new BookingModel();
            
            // Ghi chú mặc định khi click nhanh
            $note = "Cập nhật nhanh từ trang danh sách";
            
            // 3. GỌI HÀM MODEL VÀ KIỂM TRA KẾT QUẢ
            // Hàm updateStatusAndLog trả về true (thành công) hoặc false (thất bại/hết chỗ)
            $result = $bookingModel->updateStatusAndLog($id, $status, $adminName, $note);

            if ($result) {
                // Thành công: Quay lại trang danh sách
                header('Location: ' . BASE_URL . 'routes/index.php?action=admin-bookings');
                exit;
            } else {
                // Thất bại (Thường là do Tour hết chỗ khi cố gắng khôi phục vé Hủy)
                // Sử dụng JS để Alert rồi mới chuyển trang
                echo "<script>
                        alert('KHÔNG THỂ CẬP NHẬT TRẠNG THÁI!\\n\\nNguyên nhân có thể:\\n1. Tour đã hết chỗ trống (khi bạn khôi phục vé Hủy).\\n2. Lỗi hệ thống.');
                        window.location.href = '" . BASE_URL . "routes/index.php?action=admin-bookings';
                      </script>";
                exit;
            }
        }

        // Trường hợp tham số không hợp lệ, quay về danh sách
        header('Location: ' . BASE_URL . 'routes/index.php?action=admin-bookings');
    }

    // 1. Hiển thị form tạo booking
    public function create() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') exit;

        // Lấy danh sách lịch khởi hành để admin chọn
        $lkhModel = new LichKhoiHanhModel();
        $schedules = $lkhModel->getOpenSchedules();

        $this->render('pages/admin/bookings/create', ['schedules' => $schedules]);
    }

    // 2. Xử lý lưu booking
    public function store() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') exit;

        // Nhận dữ liệu
        $lichId = $_POST['lich_khoi_hanh_id'];
        $soLon = (int)$_POST['so_nguoi_lon'];
        $soTre = (int)($_POST['so_tre_em'] ?? 0);
        $tongKhach = $soLon + $soTre;

        // Kiểm tra logic: Chỗ trống
        $lkhModel = new LichKhoiHanhModel();
        $lkh = $lkhModel->getDetail($lichId);
        
        // Cần query thêm giá tour để tính tiền
        $tourModel = new TourModel();
        $tour = $tourModel->getDetail($lkh['tour_id']);

        // Kiểm tra chỗ
        if (($lkh['so_cho_da_dat'] + $tongKhach) > $lkh['so_cho_toi_da']) {
            echo "<script>alert('Không đủ chỗ trống!'); window.history.back();</script>";
            exit;
        }

        // Tính tiền
        $tongTien = ($soLon * $tour['gia_nguoi_lon']) + ($soTre * $tour['gia_tre_em']);

        // Lưu vào DB
        $bookingModel = new BookingModel();
        $data = [
            'lich_id' => $lichId,
            'ten' => $_POST['ten_nguoi_dat'],
            'sdt' => $_POST['sdt_lien_he'],
            'email' => $_POST['email_lien_he'],
            'sl_lon' => $soLon,
            'sl_tre' => $soTre,
            'tong_tien' => $tongTien,
            'ghi_chu' => $_POST['ghi_chu']
        ];

        $newId = $bookingModel->create($data);

        if ($newId) {
            // Cập nhật số chỗ đã đặt
            $lkhModel->updateSoCho($lichId, $tongKhach);
            header('Location: index.php?action=admin-bookings');
        } else {
            echo "Lỗi hệ thống!";
        }
    }

    public function detail() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') exit;

        $id = $_GET['id'] ?? 0;
        $bookingModel = new BookingModel();
        
        // 1. Lấy thông tin chi tiết đơn hàng
        $booking = $bookingModel->getDetail($id); // Hàm này đã có sẵn trong file gốc bạn gửi

        // 2. Lấy lịch sử xử lý
        $history = $bookingModel->getHistory($id);

        if (!$booking) {
            echo "Không tìm thấy đơn hàng!";
            exit;
        }

        $this->render('pages/admin/bookings/detail', [
            'booking' => $booking,
            'history' => $history
        ]);
    }
}


?>