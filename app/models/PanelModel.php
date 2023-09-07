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
      Logger::log('PanelModel->get_transactions: Erro ao buscar transações');
    }

    return $result;
  }

  // Obtém todas as contas cadastradas do usuário
  public function get_accounts($user_id)
  {
    $result = $this->panelDAO->get_accounts_db($user_id);

    if (empty($result)) {
      Logger::log('PanelModel->get_accounts: Erro ao buscar contas');
    }

    return $result;
  }

  // Obtém todas as categorias cadastradas do usuário
  public function get_categories($user_id)
  {
    $result = $this->panelDAO->get_categories_db($user_id);
    return $result;
  }

  // Adiciona receita
  public function add_income($user_id, $income)
  {
    $result = $this->panelDAO->add_income_db($user_id, $income);
    $response = ['success' => 'Receita adicionada com sucesso!'];

    if (empty($result)) {
      $response = ['error_income' => 'Erro ao cadastrar receita'];
      Logger::log('PanelModel->add_income: ' . $response['error_income']);
    }

    return $response;
  }
  
  // Adiciona despesa
  public function add_expense($user_id, $expense)
  {
    $result = $this->panelDAO->add_expense_db($user_id, $expense);
    $response = ['success' => 'Despesa adicionada com sucesso!'];

    if (empty($result)) {
      $response = ['error_expense' => 'Erro ao cadastrar despesa'];
      Logger::log('PanelModel->add_expense: ' . $response['error_expense']);

    }

    return $response;
  }

  // Adiciona nova conta, se ainda não existir
  public function add_account($user_id, $account)
  {
    $get_account = $this->panelDAO->get_accounts_db($user_id, $account);
    $response = ['success' => 'Conta cadastrada com sucesso!', 'account' => $account ];

    if ($get_account) {
      $response = ['error_account' => 'Conta já cadastrada'];
      Logger::log('PanelModel->add_account: ' . $response['error_account']);
    }
    else {
      $account = $this->panelDAO->add_account_db($user_id, $account);
    }

    return $response;
  }

  // Adiciona nova categoria, se ainda não existir
  public function add_category($user_id, $category)
  {
    $get_category = $this->panelDAO->get_categories_db($user_id, $category);
    $response = ['success' => 'Categoria cadastrada com sucesso!', 'category' => $category ];

    if ($get_category) {
      $response = ['error_category' => 'Categoria já cadastrada'];
      Logger::log('PanelModel->add_category: ' . $response['error_category']);
    }
    else {
      $category = $this->panelDAO->add_category_db($user_id, $category);
    }

    return $response;
  }

  // Verifica se o usuário existe na tabela de usuários
  public function check_user_exists($user_id)
  {
    $check_user = $this->panelDAO->check_user_db($user_id);
    $response = ['success' => 'Usuário existe'];

    if (empty($check_user)) {
      $response = ['error_user' => 'Usuário não existe na tabela users'];
      Logger::log('PanelModel->check_user_exists: ' . $response['error_user']);
    }

    return $response;

  }
}