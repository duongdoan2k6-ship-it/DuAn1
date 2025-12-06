<?php
// routers.index.php
$projectRoot = dirname(__DIR__);

require_once $projectRoot . '/controllers/BaseController.php';
require_once $projectRoot . '/controllers/HomeController.php';
require_once $projectRoot . '/controllers/TourController.php';
require_once $projectRoot . '/controllers/PersonController.php';
require_once $projectRoot . '/controllers/BaocaoController.php';
require_once $projectRoot . '/controllers/BookingController.php';
require_once $projectRoot . '/controllers/GuideController.php';


require_once $projectRoot . '/models/BaseModel.php';
require_once $projectRoot . '/models/TourModel.php';
require_once $projectRoot . '/models/PersonModel.php';
require_once $projectRoot . '/models/BaocaoModel.php';
require_once $projectRoot . '/models/BookingModel.php';


$action = $_GET['action'] ?? '/';

match ($action) {
    '/'     => (new TourController)->index(),

    // Tour
    'list-tours'     => (new TourController)->index(),
    'add-tour'       => (new TourController)->add(),
    'edit-tour'      => (new TourController)->edit(),
    // 'delete-tour'    => (new TourController)->delete(),
    'detail-tour'    => (new TourController)->detail(),

    // Hướng Dẫn Viên (Guide)
    'guide-list-guests' => (new GuideController)->danhSachKhach(),

    // Person
    'person'         => (new PersonController)->index(),
    'delete-person'  => (new PersonController)->delete(),
    'formAddPerson'  => (new PersonController)->formAddPerson(),
    'addPerson'      => (new PersonController)->addPerson(),
    'editPerson'     => (new PersonController)->editPerson(),
    'updatePerson'   => (new PersonController)->updatePerson(),

    // Báo cáo
    'list-baocao'    => (new BaocaoController)->index(),
    'add-baocao'     => (new BaocaoController)->create(),
    'edit-baocao'    => (new BaocaoController)->edit(),
    'update-baocao'  => (new BaocaoController)->update(),
    'delete-baocao'  => (new BaocaoController)->delete(),
    'detail-baocao'  => (new BaocaoController)->detail(),
    'export-baocao'  => (new BaocaoController)->exportCsv(),

    // Booking
    'list-booking'   => (new BookingController)->index(),
    'add-booking'    => (new BookingController)->add(), 
    'store-booking'    => (new BookingController)->store(), 
    'edit-booking'   => (new BookingController)->edit(), 
    'update-booking' => (new BookingController)->update(),
    'cancel-booking' => (new BookingController)->cancel(),
    'detail-booking' => (new BookingController)->detail(),
    'delete-booking' => (new BookingController)->delete(),

    //


    default          => (new TourController)->index(),
};
