<?php

require_once '../app/dao/IncomesDAO.php';

class IncomesModel {

  public function getAllIncomes()
  {
    $incomesDAO = new IncomesDAO();
    $result = $incomesDAO->getAllIncomes();
    return $result;
  }
}