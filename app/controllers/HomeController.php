<?php

class HomeController
{
  private $homeModel;

  public function __construct()
  {
    require_once '../app/models/HomeModel.php';
    $this->homeModel = new HomeModel();
  }

  public function index()
  {
    $contentHome = $this->homeModel->getContentHome();
    require_once '../app/views/home.php';
  }
}
