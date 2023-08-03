<?php
class FinancesController {
  protected $incomesModel;
  protected $expensesModel;
  protected $financesModel;

  public function __construct()
  {
    require_once '../app/models/IncomesModel.php';
    require_once '../app/models/ExpensesModel.php';
    require_once '../app/models/FinancesModel.php';

    $this->incomesModel = new IncomesModel();
    $this->expensesModel = new ExpensesModel();
    $this->financesModel = new FinancesModel();
  }

  public function display_combined()
  {
    $combined = $this->financesModel->getFinancesCombined();

    require_once '../app/views/combined.php';
  }
}