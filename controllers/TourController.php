<?php
class TourController extends BaseController {
    private $tourModel;
    private $supplierModel;

    public function __construct() {
        $this->tourModel = new TourModel();
        $this->supplierModel = new SupplierModel();
    }

    public function index() {
        $tours = $this->tourModel->getAll();
        $this->render('pages/admin/tours/index', ['tours' => $tours]);
    }

    public function create() {
        $categories = $this->tourModel->getCategories();
        $suppliers = $this->supplierModel->getAll();
        $this->render('pages/admin/tours/form_them', [
            'categories' => $categories,
            'suppliers' => $suppliers
        ]);
    }

    // [NÂNG CẤP] Sử dụng Transaction để đảm bảo toàn vẹn dữ liệu
    public function store() {
        // 1. Upload ảnh đại diện trước
        $anh_tour = '';
        if (isset($_FILES['anh_tour']) && $_FILES['anh_tour']['error'] == 0) {
            $anh_tour = $this->uploadImage($_FILES['anh_tour']);
        }

        $data = [
            'ten' => $_POST['ten_tour'],
            'anh' => $anh_tour,
            'gt' => $_POST['gioi_thieu'],
            'lt' => $_POST['lich_trinh_tom_tat'],
            'gia_lon' => $_POST['gia_nguoi_lon'],
            'gia_tre' => $_POST['gia_tre_em'],
            'ngay' => $_POST['so_ngay'],
            'loai' => $_POST['loai_tour_id'],
            'bao_gom' => $_POST['bao_gom'] ?? '',
            'khong_bao_gom' => $_POST['khong_bao_gom'] ?? '',
            'chinh_sach_huy' => $_POST['chinh_sach_huy'] ?? '',
            'luu_y' => $_POST['luu_y'] ?? ''
        ];

        try {
            $this->tourModel->conn->beginTransaction(); // Bắt đầu giao dịch

            // Insert Tour
            $tourId = $this->tourModel->insertAndGetId($data);

            if (!$tourId) throw new Exception("Lỗi khi tạo Tour");

            // Insert Lịch trình
            if (isset($_POST['itinerary_title'])) {
                foreach ($_POST['itinerary_title'] as $key => $title) {
                    if (!empty($title)) {
                        $day = $key + 1;
                        $content = $_POST['itinerary_content'][$key] ?? '';
                        $this->tourModel->insertItinerary($tourId, $day, $title, $content);
                    }
                }
            }

            // Insert Album ảnh
            if (isset($_FILES['gallery']['name'][0]) && !empty($_FILES['gallery']['name'][0])) {
                $totalFiles = count($_FILES['gallery']['name']);
                for ($i = 0; $i < $totalFiles; $i++) {
                    if ($_FILES['gallery']['error'][$i] == 0) {
                        $file = [
                            'name' => $_FILES['gallery']['name'][$i],
                            'tmp_name' => $_FILES['gallery']['tmp_name'][$i],
                            'size' => $_FILES['gallery']['size'][$i]
                        ];
                        $fileName = $this->uploadImage($file);
                        if ($fileName) {
                            $this->tourModel->insertImage($tourId, $fileName);
                        }
                    }
                }
            }

            // Insert NCC
            if (isset($_POST['suppliers'])) {
                foreach ($_POST['suppliers'] as $nccId) {
                    $note = $_POST['suppliers_note'][$nccId] ?? '';
                    $this->tourModel->insertTourSupplier($tourId, $nccId, $note);
                }
            }

            $this->tourModel->conn->commit(); // Lưu tất cả
            header('Location: index.php?action=admin-tours&msg=created');

        } catch (Exception $e) {
            $this->tourModel->conn->rollBack(); // Hoàn tác nếu lỗi
            // Nếu đã lỡ upload ảnh đại diện thì xóa đi cho sạch
            if ($anh_tour && file_exists("assets/uploads/" . $anh_tour)) {
                unlink("assets/uploads/" . $anh_tour);
            }
            echo "Lỗi: " . $e->getMessage();
        }
    }

    public function edit() {
        $id = $_GET['id'];
        $tour = $this->tourModel->getDetail($id);
        if(!$tour) die('Tour không tồn tại');

        $categories = $this->tourModel->getCategories();
        $gallery = $this->tourModel->getGallery($id);
        $itinerary = $this->tourModel->getItinerary($id);
        
        $allSuppliers = $this->supplierModel->getAll();
        $currentSuppliers = $this->tourModel->getTourSuppliers($id);

        $selectedSupplierIds = array_column($currentSuppliers, 'ncc_id');
        $selectedSupplierNotes = [];
        foreach($currentSuppliers as $s) {
            $selectedSupplierNotes[$s['ncc_id']] = $s['ghi_chu'];
        }

        $this->render('pages/admin/tours/form_sua', [
            'tour' => $tour, 
            'categories' => $categories,
            'gallery' => $gallery,
            'itinerary' => $itinerary,
            'allSuppliers' => $allSuppliers,
            'selectedSupplierIds' => $selectedSupplierIds,
            'selectedSupplierNotes' => $selectedSupplierNotes
        ]);
    }

    public function update() {
        $id = $_POST['id'];
        $tour = $this->tourModel->getDetail($id); // Lấy thông tin cũ để check ảnh

        // 1. Xử lý ảnh đại diện
        $anh_tour = $_POST['anh_cu']; 
        if (isset($_FILES['anh_tour']) && $_FILES['anh_tour']['error'] == 0) {
            // Upload ảnh mới
            $anh_tour = $this->uploadImage($_FILES['anh_tour']);
            
            // [FIX] Xóa ảnh cũ đi để tiết kiệm bộ nhớ
            if (!empty($_POST['anh_cu'])) {
                $oldFile = "assets/uploads/" . $_POST['anh_cu'];
                if (file_exists($oldFile)) unlink($oldFile);
            }
        }

        $data = [
            'ten' => $_POST['ten_tour'],
            'anh' => $anh_tour,
            'gt' => $_POST['gioi_thieu'],
            'lt' => $_POST['lich_trinh_tom_tat'],
            'gia_lon' => $_POST['gia_nguoi_lon'],
            'gia_tre' => $_POST['gia_tre_em'],
            'ngay' => $_POST['so_ngay'],
            'loai' => $_POST['loai_tour_id'],
            'bao_gom' => $_POST['bao_gom'] ?? '',
            'khong_bao_gom' => $_POST['khong_bao_gom'] ?? '',
            'chinh_sach_huy' => $_POST['chinh_sach_huy'] ?? '',
            'luu_y' => $_POST['luu_y'] ?? ''
        ];

        try {
            $this->tourModel->conn->beginTransaction();

            $this->tourModel->update($id, $data);

            // Update Lịch trình (Xóa cũ thêm mới)
            $this->tourModel->deleteOldItinerary($id);
            if (isset($_POST['itinerary_title'])) {
                foreach ($_POST['itinerary_title'] as $key => $title) {
                    if (!empty($title)) {
                        $day = $key + 1;
                        $content = $_POST['itinerary_content'][$key] ?? '';
                        $this->tourModel->insertItinerary($id, $day, $title, $content);
                    }
                }
            }

            // Update Album ảnh (Chỉ thêm mới, việc xóa làm ở function riêng)
            if (isset($_FILES['gallery']['name'][0]) && !empty($_FILES['gallery']['name'][0])) {
                $totalFiles = count($_FILES['gallery']['name']);
                for ($i = 0; $i < $totalFiles; $i++) {
                    if ($_FILES['gallery']['error'][$i] == 0) {
                        $file = [
                            'name' => $_FILES['gallery']['name'][$i],
                            'tmp_name' => $_FILES['gallery']['tmp_name'][$i],
                            'size' => $_FILES['gallery']['size'][$i]
                        ];
                        $fileName = $this->uploadImage($file);
                        if ($fileName) {
                            $this->tourModel->insertImage($id, $fileName);
                        }
                    }
                }
            }

            // Update NCC
            $this->tourModel->deleteOldSuppliers($id);
            if (isset($_POST['suppliers'])) {
                foreach ($_POST['suppliers'] as $nccId) {
                    $note = $_POST['suppliers_note'][$nccId] ?? '';
                    $this->tourModel->insertTourSupplier($id, $nccId, $note);
                }
            }

            $this->tourModel->conn->commit();
            header('Location: index.php?action=admin-tours&msg=updated');

        } catch (Exception $e) {
            $this->tourModel->conn->rollBack();
            echo "Lỗi Update: " . $e->getMessage();
        }
    }

    // [MỚI] Hàm xóa ảnh trong thư viện (Gallery)
    public function deleteGalleryImage() {
        $imageId = $_GET['image_id'] ?? 0;
        $tourId = $_GET['tour_id'] ?? 0;

        if ($imageId) {
            // Lấy thông tin ảnh để xóa file vật lý
            $image = $this->tourModel->getImageById($imageId);
            if ($image) {
                $filePath = "assets/uploads/" . $image['image_url'];
                if (file_exists($filePath)) {
                    unlink($filePath); // Xóa file
                }
                // Xóa DB
                $this->tourModel->deleteImageById($imageId);
            }
        }
        // Quay lại trang sửa
        header("Location: index.php?action=admin-tour-edit&id=$tourId#gallery");
    }

    // [NÂNG CẤP] Xóa tour và xóa sạch ảnh liên quan
    public function delete() {
        $id = $_GET['id'];
        
        // 1. Lấy thông tin tour để xóa ảnh đại diện
        $tour = $this->tourModel->getDetail($id);
        if ($tour && !empty($tour['anh_tour'])) {
            $path = "assets/uploads/" . $tour['anh_tour'];
            if (file_exists($path)) unlink($path);
        }

        // 2. Lấy album ảnh để xóa
        $gallery = $this->tourModel->getGallery($id);
        foreach ($gallery as $img) {
            $path = "assets/uploads/" . $img['image_url'];
            if (file_exists($path)) unlink($path);
        }

        // 3. Xóa DB (Cascade sẽ xóa các bảng con)
        $this->tourModel->delete($id);
        header('Location: index.php?action=admin-tours&msg=deleted');
    }

    // Helper: Upload file an toàn hơn
    private function uploadImage($file) {
        $targetDir = "assets/uploads/"; // Đã sửa đường dẫn cho đúng root
        // Kiểm tra extension
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($ext, $allowed)) {
            return false; // File không hợp lệ
        }

        if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
        
        $fileName = time() . "_" . basename($file["name"]);
        $targetFilePath = $targetDir . $fileName;
        
        if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
            return $fileName;
        }
        return false;
    }
}
?>