<?php
require_once '../app/dao/PanelDAO.php';

class PanelModel {
  public $panelDAO;

  public function __construct()
  {
    $this->panelDAO = new PanelDAO();
  }

  public function getContentPanel()
  {
    $result = [];
    return $result;
  }

  public function get_transactions($user_id)
  {
    $result = $this->panelDAO->get_transactions_db($user_id);
    return $result;
  }

  public function add_income($user_id, $income)
  {
    $result = $this->panelDAO->add_income_db($user_id, $income);
    return $result;
  }
}