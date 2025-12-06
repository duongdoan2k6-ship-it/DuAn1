<?php

class TourController extends BaseController
{
    public function index()
    {

        $tourModel = new TourModel();
        $allTours = $tourModel->getAllTours();
        $data = [
            'tours' => $allTours,
            'pageTitle' => 'Quản lý Danh sách Tour'
        ];
        $this->renderView('pages/admin/tours/list_tour.php', $data);
    }

    public function add()
    {
        $tourModel = new TourModel();
        $conn = $tourModel->getConnection();

        $dsLoaiTour = $tourModel->getAllLoaiTour();
        $dsNhaCungCap = $tourModel->getAllNhaCungCap();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $tenTour     = $_POST['ten_tour'];
            $maLoaiTour  = $_POST['ma_loai_tour'];
            $thoiLuong   = $_POST['thoi_luong'];
            $giaTour     = $_POST['gia_tour'];
            $diaDiem     = $_POST['dia_diem'];
            $moTa        = $_POST['mo_ta'];
            $trangThai   = $_POST['trang_thai'];

            // INSERT TOUR
            $isInserted = $tourModel->insert(
                $tenTour,
                $maLoaiTour,
                $thoiLuong,
                $giaTour,
                $diaDiem,
                $moTa,
                $trangThai
            );

            if ($isInserted) {
                // Lấy ID tour vừa thêm
                $maTour = $conn->lastInsertId();
                if (!$maTour) {
                    die("Lỗi: Không lấy được ID tour vừa thêm.");
                }

                /** ================= 1) LỊCH TRÌNH ================== */
                if (!empty($_POST['lich_trinh'])) {
                    foreach ($_POST['lich_trinh'] as $lt) {
                        if (trim($lt['tieu_de']) == "") continue;

                        $sqlLT = "INSERT INTO lichtrinhtour (MaTour, SoNgay, TieuDe, HoatDong)
                              VALUES (:ma, :ngay, :tieu_de, :hoatdong)";
                        $stmtLT = $conn->prepare($sqlLT);
                        $stmtLT->execute([
                            ':ma'       => $maTour,
                            ':ngay'     => $lt['so_ngay'],
                            ':tieu_de'  => $lt['tieu_de'],
                            ':hoatdong' => $lt['hoat_dong']
                        ]);
                    }
                }

                /** ================= 2) CHÍNH SÁCH ================== */
                if (!empty($_POST['chinh_sach'])) {
                    foreach ($_POST['chinh_sach'] as $cs) {
                        $sqlCS = "INSERT INTO chinhsachtour (MaTour, TenChinhSach, NoiDungChinhSach) 
                              VALUES (:ma, :ten, :nd)";
                        $stmtCS = $conn->prepare($sqlCS);
                        $stmtCS->execute([
                            ':ma'  => $maTour,
                            ':ten' => $cs['ten'],
                            ':nd'  => $cs['noi_dung']
                        ]);
                    }
                }

                /** ================= 3) NHÀ CUNG CẤP ================== */
                if (!empty($_POST['nha_cung_cap'])) {
                    foreach ($_POST['nha_cung_cap'] as $ncc) {
                        $sqlNCC = "INSERT INTO tournhacungcap (MaTour, MaNhaCungCap)
                               VALUES (:ma, :ncc)";
                        $stmtNCC = $conn->prepare($sqlNCC);
                        $stmtNCC->execute([
                            ':ma'  => $maTour,
                            ':ncc' => $ncc
                        ]);
                    }
                }

                /** ================= HÌNH ẢNH ================== */
                if (!empty($_FILES['hinh_anh']['name'][0])) {

                    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/Imagetour/'; // Đường dẫn vật lý đầy đủ

                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    foreach ($_FILES['hinh_anh']['name'] as $i => $fileName) {

                        if ($_FILES['hinh_anh']['error'][$i] !== 0) continue;

                        $tmpName = $_FILES['hinh_anh']['tmp_name'][$i];
                        $newName = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', basename($fileName));
                        $filePath = $uploadDir . $newName;

                        if (move_uploaded_file($tmpName, $filePath)) {

                            // Lưu đường dẫn tương đối vào DB
                            $dbPath = '/Imagetour/' . $newName;

                            $sql = "INSERT INTO hinhanhtour (MaTour, URLHinhAnh, ChuThich)
                    VALUES (:maTour, :url, '')";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([
                                ':maTour' => $maTour,
                                ':url'    => $dbPath
                            ]);
                        }
                    }
                    // Debug sau khi upload
                    echo "Đường dẫn lưu trong DB: $dbPath<br>";
                    echo "File tồn tại: " . (file_exists($filePath) ? 'Có' : 'Không') . "<br>";
                }




                // Chuyển hướng về danh sách tour
                header("Location: index.php?action=list-tours");
                exit();
            }
        }

        $data = [
            'dsLoaiTour'    => $dsLoaiTour,
            'dsNhaCungCap'  => $dsNhaCungCap,
            'pageTitle'     => 'Thêm Tour Mới'
        ];

        $this->renderView('pages/admin/tours/add_tour.php', $data);
    }

    public function edit()
    {
        if (!isset($_GET['id'])) {
            die("Thiếu ID tour cần sửa");
        }

        $maTour = $_GET['id'];
        $tourModel = new TourModel();
        $conn = $tourModel->getConnection();

        // Lấy dữ liệu cho dropdown
        $dsLoaiTour = $tourModel->getAllLoaiTour();
        $dsNhaCungCap = $tourModel->getAllNhaCungCap();

        // Lấy chi tiết tour (bao gồm lịch trình, ảnh, chính sách, NCC)
        $tourDetail = $tourModel->getTourDetail($maTour);

        if (!$tourDetail) {
            die("Không tìm thấy tour này");
        }

        // Xử lý khi submit form
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 1. Cập nhật thông tin cơ bản của tour
            $tenTour = $_POST['ten_tour'];
            $maLoaiTour = $_POST['ma_loai_tour'];
            $thoiLuong = $_POST['thoi_luong'];
            $giaTour = $_POST['gia_tour'];
            $trangThai = $_POST['trang_thai'];
            $diaDiem = $_POST['dia_diem'];
            $moTa = $_POST['mo_ta'];

            $isUpdated = $tourModel->updateTour(
                $maTour,
                $tenTour,
                $maLoaiTour,
                $thoiLuong,
                $giaTour,
                $diaDiem,
                $moTa,
                $trangThai
            );

            if ($isUpdated) {
                /** ================= 1) XÓA & CẬP NHẬT LỊCH TRÌNH ================== */
                // Xóa lịch trình cũ
                $sqlDeleteLT = "DELETE FROM lichtrinhtour WHERE MaTour = :ma";
                $stmt = $conn->prepare($sqlDeleteLT);
                $stmt->execute([':ma' => $maTour]);

                // Thêm lịch trình mới
                if (!empty($_POST['lich_trinh'])) {
                    foreach ($_POST['lich_trinh'] as $lt) {
                        if (trim($lt['tieu_de']) == "") continue;

                        $sqlLT = "INSERT INTO lichtrinhtour (MaTour, SoNgay, TieuDe, HoatDong)
                              VALUES (:ma, :ngay, :tieu_de, :hoatdong)";
                        $stmtLT = $conn->prepare($sqlLT);
                        $stmtLT->execute([
                            ':ma'       => $maTour,
                            ':ngay'     => $lt['so_ngay'],
                            ':tieu_de'  => $lt['tieu_de'],
                            ':hoatdong' => $lt['hoat_dong']
                        ]);
                    }
                }

                /** ================= 2) XÓA & CẬP NHẬT CHÍNH SÁCH ================== */
                // Xóa chính sách cũ
                $sqlDeleteCS = "DELETE FROM chinhsachtour WHERE MaTour = :ma";
                $stmt = $conn->prepare($sqlDeleteCS);
                $stmt->execute([':ma' => $maTour]);

                // Thêm chính sách mới
                if (!empty($_POST['chinh_sach'])) {
                    foreach ($_POST['chinh_sach'] as $cs) {
                        $sqlCS = "INSERT INTO chinhsachtour (MaTour, TenChinhSach, NoiDungChinhSach) 
                              VALUES (:ma, :ten, :nd)";
                        $stmtCS = $conn->prepare($sqlCS);
                        $stmtCS->execute([
                            ':ma'  => $maTour,
                            ':ten' => $cs['ten'],
                            ':nd'  => $cs['noi_dung']
                        ]);
                    }
                }

                /** ================= 3) XÓA & CẬP NHẬT NHÀ CUNG CẤP ================== */
                // Xóa NCC cũ
                $sqlDeleteNCC = "DELETE FROM tournhacungcap WHERE MaTour = :ma";
                $stmt = $conn->prepare($sqlDeleteNCC);
                $stmt->execute([':ma' => $maTour]);

                // Thêm NCC mới
                if (!empty($_POST['nha_cung_cap'])) {
                    foreach ($_POST['nha_cung_cap'] as $nccId) {
                        $sqlNCC = "INSERT INTO tournhacungcap (MaTour, MaNhaCungCap)
                               VALUES (:ma, :ncc)";
                        $stmtNCC = $conn->prepare($sqlNCC);
                        $stmtNCC->execute([
                            ':ma'  => $maTour,
                            ':ncc' => $nccId
                        ]);
                    }
                }

                /** ================= 4) THÊM HÌNH ẢNH MỚI (nếu có) ================== */
                if (!empty($_FILES['hinh_anh']['name'][0])) {
                    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/Imagetour/';

                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    foreach ($_FILES['hinh_anh']['name'] as $i => $fileName) {
                        if ($_FILES['hinh_anh']['error'][$i] !== 0) continue;

                        $tmpName = $_FILES['hinh_anh']['tmp_name'][$i];
                        $newName = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', basename($fileName));
                        $filePath = $uploadDir . $newName;

                        if (move_uploaded_file($tmpName, $filePath)) {
                            $dbPath = '/Imagetour/' . $newName;

                            $sql = "INSERT INTO hinhanhtour (MaTour, URLHinhAnh, ChuThich)
                                VALUES (:maTour, :url, '')";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([
                                ':maTour' => $maTour,
                                ':url'    => $dbPath
                            ]);
                        }
                    }
                }

                // Chuyển hướng về danh sách
                header("Location: index.php?action=list-tours");
                exit();
            } else {
                echo "Lỗi khi cập nhật tour.";
            }
        }

        // Lấy danh sách NCC đã chọn cho tour này
        $sqlSelectedNCC = "SELECT MaNhaCungCap FROM tournhacungcap WHERE MaTour = :ma";
        $stmt = $conn->prepare($sqlSelectedNCC);
        $stmt->execute([':ma' => $maTour]);
        $selectedNCC = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $data = [
            'dsLoaiTour'    => $dsLoaiTour,
            'dsNhaCungCap'  => $dsNhaCungCap,
            'selectedNCC'   => $selectedNCC, // Thêm mảng chứa các NCC đã chọn
            'tour'          => $tourDetail,
            'pageTitle'     => 'Chỉnh sửa Tour'
        ];

        $this->renderView('pages/admin/tours/edit_tour.php', $data);
    }

    public function detail()
    {
        if (!isset($_GET['id'])) die("Thiếu ID tour");

        $maTour = $_GET['id'];
        $tourModel = new TourModel();

        $tourDetail = $tourModel->getTourDetail($maTour);

        if (!$tourDetail) die("Không tìm thấy tour");

        $data = [
            'pageTitle' => 'Chi tiết Tour',
            'tour' => $tourDetail,
        ];

        $this->renderView('pages/admin/tours/detail_tour.php', $data);
    }
}
