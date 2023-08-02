<?php
class ExpensesController {
  private $expensesModel;
  
  public function __construct()
  {
    require_once '../app/models/Expense.php';

    $this->expensesModel = new Expense();
  }

  public function display_expenses()
  {
    $expenses = $this->expensesModel->getAllExpenses();
    
    require_once '../app/views/expenses.php';
  }
}