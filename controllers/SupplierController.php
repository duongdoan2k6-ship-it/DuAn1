<?php
class SupplierController extends BaseController {
    private $model;

    public function __construct() {
        $this->model = new SupplierModel();
    }

    public function index() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') exit;
        
        $suppliers = $this->model->getAll();
        $this->render('pages/admin/suppliers/index', ['suppliers' => $suppliers]);
    }

    public function create() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') exit;
        $this->render('pages/admin/suppliers/form_them');
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'ten' => trim($_POST['ten_ncc']),
                'dv' => $_POST['dich_vu'],
                'sdt' => $_POST['sdt'],
                'email' => $_POST['email'],
                'dc' => $_POST['dia_chi']
            ];
            
            if (empty($data['ten'])) {
                echo "<script>alert('Tên nhà cung cấp không được để trống!'); window.history.back();</script>";
                return;
            }

            $this->model->insert($data);
            header('Location: index.php?action=admin-suppliers&msg=created');
        }
    }

    public function edit() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') exit;
        
        $id = $_GET['id'] ?? 0;
        $supplier = $this->model->getDetail($id);
        
        if (!$supplier) die("Không tìm thấy dữ liệu!");
        
        $this->render('pages/admin/suppliers/form_sua', ['supplier' => $supplier]);
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $data = [
                'ten' => trim($_POST['ten_ncc']),
                'dv' => $_POST['dich_vu'],
                'sdt' => $_POST['sdt'],
                'email' => $_POST['email'],
                'dc' => $_POST['dia_chi']
            ];
            
            $this->model->update($id, $data);
            header('Location: index.php?action=admin-suppliers&msg=updated');
        }
    }

    // [NÂNG CẤP] Xóa an toàn
    public function delete() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') exit;

        $id = $_GET['id'] ?? 0;
        
        // 1. Kiểm tra xem NCC này đã có giao dịch chưa
        if ($this->model->checkUsage($id)) {
            // Nếu đã có giao dịch -> Không cho xóa để bảo toàn dữ liệu tài chính
            echo "<script>
                alert('KHÔNG THỂ XÓA!\\n\\nNhà cung cấp này đã có lịch sử cung cấp dịch vụ cho các Tour.\\nViệc xóa sẽ làm mất dữ liệu chi phí và báo cáo tài chính.');
                window.location.href = 'index.php?action=admin-suppliers';
            </script>";
            exit;
        }

        // 2. Nếu sạch (chưa dùng bao giờ) -> Cho xóa
        $this->model->delete($id);
        header('Location: index.php?action=admin-suppliers&msg=deleted');
    }
}
?>