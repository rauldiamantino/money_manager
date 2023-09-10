<?php
require_once '../app/models/PanelModel.php';
require_once '../app/models/UsersModel.php';

class PanelController
{
  public $panelModel;
  public $usersModel;
  public $user_id;
  public $user_first_name;
  public $user_last_name;
  public $user_email;
  public $active_tab;
  public $action_route;

  public function __construct()
  {
    $this->panelModel = new PanelModel();
    $this->usersModel = new UsersModel();

    // Recupera dados de sessão do usuário
    $this->user_id = $_SESSION['user']['user_id'] ?? '';
    $this->user_first_name = $_SESSION['user']['user_first_name'] ?? '';
    $this->user_last_name = $_SESSION['user']['user_last_name'] ?? '';
    $this->user_email = $_SESSION['user']['user_email'] ?? '';
  }

  // Exibe visão geral do painel
  public function display()
  {
    // Valida se o usuário está logado
    if ($this->check_session() or $this->check_logout()) {
      Logger::log(['method' => 'PanelController->display', 'result' => 'Usuario Desconectado'], 'alert');
    }

    $this->active_tab = 'overview';
    $this->action_route = '/panel/display';

    // View e conteúdo para o menu de navegação
    $nav_view_name = 'panel/templates/nav';
    $nav_view_content = [
      'user_id' => $this->user_id,
      'active_tab' => $this->active_tab,
      'action_route' => $this->action_route,
      'user_first_name' => $this->user_first_name,
      'user_last_name' => $this->user_last_name,
    ];

    // View e conteúdo para o painel
    $panel_view_name = 'panel/panel';
    $panel_view_content = [];

    return [ $nav_view_name => $nav_view_content, $panel_view_name => $panel_view_content ];
  }

  // Exibe todas as transações
  public function transactions($user_id)
  {
    // Valida se o usuário está logado
    if ($this->check_session() or $this->check_logout()) {
      Logger::log(['method' => 'PanelController->transactions', 'result' => 'Usuario Desconectado'], 'alert');
    }

    $message = [];

    // Adiciona transações
    if (isset($_POST['add_income'])) {
      $message = $this->add_income($user_id);
    }

    if (isset($_POST['add_expense'])) {
      $message = $this->add_expense($user_id);
    }

    // Apaga transação
    if (isset($_POST['delete_transaction'])) {
      $message = $this->delete_transaction($user_id);
    }

    // Altera status da transação
    if (isset($_POST['edit_transaction_status'])) {
      $message = $this->edit_transaction_status($user_id);
    }

    // Prepara conteúdo para a View
    $this->action_route = 'panel/transactions/' . $user_id;
    $transactions = $this->panelModel->get_transactions($user_id);
    $categories = $this->panelModel->get_categories($user_id);
    $accounts = $this->panelModel->get_accounts($user_id);
    $this->active_tab = 'transactions';

    // View e conteúdo para o menu de navegação
    $nav_view_name = 'panel/templates/nav';
    $nav_view_content = [
      'user_id' => $this->user_id,
      'active_tab' => $this->active_tab,
      'action_route' => $this->action_route,
      'user_first_name' => $this->user_first_name,
      'user_last_name' => $this->user_last_name,
    ];

    // View e conteúdo para a página de transações
    $transactions_view_name = 'panel/transactions';
    $transactions_view_content = [
      'transactions' => $transactions, 
      'user_id' => $this->user_id, 
      'categories' => $categories, 
      'accounts' => $accounts,
      'message' => $message,
    ];

    return [ $nav_view_name => $nav_view_content, $transactions_view_name => $transactions_view_content ];
  }

  // Exibe todas as contas
  public function accounts($user_id)
  {
    // Valida se o usuário está logado
    if ($this->check_session() or $this->check_logout()) {
      Logger::log(['method' => 'PanelController->accounts', 'result' => 'Usuario Desconectado'], 'alert');
    }

    $message = [];

    // Resultado da tentativa de adicionar conta
    if (isset($_POST['account'])) {
      $message = $this->add_account($user_id);
    }

    // Prepara conteúdo para a View
    $this->action_route = 'panel/accounts/' . $user_id;
    $accounts = $this->panelModel->get_accounts($user_id);
    $this->active_tab = 'accounts';

    // View e conteúdo para o menu de navegação
    $nav_view_name = 'panel/templates/nav';
    $nav_view_content = [
      'user_id' => $this->user_id,
      'active_tab' => $this->active_tab,
      'action_route' => $this->action_route,
      'user_first_name' => $this->user_first_name,
      'user_last_name' => $this->user_last_name,
    ];

    // View e conteúdo para a página de contas
    $accounts_view_name = 'panel/accounts';
    $accounts_view_content = [
      'accounts' => $accounts, 
      'user_id' => $this->user_id,
      'message' => $message,
    ];

    return [ $nav_view_name => $nav_view_content, $accounts_view_name => $accounts_view_content ];
  }

  // Exibe todas as categorias
  public function categories($user_id)
  {
    // Valida se o usuário está logado
    if ($this->check_session() or $this->check_logout()) {
      Logger::log(['method' => 'PanelController->categories', 'result' => 'Usuario Desconectado'], 'alert');
    }

    $message = [];

    // Resultado da tentativa de adicionar categoria
    if (isset($_POST['category'])) {
      $message = $this->add_category($user_id);
    }

    // Prepara conteúdo para a View
    $this->action_route = 'panel/categories/' . $user_id;
    $categories = $this->panelModel->get_categories($user_id);
    $this->active_tab = 'categories';

    // View e conteúdo para o menu de navegação
    $nav_view_name = 'panel/templates/nav';
    $nav_view_content = [
      'user_id' => $this->user_id,
      'active_tab' => $this->active_tab,
      'action_route' => $this->action_route,
      'user_first_name' => $this->user_first_name,
      'user_last_name' => $this->user_last_name,
    ];

    // View e conteúdo para a página de categorias
    $categories_view_name = 'panel/categories';
    $categories_view_content = [
      'categories' => $categories, 
      'user_id' => $this->user_id,
      'message' => $message,
    ];

    return [ $nav_view_name => $nav_view_content, $categories_view_name => $categories_view_content ];
  }

  // Recupera receita do formulário e adiciona no banco de dados
  public function add_income($user_id)
  {
    $income = [
      'type' => 'I',
      'status' => 0,
      'date' => $_POST['transaction_date'],
      'transaction_id' => $_POST['add_income'],
      'amount' => $_POST['transaction_amount'],
      'account_id' => $_POST['transaction_account'],
      'category_id' => $_POST['transaction_category'],
      'description' => $_POST['transaction_description'],
    ];

    $response = $this->panelModel->add_income($user_id, $income);
    $message = ['success' => 'Receita adicionada com sucesso!'];

    if ($income['transaction_id']) {
      $message = ['success' => 'Receita editada com sucesso!'];
    }

    if ($response == false) {
      $message = ['error_income' => 'Erro ao cadastrar receita'];
      Logger::log(['method' => 'PanelController->add_income', 'result' => $response ], 'error');
    }

    return $message;
  }

  // Recupera despesa do formulário e adiciona no banco de dados
  public function add_expense($user_id)
  {
    $expense = [
      'type' => 'E',
      'status' => 0,
      'date' => $_POST['transaction_date'],
      'transaction_id' => $_POST['add_expense'],
      'amount' => -1 * $_POST['transaction_amount'],
      'account_id' => $_POST['transaction_account'],
      'category_id' => $_POST['transaction_category'],
      'description' => $_POST['transaction_description'],
    ];

    $response = $this->panelModel->add_expense($user_id, $expense);
    $message = ['success' => 'Despesa adicionada com sucesso!'];

    if ($expense['transaction_id']) {
      $message = ['success' => 'Despesa editada com sucesso!'];
    }

    if ($response == false) {
      $message = ['error_expense' => 'Erro ao cadastrar despesa'];
      Logger::log(['method' => 'PanelController->add_expense', 'result' => $response ], 'error');
    }

    return $message;
  }

  public function delete_transaction($user_id)
  {
    $transaction = [
      'transaction_id' => $_POST['delete_transaction_id'],
      'transaction_type' => $_POST['delete_transaction_type']
    ];

    $response = $this->panelModel->delete_transaction($user_id, $transaction);
    $message = ['success' => 'Transação removida com sucesso!'];

    if ($response == false) {
      $message = ['error_transaction' => 'Erro ao apagar transação'];
      Logger::log(['method' => 'PanelController->delete_transaction', 'result' => $response ], 'error');
    }

    return $message;
  }

  public function edit_transaction_status($user_id)
  {
    $transaction = [
      'transaction_id' => $_POST['edit_transaction_id'],
      'transaction_type' => $_POST['edit_transaction_type'],
      'transaction_status' => intval($_POST['edit_transaction_status']),
    ];
    
    $response = $this->panelModel->edit_transaction_status($user_id, $transaction);
    $message = ['success' => 'Status alterado com sucesso!'];

    if ($response == false) {
      $message = ['error_transaction' => 'Erro ao alterar status da transação'];
      Logger::log(['method' => 'PanelController->edit_transaction_status', 'result' => $response ], 'error');
    }

    return $message;
  }

  // Adiciona conta no banco de dados
  public function add_account($user_id)
  {
    $account = $_POST['account'];
    $response = $this->panelModel->add_account($user_id, $account);
    $message = ['success' => 'Conta cadastrada com sucesso!'];

    if ($response == false) {
      $message = ['error_account' => 'Conta já cadastrada'];
      Logger::log(['method' => 'PanelController->add_account', 'result' => $response ], 'alert');
    }

    return $message;
  }

  // Adiciona categoria no banco de dados
  public function add_category($user_id)
  {
    $category = $_POST['category'];
    $response = $this->panelModel->add_category($user_id, $category);
    $message = ['success' => 'Categoria cadastrada com sucesso!'];

    if ($response == false) {
      $message = ['error_category' => 'Categoria já cadastrada'];
      Logger::log(['method' => 'PanelController->add_category', 'result' => $response ], 'alert');
    }

    return $message;
  }

  // Exibe e altera dados do usuário
  public function myaccount($user_id) 
  {
    // Valida se o usuário está logado
    if ($this->check_session() or $this->check_logout()) {
      Logger::log(['method' => 'PanelController->myaccount', 'result' => 'Usuario Desconectado'], 'alert');
    }

    // Recupera novos dados do formulário
    $message = [];
    $response_update = [];
    $error_password = false;
    $user_update = ['user_id' => $user_id ];

    if (isset($_POST['user_first_name']) and $_POST['user_first_name']) {
      $user_update['user_first_name'] = $_POST['user_first_name'];
    }

    if (isset($_POST['user_last_name']) and $_POST['user_last_name']) {
      $user_update['user_last_name'] = $_POST['user_last_name'];
    }

    if (isset($_POST['user_email']) and $_POST['user_email']) {
      $user_update['user_email'] = $_POST['user_email'];
    }

    if (isset($_POST['user_new_password']) and $_POST['user_new_password']) {
      $user_update['user_new_password'] = trim($_POST['user_new_password']);
    }

    if (isset($_POST['user_confirm_new_password']) and $_POST['user_confirm_new_password']) {
      $user_update['user_confirm_new_password'] = trim($_POST['user_confirm_new_password']);
    }

    // Atualiza cadastro
    if ($user_update['user_first_name']) {
      $response_update['user'] = $this->usersModel->update_myaccount($user_update);
    }

    // Atualiza senha
    if ($user_update['user_new_password']) {

      if ($user_update['user_new_password'] == $user_update['user_confirm_new_password']) {
        $response_update['password'] = $this->usersModel->update_myaccount_password($user_update);
      }
      else {
        $error_password = true;
      }
    }

    // Mensagens para o usuário
    foreach ($response_update as $key => $value) :

      if ($value == true) {
        $message = ['success_update' => 'Cadastro atualizado com sucesso!'];

        // Armazena novos dados do usuário
        $this->user_id = $user_update['user_id'];
        $this->user_first_name = $user_update['user_first_name'];
        $this->user_last_name = $user_update['user_last_name'];
        $this->user_email = $user_update['user_email'];
        
        // Substitui dados da sessão atual
        $_SESSION['user'] = [
          'user_id' => $this->user_id,
          'user_first_name' => $this->user_first_name,
          'user_last_name' => $this->user_last_name,
          'user_email' => $this->user_email,
        ];
      }

      if ($value === false) {
        $message = ['error_update' => 'Erro ao atualizar cadastro'];
        Logger::log(['method' => 'PanelController->myaccount', 'result' => ['message' => $message, 'local' => $key ]], 'error');
      }
    endforeach;

    if ($error_password) {
      $message = ['error_update' => 'As senhas não coincidem'];
      Logger::log(['method' => 'PanelController->myaccount', 'result' => $message ], 'alert');
    }

    // Prepara conteúdo para a View
    $this->action_route = 'panel/myaccount/' . $user_id;
    $myaccount = $this->usersModel->get_myaccount($user_id);
    $this->active_tab = 'myaccount';

    // View e conteúdo para o menu de navegação
    $nav_view_name = 'panel/templates/nav';
    $nav_view_content = [
      'user_id' => $this->user_id,
      'active_tab' => $this->active_tab,
      'action_route' => $this->action_route,
      'user_first_name' => $this->user_first_name,
      'user_last_name' => $this->user_last_name,
    ];

    // View e conteúdo para a página Minha Conta
    $myaccount_view_name = 'panel/myaccount';
    $myaccount_view_content = [
      'myaccount' => $myaccount, 
      'user_id' => $this->user_id,
      'message' => $message,
    ];

    return [ $nav_view_name => $nav_view_content, $myaccount_view_name => $myaccount_view_content ];
  }

  // Verifica se o usuário possui sessão ativa
  private function check_session()
  {
    if (empty($_SESSION['user'])) {
      header('Location: ' . BASE . '/users/login');
      return true;
    }

    return false;
  }

  // Verifica se o usuário existe e está logado
  private function check_logout()
  {
    $user_exists = $this->panelModel->check_user_exists($this->user_id);

    if ($user_exists['success'] and empty($_POST['logout'])) {
      return false;
    }

    // Encerra sessão e redireciona para a home
    unset($_SESSION['user']);
    session_destroy();

    header('Location: ' . BASE);
    return true;
  }
}