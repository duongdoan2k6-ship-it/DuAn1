<?php
class BookingController extends BaseController
{
    public $bookingModel;
    public function __construct()
    {
        $this->bookingModel = new BookingModel();
    }

    public function index()
    {
        $bookingList = $this->bookingModel->getAll();

        $this->renderView('pages/booking/list.php', [
            'bookingList' => $bookingList
        ]);
    }

    public function cancel()
    {
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $MaDatTour = $_GET['id'];

            $result = $this->bookingModel->cancelBooking($MaDatTour);

            if ($result) {
                $_SESSION['alert_message'] = "Đã huỷ booking thành công!";
                $_SESSION['alert_type'] = "success";
            } else {
                $_SESSION['alert_message'] = "KHÔNG THỂ HUỶ: Chỉ được huỷ các đơn đang ở trạng thái 'Chờ xác nhận'!";
                $_SESSION['alert_type'] = "error";
            }
        }
        header("Location: index.php?action=list-booking");
        exit();
    }

    public function detail()
    {
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            header("Location: index.php?action=list-booking");
            exit();
        }

        $id = $_GET['id'];
        $booking = $this->bookingModel->findById($id);
        if (!$booking) {
            $_SESSION['alert_message'] = "Không tìm thấy thông tin booking!";
            $_SESSION['alert_type'] = "error";
            header("Location: index.php?action=list-booking");
            exit();
        }

        $this->renderView('pages/booking/detail.php', [
            'booking' => $booking
        ]);
    }
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

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            $status = $_POST['ma_trang_thai'];

            $result = $this->bookingModel->updateStatus($id, $status);

            if ($result) {
                $_SESSION['alert_message'] = "Cập nhật trạng thái thành công!";
                $_SESSION['alert_type'] = "success";
                header("Location: index.php?action=list-booking");
            } else {
                $_SESSION['alert_message'] = "Cập nhật thất bại!";
                $_SESSION['alert_type'] = "error";
header("Location: index.php?action=edit-booking&id=$id");
            }
            exit();
        }
    }

    public function add()
    { 
        $this -> renderView('pages/booking/add.php', []);
    }
}