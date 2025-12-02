<?php
class BookingController extends BaseController 
{
    private $bookingModel;

    public function __construct()
    {
        $this->bookingModel = new BookingModel();
    }

    public function index()
    {
        // 1. Lấy dữ liệu
        $bookings = $this->bookingModel->getAll();

        // 2. Gọi view
        // Sửa lỗi trong ảnh của bạn: Dùng renderView thay vì view
        $this->renderView('pages/booking/list_booking.php', [
            'bookingList' => $bookings
        ]);
    }
    
    // ... các hàm add, edit khác
}