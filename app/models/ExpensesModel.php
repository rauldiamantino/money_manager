<?php

require_once '../app/dao/ExpensesDAO.php';

class ExpensesModel {

  public function getAllExpenses()
  {
    $expensesDAO = new ExpensesDAO();
    $result = $expensesDAO->getAllExpenses();
    return $result;
  }
}