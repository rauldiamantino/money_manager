<?php

require_once '../app/dao/ExpenseDAO.php';

class Expense {
  // Propriedades e métodos que vão interagir com o BD

  public function getAllExpenses()
  {
    $expensesDAO = new ExpenseDAO();
    $result = $expensesDAO->getAllExpenses();
    return $result;
  }
}