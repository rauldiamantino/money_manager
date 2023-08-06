<?php
require_once '../app/dao/PanelDAO.php';

class PanelModel {

  public function getContentPanel()
  {
    $result = [];
    return $result;
  }

  public function getTransactions($user_id)
  {
    $panelDAO = new PanelDAO();
    $result = $panelDAO->get_transactions_db($user_id);
    return $result;
  }
}