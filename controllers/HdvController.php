<?php
class HdvController extends BaseController
{
    private $nhatKyModel;
    private $diemDanhModel;

    public function __construct()
    {
        $this->nhatKyModel = new NhatKyTourModel();
        $this->diemDanhModel = new DiemDanhModel();
    }

    // Trang danh sách tour (Dashboard)
    public function index()
    {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'hdv') {
            header('Location: ' . BASE_URL . 'routes/index.php?action=login');
            exit;
        }

        $hdvId = $_SESSION['user']['id'];
        $lichModel = new LichKhoiHanhModel();
        $myTours = $lichModel->getToursByHdv($hdvId);

        $this->render('pages/hdv/dashboard', ['myTours' => $myTours]);
    }

    // Xem chi tiết tour: Khách, Lịch trình, Nhật ký, Phiên điểm danh
    public function detail()
    {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'hdv') {
            header('Location: ' . BASE_URL . 'routes/index.php?action=login');
            exit;
        }

        $lichId = $_GET['id'] ?? 0;
        $lichModel = new LichKhoiHanhModel();
        $khachModel = new KhachTourModel();

        // 1. Lấy thông tin chi tiết chuyến đi
        $tourInfo = $lichModel->getDetailForHdv($lichId);

        if (!$tourInfo) {
            die("Không tìm thấy thông tin chuyến đi!");
        }

        // 2. Lấy danh sách khách hàng
        $passengers = $khachModel->getPassengersByTour($lichId);

        // 3. Lấy lịch trình chi tiết (dự kiến)
        $itineraries = $lichModel->getTourItinerary($tourInfo['tour_id']);

        // 4. Lấy danh sách nhật ký tour (thực tế)
        $nhatKyList = $this->nhatKyModel->getLogsByLichId($lichId);

        // 5. [MỚI] Lấy danh sách các phiên điểm danh
        $phienDiemDanhList = $this->diemDanhModel->getPhienByLich($lichId);

        // Truyền tất cả sang view
        $this->render('pages/hdv/tour_detail', [
            'tourInfo'          => $tourInfo,
            'passengers'        => $passengers,
            'itineraries'       => $itineraries,
            'nhatKyList'        => $nhatKyList,
            'phienDiemDanhList' => $phienDiemDanhList, // Truyền biến này sang view
            'lichId'            => $lichId
        ]);
    }

    // Xử lý lưu điểm danh nhanh (Logic cũ - Giữ lại để tương thích)
    public function checkIn()
    {
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

    // ====================================================================
    // CÁC HÀM XỬ LÝ NHẬT KÝ
    // ====================================================================

    // Xử lý thêm nhật ký tour
    public function addNhatKy()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $lichId = $_POST['lich_khoi_hanh_id'];

            $data = [
                'lich_khoi_hanh_id' => $lichId,
                'tieu_de'           => $_POST['tieu_de'] ?? '',
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

            header('Location: ' . BASE_URL . 'routes/index.php?action=hdv-tour-detail&id=' . $lichId . '&status=' . $status);
            exit;
        }
    }

    // Hiển thị form sửa nhật ký
    public function editNhatKy()
    {
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

    // Xử lý cập nhật nhật ký
    public function updateNhatKy()
    {
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

    // Xử lý xóa nhật ký
    public function deleteNhatKy()
    {
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

    // ====================================================================
    // CÁC HÀM XỬ LÝ ĐIỂM DANH CHI TIẾT (MỚI)
    // ====================================================================

    // 1. Tạo Phiên điểm danh mới
    public function createPhienDiemDanh()
    {
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

    // 2. Xem chi tiết 1 phiên điểm danh (và hiển thị form điểm danh)
    public function viewDiemDanh()
    {
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

    // 3. Xử lý lưu trạng thái điểm danh chi tiết
    public function saveDiemDanh()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $lichId = $_POST['lich_id'];
            $phienId = $_POST['phien_id'];
            $attendanceData = $_POST['attendance'] ?? []; // Mảng: khach_id => [status, note]

            $success = true;
            foreach ($attendanceData as $khachId => $data) {
                $trangThai = $data['status'] ?? 0;
                $ghiChu = $data['note'] ?? null;

                if (!$this->diemDanhModel->saveChiTiet($phienId, $khachId, $trangThai, $ghiChu)) {
                    $success = false;
                }
            }

            $status = $success ? 'dd_saved' : 'dd_error';
            header('Location: ' . BASE_URL . 'routes/index.php?action=hdv-view-diem-danh&lich_id=' . $lichId . '&phien_id=' . $phienId . '&status=' . $status);
            exit;
        }
    }

    // 4. Xóa phiên điểm danh
    public function deletePhienDiemDanh()
    {
        $phienId = $_GET['phien_id'] ?? 0;
        $lichId = $_GET['lich_id'] ?? 0;

        $this->diemDanhModel->deletePhien($phienId);

        header('Location: ' . BASE_URL . 'routes/index.php?action=hdv-tour-detail&id=' . $lichId . '&status=phien_deleted');
        exit;
    }
}
?>