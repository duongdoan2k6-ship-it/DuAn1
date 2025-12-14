<?php
class HdvController extends BaseController
{
    private $nhatKyModel;
    private $diemDanhModel;

    public function __construct(){
        $this->nhatKyModel = new NhatKyTourModel();
        $this->diemDanhModel = new DiemDanhModel();
    }
    public function index(){
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'hdv') {
            header('Location: ' . BASE_URL . 'routes/index.php?action=login');
            exit;
        }

        $hdvId = $_SESSION['user']['id'];
        $lichModel = new LichKhoiHanhModel();
        $myTours = $lichModel->getToursByHdv($hdvId);

        $this->render('pages/hdv/dashboard', ['myTours' => $myTours]);
    }
    public function detail(){
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'hdv') {
            header('Location: ' . BASE_URL . 'routes/index.php?action=login');
            exit;
        }

        $lichId = $_GET['id'] ?? 0;
        $lichModel = new LichKhoiHanhModel();
        $khachModel = new KhachTourModel();

        $tourInfo = $lichModel->getDetailForHdv($lichId);

        if (!$tourInfo) {
            die("Không tìm thấy thông tin chuyến đi!");
        }
        
        $passengers = $khachModel->getPassengersByTour($lichId);
        $itineraries = $lichModel->getTourItinerary($tourInfo['tour_id']);
        $nhatKyList = $this->nhatKyModel->getLogsByLichId($lichId);
        $phienDiemDanhList = $this->diemDanhModel->getPhienByLich($lichId);

        $now = time();
        $start = strtotime($tourInfo['ngay_khoi_hanh']);
        $end = strtotime($tourInfo['ngay_ket_thuc']);

        $isEditable = ($now >= $start && $now <= $end);

        $this->render('pages/hdv/tour_detail', [
            'tourInfo'          => $tourInfo,
            'passengers'        => $passengers,
            'itineraries'       => $itineraries,
            'nhatKyList'        => $nhatKyList,
            'phienDiemDanhList' => $phienDiemDanhList, 
            'lichId'            => $lichId,
            'isEditable'            => $isEditable
        ]);
    }
    public function checkIn(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $lichId = $_POST['lich_id'];
            $attendanceData = $_POST['attendance'] ?? [];

            $khachModel = new KhachTourModel();
            $khachModel->resetStatus($lichId);

            foreach ($attendanceData as $idKhach => $value) {
                $khachModel->updateStatus($idKhach, 1);
            }

            header('Location: ' . BASE_URL . 'routes/index.php?action=hdv-tour-detail&id=' . $lichId . '&status=success');
        }
    }
    public function addNhatKy(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $lichId = $_POST['lich_khoi_hanh_id'];

            // --- [SỬA ĐOẠN NÀY] Tự động tạo tiêu đề nếu để trống ---
            $tieuDe = trim($_POST['tieu_de'] ?? '');
            if (empty($tieuDe)) {
                date_default_timezone_set('Asia/Ho_Chi_Minh'); // Đặt múi giờ VN
                $tieuDe = 'Nhật ký ' . date('H:i d/m/Y');
            }
            // -------------------------------------------------------

            $data = [
                'lich_khoi_hanh_id' => $lichId,
                'tieu_de'           => $tieuDe, // Sử dụng biến $tieuDe đã xử lý ở trên
                'noi_dung'          => $_POST['noi_dung'] ?? '',
                'su_co'             => $_POST['su_co'] ?? '',
                'phan_hoi_khach'    => $_POST['phan_hoi_khach'] ?? '',
                'hinh_anh'          => ''
            ];

            // Xử lý upload ảnh
            if (isset($_FILES['hinh_anh']) && $_FILES['hinh_anh']['error'] == 0) {
                $uploadDir = '../public/assets/uploads/nhat_ky/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileName = time() . '_' . basename($_FILES['hinh_anh']['name']);
                $targetFile = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['hinh_anh']['tmp_name'], $targetFile)) {
                    $data['hinh_anh'] = 'assets/uploads/nhat_ky/' . $fileName;
                }
            }

            if ($this->nhatKyModel->addNhatKy($data)) {
                $status = 'log_success';
            } else {
                $status = 'log_error';
            }

            // Thêm #diary-tab-pane để khi reload trang sẽ tự động mở tab Nhật ký
            header('Location: ' . BASE_URL . 'routes/index.php?action=hdv-tour-detail&id=' . $lichId . '&status=' . $status . '#diary-tab-pane');
            exit;
        }
    }
    public function editNhatKy(){
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'hdv') {
            die('Không có quyền truy cập');
        }

        $id = $_GET['id'] ?? 0;
        $log = $this->nhatKyModel->getLogById($id);

        if (!$log) {
            die('Nhật ký không tồn tại');
        }

        $this->render('pages/hdv/edit_nhat_ky', ['log' => $log]);
    }
    public function updateNhatKy(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $lichId = $_POST['lich_khoi_hanh_id'];

            $data = [
                'tieu_de'           => $_POST['tieu_de'] ?? '',
                'noi_dung'          => $_POST['noi_dung'] ?? '',
                'su_co'             => $_POST['su_co'] ?? '',
                'phan_hoi_khach'    => $_POST['phan_hoi_khach'] ?? '',
                'hinh_anh'          => ''
            ];

            if (isset($_FILES['hinh_anh']) && $_FILES['hinh_anh']['error'] == 0) {
                $uploadDir = '../public/assets/uploads/nhat_ky/';
                if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

                $fileName = time() . '_' . basename($_FILES['hinh_anh']['name']);
                $targetFile = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['hinh_anh']['tmp_name'], $targetFile)) {
                    $data['hinh_anh'] = 'assets/uploads/nhat_ky/' . $fileName;

                    $oldLog = $this->nhatKyModel->getLogById($id);
                    if ($oldLog && !empty($oldLog['hinh_anh'])) {
                        $oldPath = '../public/' . $oldLog['hinh_anh'];
                        if (file_exists($oldPath)) unlink($oldPath);
                    }
                }
            }

            $this->nhatKyModel->updateNhatKy($id, $data);
            header('Location: ' . BASE_URL . 'routes/index.php?action=hdv-tour-detail&id=' . $lichId . '&status=update_success');
            exit;
        }
    }
    public function deleteNhatKy(){
        $id = $_GET['id'] ?? 0;
        $lichId = $_GET['lich_id'] ?? 0;

        $log = $this->nhatKyModel->getLogById($id);

        if ($log) {
            if (!empty($log['hinh_anh'])) {
                $filePath = '../public/' . $log['hinh_anh'];
                if (file_exists($filePath)) unlink($filePath);
            }
            $this->nhatKyModel->deleteNhatKy($id);
        }

        header('Location: ' . BASE_URL . 'routes/index.php?action=hdv-tour-detail&id=' . $lichId . '&status=delete_success');
        exit;
    }
    public function createPhienDiemDanh(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $lichId = $_POST['lich_khoi_hanh_id'];
            $tieuDe = trim($_POST['tieu_de'] ?? '');

            if (empty($tieuDe)) {
                $status = 'phien_error';
            } else {
                $newId = $this->diemDanhModel->createPhien($lichId, $tieuDe);
                if ($newId) {
                    header('Location: ' . BASE_URL . 'routes/index.php?action=hdv-view-diem-danh&lich_id=' . $lichId . '&phien_id=' . $newId . '&status=phien_created');
                    exit;
                } else {
                    $status = 'phien_error';
                }
            }
            header('Location: ' . BASE_URL . 'routes/index.php?action=hdv-tour-detail&id=' . $lichId . '&status=' . $status);
            exit;
        }
    }
    public function viewDiemDanh(){
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'hdv') {
            header('Location: ' . BASE_URL . 'routes/index.php?action=login');
            exit;
        }

        $lichId = $_GET['lich_id'] ?? 0;
        $phienId = $_GET['phien_id'] ?? 0;

        if ($lichId == 0 || $phienId == 0) {
            die("Thiếu thông tin chuyến đi hoặc phiên điểm danh.");
        }

        // Lấy danh sách khách và trạng thái điểm danh chi tiết
        $passengers = $this->diemDanhModel->getChiTietPhien($phienId, $lichId);

        
        $phienInfo = $this->diemDanhModel->getPhienById($phienId);

        $lichModel = new LichKhoiHanhModel();
        $tourInfo = $lichModel->getDetailForHdv($lichId);

        $this->render('pages/hdv/diem_danh_detail', [
            'tourInfo'   => $tourInfo,
            'phienInfo'  => $phienInfo,
            'passengers' => $passengers,
            'lichId'     => $lichId,
            'phienId'    => $phienId
        ]);
    }
    public function saveDiemDanh(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $lichId = $_POST['lich_id'];
            $phienId = $_POST['phien_id'];
            $attendanceData = $_POST['attendance'] ?? [];

            $phienInfo = $this->diemDanhModel->getPhienById($phienId);
            if ($phienInfo && $phienInfo['trang_thai_khoa'] == 1) {
                header('Location: ' . BASE_URL . 'routes/index.php?action=hdv-view-diem-danh&lich_id=' . $lichId . '&phien_id=' . $phienId . '&status=locked_error');
                exit;
            }

            $success = true;
            foreach ($attendanceData as $khachId => $data) {
                $trangThai = $data['status'] ?? 0;
                $ghiChu = $data['note'] ?? null;

                if (!$this->diemDanhModel->saveChiTiet($phienId, $khachId, $trangThai, $ghiChu)) {
                    $success = false;
                }
            }
            if ($success) {
                $this->diemDanhModel->lockPhien($phienId);
            }

            $status = $success ? 'dd_saved' : 'dd_error';
            header('Location: ' . BASE_URL . 'routes/index.php?action=hdv-view-diem-danh&lich_id=' . $lichId . '&phien_id=' . $phienId . '&status=' . $status);
            exit;
        }
    }
    public function deletePhienDiemDanh(){
        $phienId = $_GET['phien_id'] ?? 0;
        $lichId = $_GET['lich_id'] ?? 0;

        $this->diemDanhModel->deletePhien($phienId);

        header('Location: ' . BASE_URL . 'routes/index.php?action=hdv-tour-detail&id=' . $lichId . '&status=phien_deleted');
        exit;
    }
    public function updateYeuCauDacBiet(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $lichId = $_POST['lich_id'] ?? 0;
            $khachId = $_POST['khach_id'] ?? 0;
            $ghiChu = trim($_POST['ghi_chu'] ?? '');
            if ($khachId == 0 || $lichId == 0) {
                header('Location: ' . BASE_URL . 'routes/index.php?action=hdv-tour-detail&id=' . $lichId . '&status=note_error_param');
                exit;
            }

            $khachModel = new KhachTourModel();
            if ($khachModel->updateGhiChuDacBiet($khachId, $ghiChu)) {
                $status = 'note_success';
            } else {
                $status = 'note_error';
            }
            header('Location: ' . BASE_URL . 'routes/index.php?action=hdv-tour-detail&id=' . $lichId . '&status=' . $status . '#v-pills-passengers-tab');
            exit;
        }
    }
}
?>