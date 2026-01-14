<?php
class StatisticalModel extends BaseModel {
    
    // 1. Thống kê tổng quát (Tổng doanh thu, chi phí toàn hệ thống)
    // Hàm này đang bị thiếu, gây ra lỗi Fatal Error
    public function getOverallStats($fromDate, $toDate) {
        $sql = "
            SELECT 
                -- Tổng doanh thu từ các đơn đã thanh toán
                (SELECT COALESCE(SUM(tong_tien), 0) 
                 FROM bookings b
                 JOIN lich_khoi_hanh lkh ON b.lich_khoi_hanh_id = lkh.id
                 WHERE b.trang_thai = 'DaXacNhan'
                 AND lkh.ngay_khoi_hanh BETWEEN :fromDate1 AND :toDate1
                ) as tong_doanh_thu,

                -- Tổng chi phí từ các dịch vụ đã đặt (không hủy)
                (SELECT COALESCE(SUM(thanh_tien), 0) 
                 FROM lich_dich_vu ldv 
                 JOIN lich_khoi_hanh lkh ON ldv.lich_khoi_hanh_id = lkh.id
                 WHERE ldv.trang_thai != 'Huy' 
                 AND lkh.ngay_khoi_hanh BETWEEN :fromDate2 AND :toDate2
                ) as tong_chi_phi
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            'fromDate1' => $fromDate, 'toDate1' => $toDate,
            'fromDate2' => $fromDate, 'toDate2' => $toDate
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Ép kiểu về số (float/int) để tránh lỗi null
        $doanhThu = $result['tong_doanh_thu'] ?? 0;
        $chiPhi   = $result['tong_chi_phi'] ?? 0;
        
        return [
            'doanh_thu' => $doanhThu,
            'chi_phi'   => $chiPhi,
            'loi_nhuan' => $doanhThu - $chiPhi
        ];
    }

    // 2. Thống kê chi tiết theo từng Tour
    public function getRevenueByTour($fromDate, $toDate) {
        $sql = "
            SELECT 
                t.id as tour_id,
                t.ten_tour,
                -- Đếm số chuyến đi
                COUNT(DISTINCT lkh.id) as so_chuyen_di,
                
                -- Tính doanh thu (Subquery để tránh nhân đôi khi join)
                COALESCE((
                    SELECT SUM(b.tong_tien)
                    FROM bookings b
                    WHERE b.lich_khoi_hanh_id = lkh.id
                    AND b.trang_thai = 'DaXacNhan'
                ), 0) as doanh_thu_tour,
                
                -- Tính chi phí
                COALESCE((
                    SELECT SUM(dv.thanh_tien)
                    FROM lich_dich_vu dv
                    WHERE dv.lich_khoi_hanh_id = lkh.id
                    AND dv.trang_thai != 'Huy'
                ), 0) as chi_phi_tour

            FROM tours t
            JOIN lich_khoi_hanh lkh ON t.id = lkh.tour_id
            WHERE t.is_active = 1
            AND lkh.ngay_khoi_hanh BETWEEN :fromDate AND :toDate
            
            -- Group theo từng chuyến trước để tính toán chính xác
            GROUP BY lkh.id
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            'fromDate' => $fromDate,
            'toDate' => $toDate
        ]);
        
        $rawResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Tổng hợp lại theo Tour (Vì 1 tour có thể có nhiều chuyến đi trong tháng)
        $finalResults = [];
        foreach ($rawResults as $row) {
            $tourId = $row['tour_id'];
            if (!isset($finalResults[$tourId])) {
                $finalResults[$tourId] = [
                    'tour_id' => $row['tour_id'],
                    'ten_tour' => $row['ten_tour'],
                    'so_chuyen_di' => 0,
                    'doanh_thu' => 0,
                    'chi_phi' => 0,
                    'loi_nhuan' => 0
                ];
            }
            
            $finalResults[$tourId]['so_chuyen_di']++;
            $finalResults[$tourId]['doanh_thu'] += $row['doanh_thu_tour'];
            $finalResults[$tourId]['chi_phi'] += $row['chi_phi_tour'];
            $finalResults[$tourId]['loi_nhuan'] += ($row['doanh_thu_tour'] - $row['chi_phi_tour']);
        }

        // Sắp xếp theo lợi nhuận giảm dần
        usort($finalResults, function($a, $b) {
            return $b['loi_nhuan'] <=> $a['loi_nhuan'];
        });

        return $finalResults;
    }
}
?>