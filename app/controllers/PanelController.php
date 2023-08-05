<?php

class PanelController
{
  private $panelModel;

  public function __construct()
  {
    require_once '../app/models/PanelModel.php';
    $this->panelModel = new PanelModel();
  }

  public function display()
  {
    $user_id = $_SESSION['user']['user_id'] ?? '';
    $user_first_name = $_SESSION['user']['user_first_name'] ?? '';
    $user_last_name = $_SESSION['user']['user_last_name'] ?? '';
    $user_email = $_SESSION['user']['user_email'] ?? '';
    $contentHome = $this->panelModel->getContentPanel();

    if (empty($_SESSION['user'])) {
      header('Location: ' . $this->base_uri . '../users/login');
      exit();
    }

    if ($_POST['logout']) {
      unset($_SESSION['user']);
      session_destroy();

      header('Location: ' . $this->base_uri . '../users/login');
      exit();
    }

    require_once '../app/views/panel.php';
  }
}
