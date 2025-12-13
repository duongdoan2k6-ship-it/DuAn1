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

    // --- Xử lý đổi trạng thái & Ghi lịch sử ---
    public function updateStatus() {
        // 1. Kiểm tra quyền Admin
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: ' . BASE_URL . 'routes/index.php?action=login');
            exit;
        }

        $id = $_GET['id'] ?? 0;
        $status = $_GET['status'] ?? '';

        // 2. Lấy tên admin từ Session
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

    // 2. [QUAN TRỌNG] Xử lý lưu booking với Transaction để chống Overbooking
    public function store() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') exit;

        // Nhận dữ liệu từ form
        $lichId = $_POST['lich_khoi_hanh_id'];
        $soLon = (int)$_POST['so_nguoi_lon'];
        $soTre = (int)($_POST['so_tre_em'] ?? 0);
        $tongKhach = $soLon + $soTre;

        // Khởi tạo các Model cần thiết
        $lkhModel = new LichKhoiHanhModel();
        $bookingModel = new BookingModel();
        $tourModel = new TourModel();

        try {
            // BẮT ĐẦU TRANSACTION (Khóa dữ liệu để xử lý an toàn)
            $bookingModel->conn->beginTransaction();

            // 1. Lấy thông tin lịch và KHÓA dòng này lại (ngăn người khác đặt cùng lúc)
            // Hàm getDetailForUpdate() đã được thêm ở bước trước trong LichKhoiHanhModel
            $lkh = $lkhModel->getDetailForUpdate($lichId);

            if (!$lkh) {
                throw new Exception("Lịch khởi hành không tồn tại!");
            }

            // 2. Kiểm tra lại chỗ trống (Dữ liệu $lkh lúc này là mới nhất và độc quyền)
            if (($lkh['so_cho_da_dat'] + $tongKhach) > $lkh['so_cho_toi_da']) {
                throw new Exception("Rất tiếc, vừa có người đặt và hiện tại tour không đủ chỗ trống!");
            }

            // 3. Tính tiền
            $tour = $tourModel->getDetail($lkh['tour_id']);
            $tongTien = ($soLon * $tour['gia_nguoi_lon']) + ($soTre * $tour['gia_tre_em']);

            // 4. Lưu Booking vào Database
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

            if (!$newId) {
                throw new Exception("Lỗi hệ thống khi lưu đơn hàng.");
            }

            // 5. Cập nhật số chỗ đã đặt (Cộng thêm vào)
            $lkhModel->updateSoCho($lichId, $tongKhach);

            // 6. Mọi thứ OK -> Commit (Lưu chính thức)
            $bookingModel->conn->commit();

            // Chuyển trang về danh sách
            header('Location: index.php?action=admin-bookings');

        } catch (Exception $e) {
            // Nếu có bất kỳ lỗi nào (hết chỗ, lỗi SQL...) -> Rollback (Hoàn tác)
            $bookingModel->conn->rollBack();
            
            // Thông báo lỗi cho người dùng và quay lại trang trước
            echo "<script>
                    alert('" . $e->getMessage() . "'); 
                    window.history.back();
                  </script>";
        }
    }

    public function detail() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') exit;

        $id = $_GET['id'] ?? 0;
        $bookingModel = new BookingModel();
        
        // 1. Lấy thông tin chi tiết đơn hàng
        $booking = $bookingModel->getDetail($id); 

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