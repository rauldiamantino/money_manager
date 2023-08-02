<?php
class ExpensesController {
  private $expensesModel;
  
  public function __construct()
  {
    require_once '../app/models/ExpensesModel.php';

    $this->expensesModel = new ExpensesModel();
  }

  public function display_expenses()
  {
    $expenses = $this->expensesModel->getAllExpenses();
    
    require_once '../app/views/expenses.php';
  }
}