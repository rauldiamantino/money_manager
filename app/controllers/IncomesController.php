<?php
require_once '../app/controllers/FinancesController.php';

class IncomesController extends FinancesController {

  public function display_incomes()
  {
    $incomes = $this->incomesModel->getAllIncomes();
    require_once '../app/views/incomes.php';
  }
}