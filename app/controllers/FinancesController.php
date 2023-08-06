<?php
class FinancesController {
  protected $financesModel;

  public function __construct()
  {
    require_once '../app/models/FinancesModel.php';
    $this->financesModel = new FinancesModel();
  }
}