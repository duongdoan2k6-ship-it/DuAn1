<?php
class BookingController extends BaseController
{
    public function index()
    {
        $model = new BookingModel();
        $dsBooking = $model->getAllBooking();

        $data = [
            'bookingList' => $dsBooking,
            'pageTitle'   => 'Quản lý Booking'
        ];

        $this->renderView('pages/booking/list_booking.php', $data);
    }

    public function add()
    {
        $bookingModel = new BookingModel();
        $tourModel = new TourModel();

        $dsTour = $tourModel->getAllTours();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $maLich = $_POST['ma_lich'];
            $tenKH  = $_POST['ten_kh'];
            $lienHe = $_POST['lien_he'];
            $soKhach = $_POST['so_khach'];
            $tongTien = $_POST['tong_tien'];
            $ghiChu = $_POST['ghi_chu'];

            $ok = $bookingModel->insert($maLich, $tenKH, $lienHe, $soKhach, $tongTien, $ghiChu);
            if ($ok) {
                header("Location: index.php?action=list-booking");
                exit;
            }
        }

        $data = [
            'dsTour' => $dsTour,
            'pageTitle' => 'Tạo Booking'
        ];

        $this->renderView('pages/booking/add_booking.php', $data);
    }

    public function editStatus()
    {
        $id = $_GET['id'] ?? 0;

        $model = new BookingModel();
        $booking = $model->getBookingById($id);
        $statusList = $model->getAllStatus();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newStatus = $_POST['trang_thai'];
            $oldStatus = $booking['MaTrangThai'];
            $ghiChu    = $_POST['ghi_chu'] ?? '';
            $nhanVien  = "Admin";

            $model->updateStatus($id, $newStatus);
            $model->addStatusLog($id, $oldStatus, $newStatus, $nhanVien, $ghiChu);

            header("Location: index.php?action=list-booking");
            exit;
        }

        $data = [
            'booking' => $booking,
            'statusList' => $statusList,
            'pageTitle' => "Cập nhật trạng thái"
        ];

        $this->renderView('pages/booking/edit_status.php', $data);
    }

    public function detail()
    {
        $id = $_GET['id'] ?? 0;
        $model = new BookingModel();

        $booking = $model->getBookingDetail($id);
        $logs = $model->getStatusLog($id);

        $data = [
            'booking' => $booking,
            'logs' => $logs,
            'pageTitle' => "Chi tiết Booking"
        ];

        $this->renderView('pages/booking/detail_booking.php', $data);
    }

    public function delete()
{
    $id = $_GET['id'] ?? 0;

    $model = new BookingModel();
    $ok = $model->delete($id);

    if (!$ok) {
        echo "<script>
                alert('Không thể xóa booking đã xác nhận!');
                window.location.href = 'index.php?action=list-booking';
              </script>";
        exit;
    }

    echo "<script>
            alert('Xóa booking thành công!');
            window.location.href = 'index.php?action=list-booking';
          </script>";
    exit;
}

}
