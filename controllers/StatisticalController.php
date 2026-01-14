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
        // 1. Xử lý thời gian lọc
        $fromDate = $_GET['from_date'] ?? date('Y-m-01');
        $toDate   = $_GET['to_date']   ?? date('Y-m-t');

        // 2. Gọi Model lấy dữ liệu
        $overallStats = $this->statisticalModel->getOverallStats($fromDate, $toDate);
        $tourStats    = $this->statisticalModel->getRevenueByTour($fromDate, $toDate);

        // --- [QUAN TRỌNG] Xử lý dữ liệu phòng trường hợp null ---
        if (!$overallStats) {
            $overallStats = ['doanh_thu' => 0, 'chi_phi' => 0, 'loi_nhuan' => 0];
        }

        // --- [QUAN TRỌNG] Chuẩn bị dữ liệu cho Biểu đồ (View cần cái này) ---
        $chartLabels = [];
        $chartRevenue = [];
        $chartProfit = [];

        if (!empty($tourStats)) {
            foreach ($tourStats as $tour) {
                // Chỉ lấy top 10 tour để biểu đồ không bị rối
                if (count($chartLabels) < 10) {
                    $chartLabels[] = $tour['ten_tour'];
                    $chartRevenue[] = (float)$tour['doanh_thu'];
                    $chartProfit[]  = (float)$tour['loi_nhuan'];
                }
            }
        }
        
        // Đóng gói dữ liệu biểu đồ
        $chartData = [
            'labels'  => json_encode($chartLabels),
            'revenue' => json_encode($chartRevenue),
            'profit'  => json_encode($chartProfit)
        ];

        // 3. Gửi dữ liệu ra View
        $this->render('pages/admin/statistics/index', [
            'fromDate'     => $fromDate,
            'toDate'       => $toDate,
            'overallStats' => $overallStats, // Biến này sẽ hết lỗi
            'tourStats'    => $tourStats,
            'chartData'    => $chartData     // Biến này phục vụ biểu đồ
        ]);
    }
}
?>