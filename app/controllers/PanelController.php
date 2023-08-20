<?php
require_once '../app/models/PanelModel.php';

class PanelController
{
  private $panelModel;
  private $user_id;
  private $user_first_name;
  private $user_last_name;
  private $active_tab;
  private $action_route;

  public function __construct()
  {
    $this->panelModel = new PanelModel();

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
    $this->check_session();
    $this->check_logout();

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
    $this->check_session();
    $this->check_logout();

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
    $this->check_session();
    $this->check_logout();

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
    $this->check_session();
    $this->check_logout();

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

    return $response;
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

    return $response;
  }

  // Adiciona conta no banco de dados
  public function add_account($user_id)
  {
    $account = $_POST['account'];
    $response = $this->panelModel->add_account($user_id, $account);
    
    return $response;
  }

  // Adiciona categoria no banco de dados
  public function add_category($user_id)
  {
    $category = $_POST['category'];
    $response = $this->panelModel->add_category($user_id, $category);
    
    return $response;
  }

  // Verifica se o usuário possui sessão ativa
  private function check_session()
  {
    if (empty($_SESSION['user'])) {
      header('Location: ' . BASE . '/users/login');
      exit();
    }
  }

  // Verifica se o usuário está logado
  private function check_logout()
  {
    if (isset($_POST['logout']) and $_POST['logout']) {
      unset($_SESSION['user']);
      session_destroy();

      header('Location: ' . BASE);
      exit();
    }
  }
}