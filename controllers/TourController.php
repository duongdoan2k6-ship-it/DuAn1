<?php
class TourController extends BaseController
{
    private $tourModel;
    private $supplierModel;
    private $lichModel;
    private $guideModel;
    public function __construct()
    {
        $this->tourModel = new TourModel();
        $this->supplierModel = new SupplierModel();
        $this->lichModel = new LichKhoiHanhModel();
        $this->guideModel = new GuideModel();
    }

    public function index()
    {
        $tours = $this->tourModel->getAll();
        $this->render('pages/admin/tours/index', ['tours' => $tours]);
    }

    

    public function create()
    {
        $categories = $this->tourModel->getCategories();
        $suppliers = $this->supplierModel->getAll();
        $this->render('pages/admin/tours/form_them', [
            'categories' => $categories,
            'suppliers' => $suppliers
        ]);
    }

    public function store()
    {
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
            $this->tourModel->conn->beginTransaction(); 

            $tourId = $this->tourModel->insertAndGetId($data);

            if (!$tourId) throw new Exception("Lỗi khi tạo Tour");

            if (isset($_POST['itinerary_title'])) {
                foreach ($_POST['itinerary_title'] as $key => $title) {
                    if (!empty($title)) {
                        $day = $key + 1;
                        $content = $_POST['itinerary_content'][$key] ?? '';
                        $this->tourModel->insertItinerary($tourId, $day, $title, $content);
                    }
                }
            }

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

            if (isset($_POST['suppliers'])) {
                foreach ($_POST['suppliers'] as $nccId) {
                    $note = $_POST['suppliers_note'][$nccId] ?? '';
                    $this->tourModel->insertTourSupplier($tourId, $nccId, $note);
                }
            }

            $this->tourModel->conn->commit(); 
            header('Location: index.php?action=admin-tours&msg=created');
        } catch (Exception $e) {
            $this->tourModel->conn->rollBack(); 
            if ($anh_tour && file_exists("assets/uploads/" . $anh_tour)) {
                unlink("assets/uploads/" . $anh_tour);
            }
            echo "Lỗi: " . $e->getMessage();
        }
    }

    public function edit()
    {
        $id = $_GET['id'];
        $tour = $this->tourModel->getDetail($id);
        if (!$tour) die('Tour không tồn tại');

        $categories = $this->tourModel->getCategories();
        $gallery = $this->tourModel->getGallery($id);
        $itinerary = $this->tourModel->getItinerary($id);

        $allSuppliers = $this->supplierModel->getAll();
        $currentSuppliers = $this->tourModel->getTourSuppliers($id);

        $selectedSupplierIds = array_column($currentSuppliers, 'ncc_id');
        $selectedSupplierNotes = [];
        foreach ($currentSuppliers as $s) {
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

    public function update()
    {
        $id = $_POST['id'];
        $tour = $this->tourModel->getDetail($id);

        // 1. Xử lý ảnh đại diện
        $anh_tour = $_POST['anh_cu'];
        if (isset($_FILES['anh_tour']) && $_FILES['anh_tour']['error'] == 0) {
            $anh_tour = $this->uploadImage($_FILES['anh_tour']);

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

            // Cập nhật thông tin chung tour
            $this->tourModel->update($id, $data);

            // --- [PHẦN SỬA ĐỔI QUAN TRỌNG] ---
            // Xử lý Lịch Trình: Dùng vòng lặp FOR theo số ngày để đảm bảo dữ liệu khớp 100%
            
            $this->tourModel->deleteOldItinerary($id); // Xóa lịch trình cũ
            
            $soNgay = (int)$_POST['so_ngay']; 
            $titles = $_POST['itinerary_title'] ?? [];
            $contents = $_POST['itinerary_content'] ?? [];

            // Chỉ chạy vòng lặp đúng bằng số ngày ($soNgay)
            for ($i = 0; $i < $soNgay; $i++) {
                // Kiểm tra xem dữ liệu dòng đó có tồn tại không
                if (isset($titles[$i])) {
                    $day = $i + 1; // Ngày 1, 2, 3...
                    $title = $titles[$i];
                    $content = $contents[$i] ?? ''; 
                    
                    // Gọi hàm insert
                    $this->tourModel->insertItinerary($id, $day, $title, $content);
                }
            }
            // ----------------------------------

            // Xử lý Gallery ảnh
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

            // Xử lý Nhà cung cấp
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

    public function deleteGalleryImage()
    {
        $imageId = $_GET['image_id'] ?? 0;
        $tourId = $_GET['tour_id'] ?? 0;

        if ($imageId) {
            $image = $this->tourModel->getImageById($imageId);
            if ($image) {
                $filePath = "assets/uploads/" . $image['image_url'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                $this->tourModel->deleteImageById($imageId);
            }
        }

        header("Location: index.php?action=admin-tour-edit&id=$tourId#gallery");
    }

   public function delete()
    {
        $id = $_GET['id'];

        // --- BƯỚC KIỂM TRA AN TOÀN ---
        // Gọi hàm vừa thêm ở Bước 1 để xem Tour có đang bận không
        $isBusy = $this->lichModel->checkTourDangBan($id);

        if ($isBusy) {
            // Nếu Tour đang có khách/lịch chạy -> CHẶN NGAY
            $errorMsg = "Không thể xóa Tour này vì đang có lịch khởi hành chưa kết thúc!";
            header('Location: index.php?action=admin-tours&msg=error&content=' . urlencode($errorMsg));
            return; 
        }

        // Nếu an toàn -> Gọi hàm xóa mềm ở Bước 3
        // (Lưu ý: Đã xóa đoạn code unlink ảnh ở đây để giữ ảnh cho việc khôi phục)
        $this->tourModel->delete($id);
        header('Location: index.php?action=admin-tours&msg=deleted');
    }

    // 2. [MỚI] Các hàm xử lý Thùng rác
    public function trash() {
        $deletedTours = $this->tourModel->getDeleted();
        $this->render('pages/admin/tours/trash', ['tours' => $deletedTours]);
    }

    public function restore() {
        $id = $_GET['id'];
        $this->tourModel->restore($id);
        header('Location: index.php?action=admin-tour-trash&msg=restored');
    }

    public function destroy() {
        $id = $_GET['id'];
        // Logic xóa ảnh vật lý chỉ thực hiện khi xóa vĩnh viễn
        $tour = $this->tourModel->getDetail($id);
        if ($tour && !empty($tour['anh_tour'])) {
            $path = "assets/uploads/" . $tour['anh_tour'];
            if (file_exists($path)) unlink($path);
        }
        // Xóa các ảnh gallery... (giữ nguyên logic cũ của bạn)
        $gallery = $this->tourModel->getGallery($id);
        foreach ($gallery as $img) {
            $path = "assets/uploads/" . $img['image_url'];
            if (file_exists($path)) unlink($path);
        }

        $this->tourModel->forceDelete($id);
        header('Location: index.php?action=admin-tour-trash&msg=destroyed');
    }




    public function createLich()
    {
        $tours = $this->tourModel->getAll();

        $listHDV = $this->guideModel->getAll([
            'role' => 'HDV',
            'trang_thai' => 'SanSang'
        ]);

        $listTaiXe = $this->guideModel->getAll([
            'role' => 'TaiXe',
            'trang_thai' => 'SanSang'
        ]);
        $guides = $this->lichModel->getAllHDVList();

        $this->render('pages/admin/form_them_lich', [
            'tours' => $tours,
            'guides' => $guides,
            'listHDV' => $listHDV,
            'listTaiXe' => $listTaiXe
        ]);
    }

    public function storeLich()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'tour_id' => $_POST['tour_id'],
                'ngay_khoi_hanh' => $_POST['ngay_khoi_hanh'],
                'ngay_ket_thuc' => $_POST['ngay_ket_thuc'],
                'diem_tap_trung' => $_POST['diem_tap_trung'],
                'so_cho_toi_da' => $_POST['so_cho_toi_da'],
                'hdv_id' => $_POST['hdv_id'] ?? null,
                'ghi_chu_nhan_su' => $_POST['ghi_chu_nhan_su'] ?? ''
            ];

            $taixe_id = $_POST['taixe_id'] ?? null;

            if (!empty($data['hdv_id'])) {
                $busyTour = $this->lichModel->checkStaffAvailability(
                    $data['hdv_id'],
                    $data['ngay_khoi_hanh'],
                    $data['ngay_ket_thuc']
                );

                if ($busyTour) {
                    $tenTour = $busyTour['ten_tour'];
                    $start = date('d/m H:i', strtotime($busyTour['ngay_khoi_hanh']));
                    $end = date('d/m H:i', strtotime($busyTour['ngay_ket_thuc']));

                    $errorMsg = "HDV được chọn đang bận đi tour: \"$tenTour\" ($start - $end).";
                    header('Location: index.php?action=admin-create-lich&error=' . urlencode($errorMsg));
                    return;
                }
            }

            if (!empty($taixe_id)) {
                $busyDriver = $this->lichModel->checkStaffAvailability(
                    $taixe_id,
                    $data['ngay_khoi_hanh'],
                    $data['ngay_ket_thuc']
                );

                if ($busyDriver) {
                    $tenTour = $busyDriver['ten_tour'];
                    $start = date('d/m H:i', strtotime($busyDriver['ngay_khoi_hanh']));
                    $end = date('d/m H:i', strtotime($busyDriver['ngay_ket_thuc']));

                    $errorMsg = "Tài xế được chọn đang bận đi tour: \"$tenTour\" ($start - $end).";
                    header('Location: index.php?action=admin-create-lich&error=' . urlencode($errorMsg));
                    return;
                }
            }

            try {
                $this->lichModel->conn->beginTransaction();

                $lichId = $this->lichModel->insert($data);
                
                if ($lichId) {
                    if (!empty($data['hdv_id'])) {
                        $this->lichModel->assignStaff($lichId, $data['hdv_id'], 'HDV_chinh');
                    }

                    if (!empty($taixe_id)) {
                        $this->lichModel->assignStaff($lichId, $taixe_id, 'TaiXe');
                    }
                }

                $this->lichModel->conn->commit();
                header('Location: index.php?action=admin-schedule-staff&id=' . $lichId . '&msg=Lịch trình đã được tạo thành công');
            } catch (Exception $e) {
                $this->lichModel->conn->rollBack();
                header('Location: index.php?action=admin-create-lich&error=' . urlencode($e->getMessage()));
            }
        }
    }

    public function editSchedule()
    {
        $id = $_GET['id'] ?? 0;

        $lich = $this->lichModel->getDetail($id);
        if (!$lich) {
            die("Lịch trình không tồn tại!");
        }
        $tours = $this->tourModel->getAll();
        $assignedStaff = $this->lichModel->getAssignedStaff($id);

        $startDate = $lich['ngay_khoi_hanh'];
        $endDate = $lich['ngay_ket_thuc'];

        $listHDV = $this->lichModel->getAvailableStaff($startDate, $endDate, 'HDV');

        $listTaiXe = $this->lichModel->getAvailableStaff($startDate, $endDate, 'TaiXe');

        $this->render('pages/admin/form_sua_lich', [
            'lich' => $lich,
            'tours' => $tours,
            'assignedStaff' => $assignedStaff,
            'listHDV' => $listHDV,     
            'listTaiXe' => $listTaiXe  
        ]);
    }

    public function updateSchedule()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];

            $currentLich = $this->lichModel->getDetail($id);
            if (!$currentLich) {
                die("Lỗi: Không tìm thấy lịch trình!");
            }

            $soChoMoi = $_POST['so_cho_toi_da'];
            $soChoDaDat = $currentLich['so_cho_da_dat'];

            if ($soChoMoi < $soChoDaDat) {
                $errorMsg = "Không thể giảm số chỗ xuống $soChoMoi vì đã có $soChoDaDat khách đặt!";
                header("Location: index.php?action=admin-schedule-staff&id=$id&error=" . urlencode($errorMsg));
                return;
            }
            $oldStartDate = date('Y-m-d', strtotime($currentLich['ngay_khoi_hanh']));
            $oldEndDate = date('Y-m-d', strtotime($currentLich['ngay_ket_thuc']));

            $newStartTime = $_POST['gio_khoi_hanh'];
            $newEndTime = $_POST['gio_ket_thuc'];

            $finalStart = $oldStartDate . ' ' . $newStartTime . ':00';
            $finalEnd = $oldEndDate . ' ' . $newEndTime . ':00';

            $data = [
                'ngay_khoi_hanh' => $finalStart,
                'ngay_ket_thuc' => $finalEnd,
                'so_cho_toi_da' => $soChoMoi,
                'diem_tap_trung' => $_POST['diem_tap_trung'],
                'trang_thai' => $_POST['trang_thai']
            ];

            if ($this->lichModel->updateScheduleInfo($id, $data)) {
                $msg = 'updated';
                header("Location: index.php?action=admin-schedule-staff&id=$id&msg=$msg");
            } else {
                $msg = 'Lỗi hệ thống, vui lòng thử lại';
                header("Location: index.php?action=admin-schedule-staff&id=$id&error=" . urlencode($msg));
            }
        }
    }

    public function addStaff()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $lichId = $_POST['lich_id'];
            $nhanVienId = $_POST['nhan_vien_id'];
            $vaiTro = $_POST['vai_tro'];

            $lich = $this->lichModel->getDetail($lichId);
            if (!$lich) {
                echo "<script>alert('Lỗi: Lịch trình không tồn tại!'); window.history.back();</script>";
                return;
            }

            $assigned = $this->lichModel->getAssignedStaff($lichId);

            foreach ($assigned as $a) {
                if ($a['nhan_vien_id'] == $nhanVienId) {
                    echo "<script>alert('Lỗi: Nhân sự này ĐÃ CÓ TRONG ĐOÀN này rồi!'); window.history.back();</script>";
                    return;
                }
            }

            $busyTour = $this->lichModel->checkStaffAvailability(
                $nhanVienId,
                $lich['ngay_khoi_hanh'],
                $lich['ngay_ket_thuc']
            );

            if ($busyTour) {
                $tenTour = $busyTour['ten_tour'];
                $start = date('d/m H:i', strtotime($busyTour['ngay_khoi_hanh']));
                $end = date('d/m H:i', strtotime($busyTour['ngay_ket_thuc']));

                echo "<script>
                    alert('KHÔNG THỂ PHÂN CÔNG!\\n\\nNhân sự này đang bận đi tour: \"$tenTour\"\\nThời gian: Từ $start đến $end.\\n\\nVui lòng chọn nhân sự khác.'); 
                    window.history.back();
                </script>";
                return;
            }

            if ($vaiTro === 'HDV_chinh') {
                foreach ($assigned as $a) {
                    if ($a['vai_tro'] === 'HDV_chinh') {
                        echo "<script>alert('Lỗi: Đoàn này đã có HDV Chính rồi! Bạn chỉ có thể thêm HDV Phụ.'); window.history.back();</script>";
                        return;
                    }
                }
            }

            if ($this->lichModel->assignStaff($lichId, $nhanVienId, $vaiTro)) {
                $msg = 'Staff assigned successfully';
                header("Location: index.php?action=admin-schedule-staff&id=$lichId&msg=$msg");
            } else {
                echo "<script>alert('Lỗi hệ thống, không thể lưu dữ liệu!'); window.history.back();</script>";
            }
        }
    }

    public function removeStaff()
    {
        $id = $_GET['id'] ?? 0;
        $lichId = $_GET['lich_id'] ?? 0;

        if ($this->lichModel->unassignStaff($id)) {
            $msg = 'Staff removed';
        } else {
            $msg = 'Error removing staff';
        }
        header("Location: index.php?action=admin-schedule-staff&id=$lichId&msg=$msg");
    }

    private function uploadImage($file)
    {
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

    public function detailLich()
    {
        $id = $_GET['id'] ?? 0;

        $lich = $this->lichModel->getDetail($id);
        if (!$lich) die("Lịch trình không tồn tại!");

        $staff = $this->lichModel->getAssignedStaff($id);

        $bookingModel = new BookingModel();
        $bookings = $bookingModel->getAllBookings(['lich_id' => $id]);

        $passengers = $bookingModel->getPassengersByLich($id);

        $this->render('pages/admin/tours/chi_tiet_lich', [
            'lich' => $lich,
            'staff' => $staff,
            'bookings' => $bookings,
            'passengers' => $passengers
        ]);
    }

    public function checkAvailabilityApi()
    {
        $ngay_di = $_POST['ngay_di'] ?? '';
        $ngay_ve = $_POST['ngay_ve'] ?? '';

        if (empty($ngay_di) || empty($ngay_ve)) {
            echo json_encode(['hdv' => [], 'taixe' => []]);
            exit;
        }
        $ds_hdv = $this->lichModel->getAllStaffWithStatus('HDV', $ngay_di, $ngay_ve);
        $ds_taixe = $this->lichModel->getAllStaffWithStatus('TaiXe', $ngay_di, $ngay_ve);

        header('Content-Type: application/json');
        echo json_encode([
            'hdv' => $ds_hdv,
            'taixe' => $ds_taixe
        ]);
        exit;
    }
}
