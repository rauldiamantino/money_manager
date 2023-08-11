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

  public function get_accounts($user_id)
  {
    $result = $this->panelDAO->get_accounts_db($user_id);
    return $result;
  }

  public function get_categories($user_id)
  {
    $result = $this->panelDAO->get_categories_db($user_id);
    return $result;
  }

  public function add_income($user_id, $income)
  {
    $result = $this->panelDAO->add_income_db($user_id, $income);
    return $result;
  }

  public function add_account($user_id, $account)
  {
    $get_account = $this->panelDAO->get_accounts_db($user_id, $account);
    $response = ['error_account' => 'Conta já cadastrada'];

    if (empty($get_account)) {
      $account = $this->panelDAO->add_account_db($user_id, $account);
      $response = ['success' => 'Conta cadastrada com sucesso!', 'account' => $account ];
    }

    return $response;
  }

  public function add_category($user_id, $category)
  {
    $get_category = $this->panelDAO->get_categories_db($user_id, $category);
    $response = ['error_category' => 'Categoria já cadastrada'];

    if (empty($get_category)) {
      $category = $this->panelDAO->add_category_db($user_id, $category);
      $response = ['success' => 'Categoria cadastrada com sucesso!', 'category' => $category ];
    }

    return $response;
  }
}