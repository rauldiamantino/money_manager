<?php
require_once '../app/models/HomeModel.php';
require_once '../app/helpers/ViewRenderes.php';

class HomeController
{
  private $homeModel;

  public function __construct()
  {
    $this->homeModel = new HomeModel();
  }

  public function index()
  {
    $content_home = $this->homeModel->getContentHome();
    ViewRenderer::render('home', ['content_home' => $content_home]);
  }
}
