<?php
require_once '../app/dao/PanelDAO.php';

class PanelModel {
  public $panelDAO;

  public function __construct()
  {
    $this->panelDAO = new PanelDAO();
  }

  // Obtém conteúdo a ser exibido no painel principal
  public function getContentPanel()
  {
    $result = [];
    return $result;
  }

  // Obtém receitas e despesas do usuário
  public function get_transactions($user_id)
  {
    $result = $this->panelDAO->get_transactions_db($user_id);

    if (empty($result)) {
      Logger::log(['method' => 'PanelModel->get_transactions', 'result' => $result ], 'alert');
    }

    return $result;
  }

  // Obtém todas as contas cadastradas do usuário
  public function get_accounts($user_id)
  {
    $result = $this->panelDAO->get_accounts_db($user_id);

    if (empty($result)) {
      Logger::log(['method' => 'PanelModel->get_accounts', 'result' => $result ], 'alert');
    }

    return $result;
  }

  // Obtém todas as categorias cadastradas do usuário
  public function get_categories($user_id)
  {
    $result = $this->panelDAO->get_categories_db($user_id);

    if (empty($result)) {
      Logger::log(['method' => 'PanelModel->get_categories', 'result' => $result ], 'alert');
    }

    return $result;
  }

  // Adiciona receita
  public function add_income($user_id, $income)
  {
    $result = $this->panelDAO->add_income_db($user_id, $income);

    if (empty($result)) {
      Logger::log(['method' => 'PanelModel->add_income', 'result' => $result ], 'error');
      return false;
    }

    return true;
  }
  
  // Adiciona despesa
  public function add_expense($user_id, $expense)
  {
    $result = $this->panelDAO->add_expense_db($user_id, $expense);

    if (empty($result)) {
      Logger::log(['method' => 'PanelModel->add_expense', 'result' => $result ], 'error');
      return false;
    }

    return true;
  }

  // Apaga transação
  public function delete_transaction($user_id, $transaction)
  {
    $result = $this->panelDAO->delete_transaction_db($user_id, $transaction);

    if (empty($result)) {
      Logger::log(['method' => 'PanelModel->delete_transaction', 'result' => $result ], 'error');
      return false;
    }

    return true;
  }

  // Adiciona nova conta, se ainda não existir
  public function add_account($user_id, $account)
  {
    $get_account = $this->panelDAO->get_accounts_db($user_id, $account);

    if ($get_account) {
      Logger::log(['method' => 'PanelModel->add_account', 'result' => $get_account ], 'alert');
      return false;
    }

    $this->panelDAO->add_account_db($user_id, $account);
    return true;
  }

  // Adiciona nova categoria, se ainda não existir
  public function add_category($user_id, $category)
  {
    $get_category = $this->panelDAO->get_categories_db($user_id, $category);

    if ($get_category) {
      Logger::log(['method' => 'PanelModel->add_category', 'result' => $get_category ], 'alert');
      return false;
    }
    
    $this->panelDAO->add_category_db($user_id, $category);
    return true;
  }

  // Verifica se o usuário existe na tabela de usuários
  public function check_user_exists($user_id)
  {
    $check_user = $this->panelDAO->check_user_db($user_id);
    $response = ['success' => 'Usuário existe'];

    if (empty($check_user)) {
      $response = ['error_user' => 'Usuário não existe na tabela users'];
      Logger::log(['method' => 'PanelModel->check_user_exists', 'result' => $check_user ], 'error');
    }

    return $response;
  }
}