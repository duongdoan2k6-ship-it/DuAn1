<?php
class TourController extends BaseController {
    private $tourModel;
    private $supplierModel;
    private $lichModel; // Khai báo model Lịch Khởi Hành

    public function __construct() {
        $this->tourModel = new TourModel();
        $this->supplierModel = new SupplierModel();
        $this->lichModel = new LichKhoiHanhModel(); // Khởi tạo model
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

    // Sử dụng Transaction để đảm bảo toàn vẹn dữ liệu
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
            
            // Xóa ảnh cũ đi để tiết kiệm bộ nhớ
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

    // Hàm xóa ảnh trong thư viện (Gallery)
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

    // Xóa tour và xóa sạch ảnh liên quan
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

    // =========================================================================
    // [PHẦN MỚI BỔ SUNG] QUẢN LÝ LỊCH TRÌNH & PHÂN CÔNG NHÂN SỰ
    // =========================================================================

    // 1. Hiển thị form thêm lịch (Mapping: admin-create-lich)
    public function createLich() {
        $tours = $this->tourModel->getAll();
        // Lấy danh sách HDV đang sẵn sàng để chọn nhanh
        $guides = $this->lichModel->getAllHDVList(); 
        
        $this->render('pages/admin/form_them_lich', [
            'tours' => $tours,
            'guides' => $guides
        ]);
    }

    // 2. Xử lý lưu lịch mới (Mapping: admin-store-lich)
    public function storeLich() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Lấy dữ liệu từ form
            $data = [
                'tour_id' => $_POST['tour_id'],
                'ngay_khoi_hanh' => $_POST['ngay_khoi_hanh'],
                'ngay_ket_thuc' => $_POST['ngay_ket_thuc'],
                'diem_tap_trung' => $_POST['diem_tap_trung'],
                'so_cho_toi_da' => $_POST['so_cho_toi_da'],
                // Lưu ý: hdv_id và ghi_chu_nhan_su sẽ được tách ra xử lý riêng
                'hdv_id' => $_POST['hdv_id'] ?? null, 
                'ghi_chu_nhan_su' => $_POST['ghi_chu_nhan_su'] ?? ''
            ];

            try {
                $this->lichModel->conn->beginTransaction();

                // 1. Insert vào bảng lich_khoi_hanh
                // Hàm insert trong Model đã có unset('hdv_id') nên không lo lỗi dư cột
                $lichId = $this->lichModel->insert($data);

                if ($lichId) {
                    // 2. Nếu có chọn HDV chính, insert ngay vào bảng lich_nhan_vien
                    if (!empty($data['hdv_id'])) {
                        // Gán vai trò là HDV_chinh
                        $this->lichModel->assignStaff($lichId, $data['hdv_id'], 'HDV_chinh');
                    }

                    // 3. (Tuỳ chọn) Bạn có thể lưu ghi chú nhân sự vào bảng riêng nếu cần
                    // hiện tại hệ thống chỉ xử lý lưu HDV chính.
                }

                $this->lichModel->conn->commit();
                // Chuyển hướng đến trang chi tiết để có thể thêm các nhân sự khác
                header('Location: index.php?action=admin-schedule-staff&id=' . $lichId . '&msg=Lịch trình đã được tạo thành công');
            } catch (Exception $e) {
                $this->lichModel->conn->rollBack();
                // Redirect về trang tạo và báo lỗi
                header('Location: index.php?action=admin-create-lich&error=' . urlencode($e->getMessage()));
            }
        }
    }

    // 3. Hiển thị form sửa lịch & phân công (Mapping với action: admin-schedule-staff)
    public function editSchedule() {
        $id = $_GET['id'] ?? 0;
        
        // Lấy chi tiết lịch
        $lich = $this->lichModel->getDetail($id);
        if (!$lich) {
            die("Lịch trình không tồn tại!");
        }

        // Lấy danh sách tất cả Tours (để hiện trong dropdown chọn tour)
        $tours = $this->tourModel->getAll();

        // Lấy danh sách nhân sự ĐÃ phân công cho lịch này
        $assignedStaff = $this->lichModel->getAssignedStaff($id);

        // Lấy danh sách TẤT CẢ nhân sự (để hiện trong dropdown thêm mới)
        $allStaff = $this->lichModel->getAllNhanVienList();

        // Render view form_sua_lich.php
        $this->render('pages/admin/form_sua_lich', [
            'lich' => $lich,
            'tours' => $tours,
            'assignedStaff' => $assignedStaff,
            'allStaff' => $allStaff
        ]);
    }

    // 4. Cập nhật thông tin lịch trình (Mapping với action: admin-update-lich)
    public function updateSchedule() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            
            // 1. Lấy thông tin hiện tại trong DB để check ràng buộc
            $currentLich = $this->lichModel->getDetail($id);
            if (!$currentLich) {
                die("Lỗi: Không tìm thấy lịch trình!");
            }

            // 2. Validate số chỗ: Không được giảm thấp hơn số đã đặt
            $soChoMoi = $_POST['so_cho_toi_da'];
            $soChoDaDat = $currentLich['so_cho_da_dat'];

            if ($soChoMoi < $soChoDaDat) {
                $errorMsg = "Không thể giảm số chỗ xuống $soChoMoi vì đã có $soChoDaDat khách đặt!";
                header("Location: index.php?action=admin-schedule-staff&id=$id&error=" . urlencode($errorMsg));
                return;
            }

            // 3. Xử lý thời gian: Giữ nguyên NGÀY cũ, chỉ cập nhật GIỜ mới
            // Lấy phần ngày (Y-m-d) từ dữ liệu cũ
            $oldStartDate = date('Y-m-d', strtotime($currentLich['ngay_khoi_hanh']));
            $oldEndDate = date('Y-m-d', strtotime($currentLich['ngay_ket_thuc']));

            // Lấy phần giờ (H:i) từ form gửi lên
            $newStartTime = $_POST['gio_khoi_hanh']; // format 08:00
            $newEndTime = $_POST['gio_ket_thuc'];   // format 18:00

            // Ghép lại thành datetime hoàn chỉnh
            $finalStart = $oldStartDate . ' ' . $newStartTime . ':00';
            $finalEnd = $oldEndDate . ' ' . $newEndTime . ':00';

            // 4. Chuẩn bị dữ liệu update (Lưu ý: KHÔNG update tour_id)
            $data = [
                'ngay_khoi_hanh' => $finalStart,
                'ngay_ket_thuc' => $finalEnd,
                'so_cho_toi_da' => $soChoMoi,
                'diem_tap_trung' => $_POST['diem_tap_trung'],
                'trang_thai' => $_POST['trang_thai']
            ];

            // 5. Gọi Model update
            // Sử dụng hàm mới updateScheduleInfo thay vì update thường
            if ($this->lichModel->updateScheduleInfo($id, $data)) {
                $msg = 'updated';
                header("Location: index.php?action=admin-schedule-staff&id=$id&msg=$msg");
            } else {
                $msg = 'Lỗi hệ thống, vui lòng thử lại';
                header("Location: index.php?action=admin-schedule-staff&id=$id&error=" . urlencode($msg));
            }
        }
    }

    public function addStaff() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $lichId = $_POST['lich_id'];
            $nhanVienId = $_POST['nhan_vien_id'];
            $vaiTro = $_POST['vai_tro'];

            if ($this->lichModel->assignStaff($lichId, $nhanVienId, $vaiTro)) {
                $msg = 'Staff assigned successfully';
            } else {
                $msg = 'Error assigning staff';
            }
            header("Location: index.php?action=admin-schedule-staff&id=$lichId&msg=$msg");
        }
    }

    public function removeStaff() {
        $id = $_GET['id'] ?? 0;
        $lichId = $_GET['lich_id'] ?? 0;

        if ($this->lichModel->unassignStaff($id)) {
            $msg = 'Staff removed';
        } else {
            $msg = 'Error removing staff';
        }
        header("Location: index.php?action=admin-schedule-staff&id=$lichId&msg=$msg");
    }

    private function uploadImage($file) {
        $targetDir = "assets/uploads/"; 
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($ext, $allowed)) {
            return false; 
        }

        if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
        
        $fileName = time() . "_" . basename($file["name"]);
        $targetFilePath = $targetDir . $fileName;
        
        if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
            return $fileName;
        }
        return false;
    }
    public function detailLich() {
        $id = $_GET['id'] ?? 0;
        
        // 1. Thông tin lịch & Tour
        $lich = $this->lichModel->getDetail($id);
        if (!$lich) die("Lịch trình không tồn tại!");

        // 2. Thông tin nhân sự
        $staff = $this->lichModel->getAssignedStaff($id);

        // 3. Danh sách Booking của lịch này
        $bookingModel = new BookingModel();
        $bookings = $bookingModel->getAllBookings(['lich_id' => $id]);

        // 4. Danh sách tất cả hành khách (Manifest)
        $passengers = $bookingModel->getPassengersByLich($id);

        $this->render('pages/admin/tours/chi_tiet_lich', [
            'lich' => $lich,
            'staff' => $staff,
            'bookings' => $bookings,
            'passengers' => $passengers
        ]);
    }
}
?>