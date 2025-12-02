<?php

class BaocaoController extends BaseController
{
    private $ModelBaocao;

    public function __construct()
    {
        $this->ModelBaocao = new BaocaoModel();
    }

    // =====================================================
    // DASHBOARD + DANH SÁCH
    // =====================================================
    public function index()
    {
        // Lọc dữ liệu GET
        $fromDateRaw       = $_GET['from_date'] ?? '';
        $toDateRaw         = $_GET['to_date'] ?? '';
        $maLichKhoiHanhRaw = $_GET['MaLichKhoiHanh'] ?? '';

        $fromDate = $fromDateRaw ? $fromDateRaw . ' 00:00:00' : null;
        $toDate   = $toDateRaw   ? $toDateRaw   . ' 23:59:59' : null;
        $maLichKhoiHanh = $maLichKhoiHanhRaw !== '' ? $maLichKhoiHanhRaw : null;

        // Danh sách báo cáo
        $Baocao = $this->ModelBaocao->getBaocaoWithFilter($fromDate, $toDate, $maLichKhoiHanh);

        // Tổng hợp
        $summary = $this->ModelBaocao->getSummary($fromDate, $toDate, $maLichKhoiHanh);

        // Dữ liệu biểu đồ theo lịch khởi hành
        $chartData = $this->ModelBaocao->getChartDataByDeparture($fromDate, $toDate, $maLichKhoiHanh);

        // Dữ liệu biểu đồ theo ngày
        $chartByDateData = $this->ModelBaocao->getChartDataByDate($fromDate, $toDate, $maLichKhoiHanh);

        $this->renderView('pages/baocao/baocao.php', [
            'Baocao'          => $Baocao,
            'summary'         => $summary,
            'chartData'       => $chartData,
            'chartByDateData' => $chartByDateData,
            'filters' => [
                'from_date'      => $fromDateRaw,
                'to_date'        => $toDateRaw,
                'MaLichKhoiHanh' => $maLichKhoiHanhRaw,
            ],
            'pageTitle' => 'Quản lý Báo cáo tài chính'
        ]);
    }

    // =====================================================
    // CREATE
    // =====================================================
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $doanhThu = (float)($_POST['DoanhThu'] ?? 0);
            $chiPhi   = (float)($_POST['ChiPhi'] ?? 0);

            if ($doanhThu < 0) $doanhThu = 0;
            if ($chiPhi   < 0) $chiPhi   = 0;

            $loiNhuan = $doanhThu - $chiPhi;

            $ngayTaoRaw   = $_POST['NgayTaoBaoCao'] ?? '';
            $ngayTaoChuan = str_replace('T', ' ', $ngayTaoRaw);

            $data = [
                'MaLichKhoiHanh' => $_POST['MaLichKhoiHanh'] ?? null,
                'DoanhThu'       => $doanhThu,
                'ChiPhi'         => $chiPhi,
                'LoiNhuan'       => $loiNhuan,
                'NgayTaoBaoCao'  => $ngayTaoChuan
            ];

            if ($this->ModelBaocao->insertBaocao($data)) {
                header("Location: index.php?action=list-baocao");
                exit;
            }

            $this->renderView('pages/baocao/add.php', [
'pageTitle' => 'Thêm mới Báo cáo',
                'error'     => 'Thêm báo cáo thất bại. Vui lòng thử lại.'
            ]);
            return;
        }

        $this->renderView('pages/baocao/add.php', [
            'pageTitle' => 'Thêm mới Báo cáo'
        ]);
    }

    // =====================================================
    // DELETE
    // =====================================================
    public function delete()
    {
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $this->ModelBaocao->deleteBaocao($id);
        }
        header("Location: index.php?action=list-baocao");
        exit;
    }

    // =====================================================
    // EDIT FORM
    // =====================================================
    public function edit()
    {
        if (isset($_GET['id'])) {
            $id     = (int)$_GET['id'];
            $Baocao = $this->ModelBaocao->getBaocaoById($id);

            $this->renderView('pages/baocao/edit.php', [
                'Baocao'    => $Baocao,
                'pageTitle' => 'Cập nhật Báo cáo'
            ]);
        } else {
            header("Location: index.php?action=list-baocao");
            exit;
        }
    }

    // =====================================================
    // UPDATE
    // =====================================================
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $id       = (int)($_POST['BaoCaoID'] ?? 0);
            $doanhThu = (float)($_POST['DoanhThu'] ?? 0);
            $chiPhi   = (float)($_POST['ChiPhi'] ?? 0);

            if ($doanhThu < 0) $doanhThu = 0;
            if ($chiPhi   < 0) $chiPhi   = 0;

            $loiNhuan = $doanhThu - $chiPhi;

            $ngayTaoRaw   = $_POST['NgayTaoBaoCao'] ?? '';
            $ngayTaoChuan = str_replace('T', ' ', $ngayTaoRaw);

            $data = [
                'MaLichKhoiHanh' => $_POST['MaLichKhoiHanh'] ?? null,
                'DoanhThu'       => $doanhThu,
                'ChiPhi'         => $chiPhi,
                'LoiNhuan'       => $loiNhuan,
                'NgayTaoBaoCao'  => $ngayTaoChuan
            ];

            $this->ModelBaocao->updateBaocao($id, $data);
        }

        header("Location: index.php?action=list-baocao");
        exit;
    }

    // =====================================================
    // DETAIL VIEW
    // =====================================================
    public function detail()
    {
        if (!isset($_GET['id'])) {
            header("Location: index.php?action=list-baocao");
            exit;
        }

        $id     = (int)$_GET['id'];
        $Baocao = $this->ModelBaocao->getBaocaoById($id);

        if (!$Baocao) {
            header("Location: index.php?action=list-baocao");
            exit;
        }

        $this->renderView('pages/baocao/baocao_detail.php', [
            'Baocao'    => $Baocao,
'pageTitle' => 'Chi tiết Báo cáo tài chính'
        ]);
    }

    // =====================================================
    // EXPORT CSV (Excel)
    // =====================================================
    public function exportCsv()
    {
        $fromDateRaw       = $_GET['from_date'] ?? '';
        $toDateRaw         = $_GET['to_date'] ?? '';
        $maLichKhoiHanhRaw = $_GET['MaLichKhoiHanh'] ?? '';

        $fromDate = $fromDateRaw ? $fromDateRaw . ' 00:00:00' : null;
        $toDate   = $toDateRaw   ? $toDateRaw   . ' 23:59:59' : null;
        $maLichKhoiHanh = $maLichKhoiHanhRaw !== '' ? $maLichKhoiHanhRaw : null;

        $rows = $this->ModelBaocao->getBaocaoWithFilter($fromDate, $toDate, $maLichKhoiHanh);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="bao_cao_tai_chinh.csv"');

        $output = fopen('php://output', 'w');

        fputcsv($output, [
            'BaoCaoID',
            'MaLichKhoiHanh',
            'DoanhThu',
            'ChiPhi',
            'LoiNhuan',
            'NgayTaoBaoCao'
        ]);

        foreach ($rows as $row) {
            fputcsv($output, [
                $row['BaoCaoID'],
                $row['MaLichKhoiHanh'],
                $row['DoanhThu'],
                $row['ChiPhi'],
                $row['LoiNhuan'],
                $row['NgayTaoBaoCao'],
            ]);
        }

        fclose($output);
        exit;
    }
}

?>