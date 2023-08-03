<?php
class FinancesController {
  protected $financesModel;

  public function __construct()
  {
    require_once '../app/models/FinancesModel.php';
    $this->financesModel = new FinancesModel();
  }

  public function display($data = 0)
  {
    $user = $data ?? 0;
    $finances = $this->financesModel->get_finances($user);
    require_once '../app/views/finances.php';
  }
}