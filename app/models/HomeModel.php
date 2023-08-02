<?php

require_once '../app/dao/HomeDAO.php';

class HomeModel {

  public function getContentHome()
  {
    $homeDAO = new HomeDAO();
    $result = $homeDAO->contentHome();
    return $result;
  }
}