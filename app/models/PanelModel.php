<?php
require_once '../app/dao/PanelDAO.php';
require_once '../app/dao/UsersDAO.php';
require_once '../app/helpers/Logger.php';

class PanelModel {
  public $panelDAO;
  public $usersDAO;

  public function __construct()
  {
    $this->panelDAO = new PanelDAO();
    $this->usersDAO = new UsersDAO();
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
    return $result;
  }

  // Obtém todas as contas cadastradas do usuário
  public function get_accounts($user_id)
  {
    $result = $this->panelDAO->get_accounts_db($user_id);
    return $result;
  }

  // Obtém todas as categorias cadastradas do usuário
  public function get_categories($user_id)
  {
    $result = $this->panelDAO->get_categories_db($user_id);
    return $result;
  }

  // Obtém os dados da conta do usuário
  public function get_myaccount($user_id)
  {
    $result = $this->panelDAO->get_myaccount_db($user_id);
    return $result;
  }

  // Atualiza os dados da conta do usuário
  public function update_myaccount($new_data)
  {
    $result = $this->usersDAO->update_users_db($new_data);
    $response = ['success_update' => 'Cadastro atualizado com sucesso!'];

    if (empty($result)) {
      $response = ['error_update' => 'Erro ao atualizar cadastro'];
    }

    return $response;
  }

  // Atualiza senha do conta do usuário
  public function update_myaccount_password($new_data)
  {
    $get_user = $this->get_user($new_data['user_id']);
    $response = ['error_update' => 'Erro ao atualizar cadastro'];

    if ($get_user and $this->usersDAO->update_password_user_db($new_data)) {
      $response = ['success_update' => 'Cadastro e Senha atualizados com sucesso!'];
    }

    return $response;
  }

  // Busca usuário no Banco de Dados
  private function get_user($user_id)
  {
    $email = '';
    $response = $this->usersDAO->get_user_db($email, $user_id);
    return $response;
  }

  // Adiciona receita
  public function add_income($user_id, $income)
  {
    $result = $this->panelDAO->add_income_db($user_id, $income);
    $response = ['success' => 'Receita adicionada com sucesso!'];

    if (empty($result)) {
      $response = ['error_income' => 'Erro ao cadastrar receita'];
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
    }

    return $response;
  }

  // Adiciona nova conta, se ainda não existir
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

  // Adiciona nova categoria, se ainda não existir
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

  // Verifica se o usuário existe na tabela de usuários
  public function check_user_exists($user_id)
  {
    $check_user = $this->panelDAO->check_user_db($user_id);
    $response = ['success' => 'Usuário existe'];

    if (empty($check_user)) {
      $response = ['error_user' => 'Usuário não existe na tabela users'];
      Logger::log($response['error_user']);
    }

    return $response;

  }
}