<?php
class IncomesController {
  private $IncomesModel;
  
  public function __construct()
  {
    require_once '../app/models/IncomesModel.php';

    $this->incomesModel = new IncomesModel();
  }

  public function display_incomes()
  {
    $incomes = $this->incomesModel->getAllIncomes();
    
    require_once '../app/views/incomes.php';
  }
}