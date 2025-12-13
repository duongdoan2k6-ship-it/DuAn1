<?php
class TourController extends BaseController {
    private $tourModel;
    private $supplierModel; // Thêm model NCC

    public function __construct() {
        $this->tourModel = new TourModel();
        $this->supplierModel = new SupplierModel(); // Khởi tạo
    }

    public function index() {
        $tours = $this->tourModel->getAll();
        $this->render('pages/admin/tours/index', ['tours' => $tours]);
    }

    public function create() {
        $categories = $this->tourModel->getCategories();
        $suppliers = $this->supplierModel->getAll(); // Lấy list NCC để hiện checkbox
        
        $this->render('pages/admin/tours/form_them', [
            'categories' => $categories,
            'suppliers' => $suppliers
        ]);
    }

    public function store() {
        // 1. Upload ảnh đại diện
        $anh_tour = '';
        if (isset($_FILES['anh_tour']) && $_FILES['anh_tour']['error'] == 0) {
            $anh_tour = this_upload_image($_FILES['anh_tour']);
        }

        // 2. Insert Tour
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
        
        $tourId = $this->tourModel->insertAndGetId($data);

        if ($tourId) {
            // 3. Lưu Lịch trình
            if (isset($_POST['itinerary_title'])) {
                foreach ($_POST['itinerary_title'] as $key => $title) {
                    if (!empty($title)) {
                        $day = $key + 1;
                        $content = $_POST['itinerary_content'][$key] ?? '';
                        $this->tourModel->insertItinerary($tourId, $day, $title, $content);
                    }
                }
            }

            // 4. Lưu Album ảnh
            if (isset($_FILES['gallery']['name'][0]) && !empty($_FILES['gallery']['name'][0])) {
                $totalFiles = count($_FILES['gallery']['name']);
                for ($i = 0; $i < $totalFiles; $i++) {
                    if ($_FILES['gallery']['error'][$i] == 0) {
                        $fileName = time() . '_' . $_FILES['gallery']['name'][$i];
                        $targetPath = 'assets/uploads/' . $fileName;
                        if (move_uploaded_file($_FILES['gallery']['tmp_name'][$i], 'public/' . $targetPath)) {
                             $this->tourModel->insertImage($tourId, $fileName);
                        }
                    }
                }
            }

            // 5. Lưu Nhà Cung Cấp (MỚI)
            if (isset($_POST['suppliers'])) {
                foreach ($_POST['suppliers'] as $nccId) {
                    $note = $_POST['suppliers_note'][$nccId] ?? ''; // Lấy ghi chú tương ứng
                    $this->tourModel->insertTourSupplier($tourId, $nccId, $note);
                }
            }
            
            header('Location: index.php?action=admin-tours');
        } else {
            echo "Lỗi thêm mới!";
        }
    }

    public function edit() {
        $id = $_GET['id'];
        $tour = $this->tourModel->getDetail($id);
        $categories = $this->tourModel->getCategories();
        
        // Dữ liệu phụ
        $gallery = $this->tourModel->getGallery($id);
        $itinerary = $this->tourModel->getItinerary($id);
        
        // Dữ liệu NCC
        $allSuppliers = $this->supplierModel->getAll(); // Tất cả NCC
        $currentSuppliers = $this->tourModel->getTourSuppliers($id); // NCC đã chọn của tour này

        // Chuyển danh sách NCC đã chọn thành mảng ID đơn giản để dễ kiểm tra trong render
        $selectedSupplierIds = array_column($currentSuppliers, 'ncc_id');
        // Chuyển danh sách NCC đã chọn thành mảng Key-Value (ID => Ghi chú) để điền vào ô input
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
        
        // 1. Update Tour Info
        $anh_tour = $_POST['anh_cu']; 
        if (isset($_FILES['anh_tour']) && $_FILES['anh_tour']['error'] == 0) {
            $anh_tour = this_upload_image($_FILES['anh_tour']);
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

        $this->tourModel->update($id, $data);

        // 2. Update Lịch trình
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

        // 3. Update Album ảnh
        if (isset($_FILES['gallery']['name'][0]) && !empty($_FILES['gallery']['name'][0])) {
            $totalFiles = count($_FILES['gallery']['name']);
            for ($i = 0; $i < $totalFiles; $i++) {
                if ($_FILES['gallery']['error'][$i] == 0) {
                    $fileName = time() . '_' . $_FILES['gallery']['name'][$i];
                    $targetPath = 'assets/uploads/' . $fileName;
                    if (move_uploaded_file($_FILES['gallery']['tmp_name'][$i], 'public/' . $targetPath)) {
                            $this->tourModel->insertImage($id, $fileName);
                    }
                }
            }
        }

        // 4. Update Nhà Cung Cấp (MỚI)
        $this->tourModel->deleteOldSuppliers($id); // Xóa hết cũ
        if (isset($_POST['suppliers'])) {
            foreach ($_POST['suppliers'] as $nccId) {
                $note = $_POST['suppliers_note'][$nccId] ?? '';
                $this->tourModel->insertTourSupplier($id, $nccId, $note); // Thêm lại mới
            }
        }

        header('Location: index.php?action=admin-tours');
    }

    public function delete() {
        $id = $_GET['id'];
        $this->tourModel->delete($id);
        header('Location: index.php?action=admin-tours');
    }
}

function this_upload_image($file) {
    $targetDir = "public/assets/uploads/";
    $fileName = time() . "_" . basename($file["name"]);
    $targetFilePath = $targetDir . $fileName;
    if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
    if (move_uploaded_file($file["tmp_name"], $targetFilePath)) return $fileName;
    return "default.jpg";
}

    
?>