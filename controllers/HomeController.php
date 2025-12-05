<?php

class HomeController extends BaseController
{
   public function index() 
    {
        $this->renderView('pages/tours/main.php');
    }
}