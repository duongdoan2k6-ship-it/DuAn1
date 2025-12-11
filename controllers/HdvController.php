<?php
class HdvController extends BaseController
{

    private $nhatKyModel;

    public function __construct()
    {
        $this->nhatKyModel = new NhatKyTourModel();
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

    // Xem chi tiết tour, danh sách khách và nhật ký
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

        // 4. [MỚI] Lấy danh sách nhật ký tour (thực tế)
        $nhatKyList = $this->nhatKyModel->getLogsByLichId($lichId);

        // Truyền tất cả sang view
        $this->render('pages/hdv/tour_detail', [
            'tourInfo'   => $tourInfo,
            'passengers' => $passengers,
            'itineraries' => $itineraries,
            'nhatKyList' => $nhatKyList, // Truyền biến này sang view
            'lichId'     => $lichId
        ]);
    }

    // Xử lý lưu điểm danh
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

    // [MỚI] Xử lý thêm nhật ký tour
    public function addNhatKy()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $lichId = $_POST['lich_khoi_hanh_id'];

            // Lấy dữ liệu từ form
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
                // Đường dẫn lưu file (tính từ file index.php trong thư mục routes)
                // Cần đi ra khỏi routes (../) vào public/assets/uploads/nhat_ky/
                $uploadDir = '../public/assets/uploads/nhat_ky/';

                // Tạo thư mục nếu chưa tồn tại
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileName = time() . '_' . basename($_FILES['hinh_anh']['name']);
                $targetFile = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['hinh_anh']['tmp_name'], $targetFile)) {
                    // Lưu đường dẫn vào DB (lưu đường dẫn tương đối để dễ hiển thị)
                    // Vì khi hiển thị sẽ từ public/index.php hoặc routes, ta nên lưu từ assets...
                    $data['hinh_anh'] = 'assets/uploads/nhat_ky/' . $fileName;
                }
            }

            // Gọi model để lưu
            if ($this->nhatKyModel->addNhatKy($data)) {
                $status = 'log_success';
            } else {
                $status = 'log_error';
            }

            // Quay lại trang chi tiết
            header('Location: ' . BASE_URL . 'routes/index.php?action=hdv-tour-detail&id=' . $lichId . '&status=' . $status);
            exit;
        }
    }
    // --- [MỚI] Hiển thị form sửa nhật ký ---
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

        // Gọi view sửa (chúng ta sẽ tạo file này ở Bước 4)
        $this->render('pages/hdv/edit_nhat_ky', ['log' => $log]);
    }

    // --- [MỚI] Xử lý cập nhật nhật ký ---
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
                'hinh_anh'          => '' // Mặc định rỗng
            ];

            // Xử lý upload ảnh mới (nếu có)
            if (isset($_FILES['hinh_anh']) && $_FILES['hinh_anh']['error'] == 0) {
                $uploadDir = '../public/assets/uploads/nhat_ky/';
                if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

                $fileName = time() . '_' . basename($_FILES['hinh_anh']['name']);
                $targetFile = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['hinh_anh']['tmp_name'], $targetFile)) {
                    $data['hinh_anh'] = 'assets/uploads/nhat_ky/' . $fileName;

                    // (Tùy chọn) Xóa ảnh cũ để tiết kiệm dung lượng
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

    // --- [MỚI] Xử lý xóa nhật ký ---
    public function deleteNhatKy()
    {
        $id = $_GET['id'] ?? 0;
        $lichId = $_GET['lich_id'] ?? 0;

        // Lấy thông tin để xóa ảnh
        $log = $this->nhatKyModel->getLogById($id);

        if ($log) {
            // Xóa file ảnh nếu có
            if (!empty($log['hinh_anh'])) {
                $filePath = '../public/' . $log['hinh_anh'];
                if (file_exists($filePath)) unlink($filePath);
            }

            // Xóa trong DB
            $this->nhatKyModel->deleteNhatKy($id);
        }

        header('Location: ' . BASE_URL . 'routes/index.php?action=hdv-tour-detail&id=' . $lichId . '&status=delete_success');
        exit;
    }
}
