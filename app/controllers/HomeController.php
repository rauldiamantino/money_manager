<?php
require_once '../app/models/HomeModel.php';

class HomeController
{
  private $homeModel;

  public function __construct()
  {
    $this->homeModel = new HomeModel();
  }

  public function index()
  {
    // View e conteúdo para a home
    $view_name = 'home';
    $view_content = $this->homeModel->getContentHome();

    return [ $view_name => $view_content ];
  }
}
