<?php

require_once '../app/dao/FinancesDAO.php';

class FinancesModel {

  public function getFinancesCombined()
  {
    $financesDAO = new FinancesDAO();
    $result = $financesDAO->getFinancesCombinedDb();
    return $result;
  }
}