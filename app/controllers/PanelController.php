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
    $this->action_route = '../panel/display';

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

    // Resultado da tentativa de adicionar transações
    if (isset($_POST['add_income'])) {
      $message = $this->add_income($user_id);
    }

    if (isset($_POST['add_expense'])) {
      $message = $this->add_expense($user_id);
    }

    // Prepara conteúdo para a View
    $this->action_route = '../transactions/' . $user_id;
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
    $this->action_route = '../accounts/' . $user_id;
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
    $this->action_route = '../categories/' . $user_id;
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
      'description' => $_POST['transaction_description'],
      'amount' => $_POST['transaction_amount'],
      'category_id' => $_POST['transaction_category'],
      'account_id' => $_POST['transaction_account'],
      'date' => $_POST['transaction_date'],
    ];

    $response = $this->panelModel->add_income($user_id, $income);
    $message = ['success' => 'Receita adicionada com sucesso!'];

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
      'description' => $_POST['transaction_description'],
      'amount' => -1 * $_POST['transaction_amount'],
      'category_id' => $_POST['transaction_category'],
      'account_id' => $_POST['transaction_account'],
      'date' => $_POST['transaction_date'],
    ];

    $response = $this->panelModel->add_expense($user_id, $expense);
    $message = ['success' => 'Despesa adicionada com sucesso!'];

    if ($response == false) {
      $message = ['error_expense' => 'Erro ao cadastrar despesa'];
      Logger::log(['method' => 'PanelController->add_expense', 'result' => $response ], 'error');
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

  // Exibe cadastro do usuário
  public function myaccount($user_id) 
  {
    // Valida se o usuário está logado
    if ($this->check_session() or $this->check_logout()) {
      Logger::log(['method' => 'PanelController->myaccount', 'result' => 'Usuario Desconectado'], 'alert');
    }

    // Recupera alterações no cadastro
    $user_first_name = $_POST['user_first_name'] ?? '';
    $user_last_name = $_POST['user_last_name'] ?? '';
    $user_email = $_POST['user_email'] ?? '';

    // Recupera alteração de senha
    $user_new_password = trim($_POST['user_new_password']) ?? '';
    $user_confirm_new_password = trim($_POST['user_confirm_new_password']) ?? '';

    $message = [];
    $request_update = false;
    $return_update = false;

    // Atualiza o cadastro do usuário
    if ($user_first_name) {
      $request_update = true;

      $result = $this->usersModel->update_myaccount([
        'user_id' => $user_id,
        'user_first_name' => $user_first_name,
        'user_last_name' => $user_last_name, 
        'user_email' => $user_email,
      ]);

      if ($result) {
        $return_update = true;
      }
    }

    if ($user_new_password) {
      $request_update = true;
      $return_update = false;
      $result = '';

      // Se as senhas forem iguais, faz alteração
      if ($user_new_password == $user_confirm_new_password) {
        $result = $this->usersModel->update_myaccount_password(['user_id' => $user_id, 'user_new_password' => $user_new_password ]);
      }
      else {
        $request_update = false;
        $message = ['error_update' => 'As senhas não coincidem'];
        Logger::log(['method' => 'PanelController->myaccount', 'result' => $message ], 'error');
      }

      if ($result) {
        $return_update = true;
      }
    }

    // Exibe mensagens de atualização do cadastro
    if ($return_update) {
      $message = ['success_update' => 'Cadastro atualizado com sucesso!'];
    }

    if ($request_update and $return_update == false) {
      $message = ['error_update' => 'Erro ao atualizar cadastro'];
      Logger::log(['method' => 'PanelController->myaccount', 'result' => $message ], 'error');
    }

    // Prepara conteúdo para a View
    $this->action_route = '../myaccount/' . $user_id;
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