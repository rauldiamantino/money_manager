<?php

require_once '../app/dao/FinancesDAO.php';

class FinancesModel {

  public function get_finances()
  {
    $user = 0;
    $financesDAO = new FinancesDAO();
    $result = $financesDAO->get_finances_db($user);
    return $result;
  }
}