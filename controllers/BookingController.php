<?php
class BookingController extends BaseController {

    public function index() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: ' . BASE_URL . 'routes/index.php?action=login');
            exit;
        }

        $filters = [
            'keyword' => $_GET['keyword'] ?? '',
            'status'  => $_GET['status'] ?? '',
            'tour_id' => $_GET['tour_id'] ?? ''
        ];

        $bookingModel = new BookingModel();
        $bookings = $bookingModel->getAllBookings($filters);

        $lkhModel = new LichKhoiHanhModel();
        $tourList = $lkhModel->getAllToursList();

        $this->render('pages/admin/bookings', [
            'bookings' => $bookings,
            'tourList' => $tourList,
            'filters'  => $filters
        ]);
    }

    public function updateStatus() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: ' . BASE_URL . 'routes/index.php?action=login');
            exit;
        }

        $id = $_GET['id'] ?? 0;
        $status = $_GET['status'] ?? '';
        $adminName = $_SESSION['user']['ho_ten'] ?? 'Administrator'; 
        $validStatus = ['DaXacNhan', 'Huy'];

        if ($id && in_array($status, $validStatus)) {
            $bookingModel = new BookingModel();
            $note = "Cập nhật nhanh từ trang danh sách";
            
            $result = $bookingModel->updateStatusAndLog($id, $status, $adminName, $note);

            if ($result) {
                header('Location: ' . BASE_URL . 'routes/index.php?action=admin-bookings');
                exit;
            } else {
                echo "<script>
                        alert('KHÔNG THỂ CẬP NHẬT TRẠNG THÁI!\\n\\nNguyên nhân có thể:\\n1. Tour đã hết chỗ trống (khi bạn khôi phục vé Hủy).\\n2. Lỗi hệ thống.');
                        window.location.href = '" . BASE_URL . "routes/index.php?action=admin-bookings';
                      </script>";
                exit;
            }
        }
        header('Location: ' . BASE_URL . 'routes/index.php?action=admin-bookings');
    }

    public function create() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') exit;

        $lkhModel = new LichKhoiHanhModel();
        $schedules = $lkhModel->getOpenSchedules();

        $this->render('pages/admin/bookings/create', ['schedules' => $schedules]);
    }

    public function store() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') exit;

        // 1. Lấy dữ liệu từ Form
        $lichId = $_POST['lich_khoi_hanh_id'];
        $soLon = (int)$_POST['so_nguoi_lon'];
        $soTre = (int)($_POST['so_tre_em'] ?? 0);
        $tongKhach = $soLon + $soTre;
        
        // Mảng danh sách thành viên từ View
        $members = $_POST['members'] ?? []; 

        // Chuẩn bị dữ liệu Booking để lưu
        $bookingData = [
            'lich_khoi_hanh_id' => $lichId,
            'ten_nguoi_dat'     => $_POST['ten_nguoi_dat'],
            'sdt_lien_he'       => $_POST['sdt_lien_he'],
            'email_lien_he'     => $_POST['email_lien_he'],
            'so_nguoi_lon'      => $soLon,
            'so_tre_em'         => $soTre,
            'tong_tien'         => $_POST['tong_tien'],
            'ghi_chu'           => $_POST['ghi_chu'] ?? '',
            'trang_thai'        => 'DaXacNhan' 
        ];

        // 2. Khởi tạo Models
        $bookingModel = new BookingModel();
        
        // --- [QUAN TRỌNG: FIX LỖI TRANSACTION] ---
        // Khởi tạo các model khác nhưng GÁN KẾT NỐI của bookingModel sang
        // Điều này đảm bảo tất cả chạy trên cùng 1 Transaction
        $lkhModel = new LichKhoiHanhModel();
        $khachTourModel = new KhachTourModel();
        
        // Kiểm tra xem BaseModel có cho phép truy cập $conn không (thường là protected hoặc public)
        // Nếu $conn là public trong BaseModel thì dòng dưới hoạt động tốt.
        // Nếu $conn là protected, bạn cần sửa BaseModel thành public $conn;
        if (property_exists($bookingModel, 'conn')) {
            $lkhModel->conn = $bookingModel->conn;
            $khachTourModel->conn = $bookingModel->conn;
        }
        // ------------------------------------------

        try {
            // Bắt đầu Transaction
            $bookingModel->conn->beginTransaction();

            // A. Kiểm tra chỗ trống
            if (!$lkhModel->checkAvailability($lichId, $tongKhach)) {
                throw new Exception("Lịch khởi hành này không còn đủ chỗ trống cho $tongKhach người!");
            }

            // B. Lưu Booking và Lấy ID
            $bookingId = $bookingModel->createAndGetId($bookingData); 

            if (!$bookingId) throw new Exception("Lỗi hệ thống khi tạo đơn hàng (Không lấy được ID).");

            // C. Lưu Danh sách thành viên
            foreach ($members as $mem) {
                if (empty($mem['name'])) continue; // Bỏ qua dòng trống

                $khachData = [
                    'booking_id' => $bookingId,
                    'ho_ten_khach' => $mem['name'],
                    'loai_khach' => $mem['type'],
                    'gioi_tinh' => $mem['gender'],
                    'ngay_sinh' => !empty($mem['dob']) ? $mem['dob'] : null,
                    'ghi_chu_dac_biet' => $mem['note'] ?? '',
                    'trang_thai_diem_danh' => 0
                ];
                
                $khachTourModel->insert($khachData);
            }

            // D. Cập nhật số chỗ đã đặt
            $lkhModel->updateBookedSeats($lichId, $tongKhach);

            // Hoàn tất
            $bookingModel->conn->commit();
            
            // Chuyển hướng thành công
            echo "<script>
                alert('Tạo booking thành công!');
                window.location.href = 'index.php?action=admin-bookings';
            </script>";
            exit;

        } catch (Exception $e) {
            // Nếu có lỗi thì Rollback (Hoàn tác)
            $bookingModel->conn->rollBack();
            echo "<script>
                    alert('LỖI: " . addslashes($e->getMessage()) . "'); 
                    window.history.back();
                  </script>";
        }
    }

    public function detail() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') exit;

        $id = $_GET['id'] ?? 0;
        $bookingModel = new BookingModel();
        
        $booking = $bookingModel->getDetail($id); 
        $history = $bookingModel->getHistory($id);
        
        $guests = $bookingModel->getGuests($id); 

        if (!$booking) {
            echo "Không tìm thấy đơn hàng!";
            exit;
        }

        $this->render('pages/admin/bookings/detail', [
            'booking' => $booking,
            'history' => $history,
            'guests'  => $guests 
        ]);
    }
}
?>