<?php
class StatisticalController extends BaseController
{
    private $statisticalModel;

    public function __construct()
    {
        // Khởi tạo Model
        $this->statisticalModel = new StatisticalModel();
    }

    public function index()
    {
        // 1. Xử lý thời gian lọc:
        // Nếu không có tham số trên URL, mặc định lấy ngày đầu tháng đến cuối tháng hiện tại
        $fromDate = $_GET['from_date'] ?? date('Y-m-01');
        $toDate   = $_GET['to_date']   ?? date('Y-m-t');

        // 2. Gọi Model để lấy dữ liệu:
        // Lấy tổng quan (Doanh thu tổng, chi phí tổng...)
        $overallStats = $this->statisticalModel->getOverallStats($fromDate, $toDate);

        // Lấy chi tiết từng tour (Doanh thu, chi phí, lợi nhuận từng tour)
        $tourStats = $this->statisticalModel->getRevenueByTour($fromDate, $toDate);

        // 3. Gửi dữ liệu ra View (chúng ta sẽ tạo view này ở Bước 3)
        $this->render('pages/admin/statistics/index', [
            'fromDate'     => $fromDate,
            'toDate'       => $toDate,
            'overallStats' => $overallStats,
            'tourStats'    => $tourStats
        ]);
    }
}
