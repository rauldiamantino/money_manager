<?php
require_once '../app/dao/UsersDAO.php';

class UsersModel {

  public function get_data_register($data)
  {
    $financesDAO = new UsersDAO();
    // $result = $financesDAO->get_users_db();
    $result = $data;
    return $result;
  }
}