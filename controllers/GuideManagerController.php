<?php
class GuideManagerController extends BaseController
{

    private $guideModel;

    public function __construct()
    {
        $this->guideModel = new GuideModel();
    }

    public function index()
    {
        // 1. Kiểm tra quyền Admin
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') exit;

        // 2. Lấy các tham số lọc từ URL (keyword, vai trò, trạng thái...)
        $filters = [
            'keyword'    => $_GET['keyword'] ?? '',
            'phan_loai'  => $_GET['phan_loai'] ?? '',
            'role'       => $_GET['role'] ?? '',
            'trang_thai' => $_GET['trang_thai'] ?? '',
        ];

        // 3. Gọi Model để lấy danh sách nhân sự
        $guides = $this->guideModel->getAll($filters);

        // 4. Render View và truyền dữ liệu
        $this->render('pages/admin/guides/index', [
            'guides' => $guides,
            'filters' => $filters
        ]);
    }

    public function create()
    {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') exit;
        $this->render('pages/admin/guides/form_them');
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);

            // 1. Kiểm tra email
            if ($this->guideModel->checkEmailExists($email)) {
                echo "<script>alert('Lỗi: Email này đã tồn tại!'); window.history.back();</script>";
                return;
            }
            $sdt = trim($_POST['sdt']);
            if (!preg_match('/^0[0-9]{9}$/', $sdt)) {
                echo "<script>alert('Lỗi: Số điện thoại không hợp lệ (Phải bắt đầu bằng số 0 và có 10 chữ số)!'); window.history.back();</script>";
                return;
            }

            // 2. Xử lý ảnh
            $anh = 'default_avatar.png';
            if (isset($_FILES['anh_dai_dien']) && $_FILES['anh_dai_dien']['error'] == 0) {
                $uploaded = $this->uploadImage($_FILES['anh_dai_dien']);
                if ($uploaded) $anh = $uploaded;
            }

            // 3. Mật khẩu
            $mat_khau_hash = $_POST['mat_khau'];

            $data = [
                'ho_ten' => $_POST['ho_ten'],
                'ngay_sinh' => $_POST['ngay_sinh'],
                'email' => $email,
                'mat_khau' => $mat_khau_hash,
                'sdt' => $_POST['sdt'],
                'anh' => $anh,
                'chung_chi' => $_POST['chung_chi'] ?? '',
                'kinh_nghiem' => $_POST['kinh_nghiem'] ?? '',
                'suc_khoe' => $_POST['suc_khoe'] ?? 'Tốt',
                'role' => $_POST['phan_loai_nhan_su'] ?? 'HDV',
            ];

            if ($this->guideModel->insert($data)) {
                header('Location: ' . BASE_URL . 'routes/index.php?action=admin-guides&msg=created');
            } else {
                echo "Lỗi hệ thống!";
            }
        }
    }

    public function edit()
    {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') exit;
        $id = $_GET['id'] ?? 0;
        $guide = $this->guideModel->getDetail($id);
        if (!$guide) die("Không tìm thấy nhân sự!");
        $this->render('pages/admin/guides/form_sua', ['guide' => $guide]);
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $email = trim($_POST['email']);

            // Kiểm tra email trùng
            if ($this->guideModel->checkEmailExists($email, $id)) {
                echo "<script>alert('Lỗi: Email này đã được sử dụng!'); window.history.back();</script>";
                return;
            }

            $sdt = trim($_POST['sdt']);
            if (!preg_match('/^0[0-9]{9}$/', $sdt)) {
                echo "<script>alert('Lỗi: Số điện thoại không hợp lệ (Phải bắt đầu bằng số 0 và có 10 chữ số)!'); window.history.back();</script>";
                return;
            }

            // Xử lý ảnh đại diện
            $oldInfo = $this->guideModel->getDetail($id);
            $anh = $oldInfo['anh_dai_dien'];

            if (isset($_FILES['anh_dai_dien']) && $_FILES['anh_dai_dien']['error'] == 0) {
                $newImg = $this->uploadImage($_FILES['anh_dai_dien']);
                if ($newImg) {
                    $anh = $newImg;
                    // Xóa ảnh cũ nếu không phải mặc định
                    if ($oldInfo['anh_dai_dien'] != 'default_avatar.png') {
                        $oldPath = 'assets/uploads/hdv/' . $oldInfo['anh_dai_dien'];
                        if (file_exists($oldPath)) unlink($oldPath);
                    }
                }
            }

            // Xử lý mật khẩu (chỉ cập nhật nếu người dùng nhập mới)
            $mat_khau_update = '';
            if (!empty($_POST['mat_khau_moi'])) {
                $mat_khau_update = $_POST['mat_khau_moi'];
            }

            // [ĐÃ SỬA] Chỉ lấy các trường thông tin cá nhân từ Form
            // Đã bỏ: chung_chi, kinh_nghiem, suc_khoe, phan_loai, role
            $data = [
                'ho_ten'    => $_POST['ho_ten'],
                'ngay_sinh' => $_POST['ngay_sinh'],
                'email'     => $email,
                'sdt'       => $_POST['sdt'],
                'anh'       => $anh,
                'mat_khau'  => $mat_khau_update
            ];

            // Gọi Model để update
            $this->guideModel->update($id, $data);

            // Chuyển hướng
            header('Location: ' . BASE_URL . 'routes/index.php?action=admin-guides&msg=updated');
        }
    }

    public function delete()
    {
        $id = $_GET['id'] ?? 0;

        // 1. Lấy thông tin nhân viên để lấy tên file ảnh
        $guide = $this->guideModel->getDetail($id);

        if ($guide) {
            // 2. Xóa ảnh nếu không phải ảnh mặc định
            if ($guide['anh_dai_dien'] != 'default_avatar.png') {
                $path = 'assets/uploads/hdv/' . $guide['anh_dai_dien'];
                if (file_exists($path)) {
                    unlink($path); // Xóa file ảnh vật lý
                }
            }

            // 3. Xóa dữ liệu trong DB
            $this->guideModel->delete($id);
        }

        header('Location: ' . BASE_URL . 'routes/index.php?action=admin-guides&msg=deleted');
    }

    public function detail()
    {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') exit;
        $id = $_GET['id'] ?? 0;
        $guide = $this->guideModel->getDetail($id);
        $history = $this->guideModel->getHistory($id);
        $this->render('pages/admin/guides/detail', ['guide' => $guide, 'history' => $history]);
    }

    private function uploadImage($file)
    {
        $targetDir = "../assets/uploads/hdv/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $fileName = time() . "_" . basename($file["name"]);
        if (move_uploaded_file($file["tmp_name"], $targetDir . $fileName)) {
            return $fileName;
        }
        return false;
    }
}
