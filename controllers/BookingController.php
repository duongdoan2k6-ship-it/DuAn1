<?php
class BookingController extends BaseController
{
    public $bookingModel;
    public function __construct()
    {
        $this->bookingModel = new BookingModel();
    }

    // 1. Danh sách booking
    public function index()
    {
        $bookingList = $this->bookingModel->getAll();
        $this->renderView('pages/booking/list.php', [
            'bookingList' => $bookingList
        ]);
    }

    // 2. Hiển thị form thêm mới
    public function add()
    {
        // Lấy danh sách lịch trình để đổ vào dropdown
        $listLich = $this->bookingModel->getAvailableSchedules();

        $this->renderView('pages/booking/add.php', [
            'listLich' => $listLich
        ]);
    }

    // 3. [MỚI] Xử lý lưu booking khi submit form thêm mới
    // Trong file controllers/BookingController.php

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'MaLichKhoiHanh' => $_POST['MaLichKhoiHanh'],
                'HoTen'          => $_POST['HoTen'],
                'SoDienThoai'    => $_POST['SoDienThoai'],
                'Email'          => $_POST['Email'],
                'DiaChi'         => $_POST['DiaChi'],
                'SoLuongNguoiLon' => (int)$_POST['SoLuongNguoiLon'],
                'SoLuongTreEm'   => (int)$_POST['SoLuongTreEm'],
                'GhiChu'         => $_POST['GhiChu']
            ];

            // Gọi Model để xử lý
            $result = $this->bookingModel->createBooking($data);

            if ($result === true) {
                $_SESSION['alert_message'] = "Thêm booking thành công!";
                $_SESSION['alert_type'] = "success";
                header("Location: index.php?action=list-booking");
            } elseif ($result === "full_slots") {
                $_SESSION['alert_message'] = "Lỗi: Lịch trình này không còn đủ chỗ!";
                $_SESSION['alert_type'] = "error";
                header("Location: index.php?action=add-booking");
            } else {
                // Nếu lỗi khác, nó sẽ hiện ra màn hình (do đoạn catch ở Model)
            }
            exit();
        }
    }

    // 4. Hủy booking (Nút xóa nhanh ngoài danh sách)
    public function cancel()
    {
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $MaDatTour = $_GET['id'];
            $result = $this->bookingModel->cancelBooking($MaDatTour);

            if ($result) {
                $_SESSION['alert_message'] = "Đã huỷ booking thành công!";
                $_SESSION['alert_type'] = "success";
            } else {
                $_SESSION['alert_message'] = "KHÔNG THỂ HUỶ: Chỉ được huỷ đơn 'Chờ xác nhận'!";
                $_SESSION['alert_type'] = "error";
            }
        }
        header("Location: index.php?action=list-booking");
        exit();
    }

    // 5. Xem chi tiết
    public function detail()
    {
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            header("Location: index.php?action=list-booking");
            exit();
        }

        $booking = $this->bookingModel->findById($_GET['id']);

        if (!$booking) {
            $_SESSION['alert_message'] = "Không tìm thấy thông tin!";
            $_SESSION['alert_type'] = "error";
            header("Location: index.php?action=list-booking");
            exit();
        }

        $this->renderView('pages/booking/detail.php', ['booking' => $booking]);
    }

    // 6. Hiển thị form sửa (đổi trạng thái)
    public function edit()
    {
        if (!isset($_GET['id'])) header("Location: index.php?action=list-booking");

        $id = $_GET['id'];
        $booking = $this->bookingModel->findById($id);

        $listTrangThai = [
            ['MaTrangThai' => 1, 'TenTrangThai' => 'Chờ xác nhận'],
            ['MaTrangThai' => 2, 'TenTrangThai' => 'Đã xác nhận'],
            ['MaTrangThai' => 3, 'TenTrangThai' => 'Đã hủy']
        ];

        $this->renderView('pages/booking/edit.php', [
            'booking' => $booking,
            'listTrangThai' => $listTrangThai
        ]);
    }

    // 7. Xử lý cập nhật trạng thái
    // Trong file controllers/BookingController.php

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];

            // 1. Check quyền sửa (chỉ cho sửa khi trạng thái là 1)
            $currentBooking = $this->bookingModel->findById($id);
            if ($currentBooking['MaTrangThai'] != 1) {
                $_SESSION['alert_message'] = "CẢNH BÁO: Đơn hàng này không thể chỉnh sửa nữa!";
                $_SESSION['alert_type'] = "error";
                header("Location: index.php?action=edit-booking&id=$id");
                exit();
            }

            // 2. Lấy ĐẦY ĐỦ dữ liệu từ form (Lần trước bạn thiếu đoạn này)
            $data = [
                // Thông tin cơ bản
                'TenKhachHang'    => $_POST['TenKhachHang'],
                'LienHeKhachHang' => $_POST['LienHeKhachHang'],
                'GhiChu'          => $_POST['GhiChu'],
                'MaTrangThai'     => $_POST['MaTrangThai'],

                // [QUAN TRỌNG] Các trường mới cần thêm vào để Model tính toán
                'SoLuongNguoiLon' => (int)$_POST['SoLuongNguoiLon'],
                'SoLuongTreEm'    => (int)$_POST['SoLuongTreEm'],
                'TongTien'        => $_POST['TongTien'],
                'MaLichKhoiHanh'  => $_POST['MaLichKhoiHanh'] // Để check chỗ trống bên bảng Lịch
            ];

            // 3. Gọi Model xử lý
            $result = $this->bookingModel->updateBooking($id, $data);

            if ($result === true) {
                $_SESSION['alert_message'] = "Cập nhật thành công!";
                $_SESSION['alert_type'] = "success";
                header("Location: index.php?action=list-booking");
            } elseif ($result === 'not_enough_seats') {
                $_SESSION['alert_message'] = "Lỗi: Không đủ chỗ trống để thêm người!";
                $_SESSION['alert_type'] = "error";
                header("Location: index.php?action=edit-booking&id=$id");
            } else {
                $_SESSION['alert_message'] = "Lỗi hệ thống (Vui lòng kiểm tra lại Model)!";
                $_SESSION['alert_type'] = "error";
                header("Location: index.php?action=edit-booking&id=$id");
            }
            exit();
        }
    }
}
