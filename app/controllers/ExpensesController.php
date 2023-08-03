<?php
require_once '../app/controllers/FinancesController.php';

class ExpensesController extends FinancesController {

  public function display_expenses()
  {
    $expenses = $this->expensesModel->getAllExpenses();
    require_once '../app/views/expenses.php';
  }
}