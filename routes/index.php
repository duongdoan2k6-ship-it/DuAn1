<?php
$projectRoot = dirname(__DIR__);

require_once $projectRoot . '/controllers/BaseController.php';
require_once $projectRoot . '/controllers/HomeController.php';
require_once $projectRoot . '/controllers/TourController.php';
require_once $projectRoot . '/controllers/PersonController.php';

require_once $projectRoot . '/models/BaseModel.php';
require_once $projectRoot . '/models/TourModel.php'; 
require_once $projectRoot . '/models/PersonModel.php'; 

$action = $_GET['action'] ?? '/';

match ($action) {
    '/'     => (new HomeController) -> index(),
    // tours
    'list-tours' => (new TourController) -> index(),
    'add-tour' => (new TourController) -> add(),
    'edit-tour' => (new TourController)->edit(),
    'delete-tour' => (new TourController)->delete(),

    // personal
    'person' => (new PersonController) -> index(),
    'delete' => (new PersonController) -> delete(),
    'formAddPerson' => (new PersonController) -> formAddPerson(),
    'addPerson' => (new PersonController) -> addPerson(),
};