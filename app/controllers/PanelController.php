<?php
require_once '../app/models/PanelModel.php';
require_once '../app/models/UsersModel.php';

class PanelController
{
  public $panelModel;
  public $usersModel;
  public $userId;
  public $userFirstName;
  public $userLastName;
  public $userEmail;
  public $activeTab;
  public $actionRoute;

  public function __construct()
  {
    $this->panelModel = new PanelModel();
    $this->usersModel = new UsersModel();

    // Recupera dados de sessão do usuário
    $this->userId = $_SESSION['user']['user_id'] ?? '';
    $this->userFirstName = $_SESSION['user']['user_first_name'] ?? '';
    $this->userLastName = $_SESSION['user']['user_last_name'] ?? '';
    $this->userEmail = $_SESSION['user']['user_email'] ?? '';
  }

  // Exibe visão geral do painel
  public function display()
  {
    // Valida se o usuário está logado
    if ($this->checkSession() or $this->checkLogout()) {
      Logger::log(['method' => 'PanelController->display', 'result' => 'Usuario Desconectado'], 'alert');
    }

    $this->activeTab = 'overview';
    $this->actionRoute = '/panel/display';

    // View e conteúdo para o menu de navegação
    $navViewName = 'panel/templates/nav';
    $navViewContent = [
      'user_id' => $this->userId,
      'active_tab' => $this->activeTab,
      'action_route' => $this->actionRoute,
      'user_first_name' => $this->userFirstName,
      'user_last_name' => $this->userLastName,
    ];

    // View e conteúdo para o painel
    $panelViewName = 'panel/panel';
    $panelViewContent = [];

    return [ $navViewName => $navViewContent, $panelViewName => $panelViewContent ];
  }

  // Exibe todas as transações
  public function transactions($userId)
  {
    $this->userId = $userId;

    // Valida se o usuário está logado
    if ($this->checkSession() or $this->checkLogout()) {
      Logger::log(['method' => 'PanelController->transactions', 'result' => 'Usuario Desconectado'], 'alert');
    }

    $message = [];

    // Recupera informações da transação
    $transactions = [
      'operation' => [
        'add_income' => $_POST['add_income'] ?? 0,
        'add_expense' => $_POST['add_expense'] ?? 0,
        'edit_income' => $_POST['edit_income'] ?? 0,
        'edit_expense' => $_POST['edit_expense'] ?? 0,
        'delete_transaction' => $_POST['delete_transaction_id'] ?? 0,
        'change_status' => $_POST['change_status_transaction_id'] ?? 0,
      ],
      'transaction' => [
        'status' => 0,
        'date' => $_POST['transaction_date'],
        'amount' => $_POST['transaction_amount'],
        'account_id' => $_POST['transaction_account'],
        'category_id' => $_POST['transaction_category'],
        'description' => $_POST['transaction_description'],
      ]
    ];

    // Adiciona ou edita uma receita
    if ($transactions['operation']['add_income']) {

      // Campos específicos para uma receita
      $transactions['transaction']['type'] = 'I';
      $transactions['transaction']['amount'] = $_POST['transaction_amount'];
      $transactions['transaction']['transaction_id'] = $_POST['edit_income'];

      if ($transactions['operation']['edit_income']) {
        $message = $this->editIncome($transactions['transaction']);
      }
      else {
        $message = $this->createIncome($transactions['transaction']);
      }
    }

    // Adiciona ou edita uma despesa
    if ($transactions['operation']['add_expense']) {

      // Campos específicos para uma despesa
      $transactions['transaction']['type'] = 'E';
      $transactions['transaction']['amount'] = -1 * $_POST['transaction_amount'];
      $transactions['transaction']['transaction_id'] = $_POST['edit_expense'];

      // Edita despesa
      if ($transactions['operation']['edit_expense']) {
        $message = $this->editExpense($transactions['transaction']);
      }
      else {
        $message = $this->createExpense($transactions['transaction']);
      }
    }

    // Apaga transação
    if ($transactions['operation']['delete_transaction']) {

      $transactions['transaction'] = [
        'id' => $_POST['delete_transaction_id'],
        'table' => $_POST['delete_transaction_type'] == 'E' ? 'expenses' : 'incomes',
      ];

      $message = $this->deleteTransaction($transactions['transaction']);
    }

    // Altera status da transação
    if ($transactions['operation']['change_status']) {

      $transactions['transaction'] = [
        'id' => $_POST['change_status_transaction_id'],
        'status' => intval($_POST['edit_transaction_status']),
        'table' => $_POST['change_status_transaction_type'] == 'E' ? 'expenses' : 'incomes',
      ];

      $message = $this->changeTransactionStatus($transactions['transaction']);
    }

    // Prepara conteúdo para a View
    $this->actionRoute = 'panel/transactions/' . $this->userId;
    $transactions = $this->panelModel->get_transactions($this->userId);
    $categories = $this->panelModel->get_categories($this->userId);
    $accounts = $this->panelModel->get_accounts($this->userId);
    $this->activeTab = 'transactions';

    // View e conteúdo para o menu de navegação
    $navViewName = 'panel/templates/nav';
    $navViewContent = [
      'user_id' => $this->userId,
      'active_tab' => $this->activeTab,
      'action_route' => $this->actionRoute,
      'user_first_name' => $this->userFirstName,
      'user_last_name' => $this->userLastName,
    ];

    // View e conteúdo para a página de transações
    $transactionsViewName = 'panel/transactions';
    $transactionsViewContent = [
      'transactions' => $transactions, 
      'user_id' => $this->userId, 
      'categories' => $categories, 
      'accounts' => $accounts,
      'message' => $message,
    ];

    return [ $navViewName => $navViewContent, $transactionsViewName => $transactionsViewContent ];
  }

  // Exibe todas as contas
  public function accounts($userId)
  {
    $this->userId = $userId;

    // Valida se o usuário está logado
    if ($this->checkSession() or $this->checkLogout()) {
      Logger::log(['method' => 'PanelController->accounts', 'result' => 'Usuario Desconectado'], 'alert');
    }
    
    $account = [
      'id' => $_POST['account_id'] ?? 0,
      'name' => $_POST['account_name'] ?? '',
      'delete' => $_POST['delete_account_id'] ?? 0
    ];

    $message = [];

    // Adiciona uma nova conta para o usuário
    if ($account['name'] and empty($account['id'])) {
      $message = $this->createAccount($account);
    }

    // Edita uma conta já existente
    if ($account['id']) {
      $message = $this->editAccount($account);
    }

    // Apaga uma conta
    if ($account['delete']) {
      $message = $this->deleteAccount($account);
    }

    if (empty($message)) {
      Logger::log(['method' => 'PanelController->accounts', 'result' => $account ]);
    }

    // Prepara conteúdo para a View
    $this->actionRoute = 'panel/accounts/' . $this->userId;
    $accounts = $this->panelModel->get_accounts($this->userId);
    $this->activeTab = 'accounts';

    // View e conteúdo para o menu de navegação
    $navViewName = 'panel/templates/nav';
    $navViewContent = [
      'user_id' => $this->userId,
      'active_tab' => $this->activeTab,
      'action_route' => $this->actionRoute,
      'user_first_name' => $this->userFirstName,
      'user_last_name' => $this->userLastName,
    ];

    // View e conteúdo para a página de contas
    $accountsViewName = 'panel/accounts';
    $accountsViewContent = [
      'accounts' => $accounts, 
      'user_id' => $this->userId,
      'message' => $message,
    ];

    return [ $navViewName => $navViewContent, $accountsViewName => $accountsViewContent ];
  }

  // Exibe todas as categorias
  public function categories($userId)
  {
    $this->userId = $userId;

    // Valida se o usuário está logado
    if ($this->checkSession() or $this->checkLogout()) {
      Logger::log(['method' => 'PanelController->categories', 'result' => 'Usuario Desconectado'], 'alert');
    }

    $category = [
      'id' => $_POST['category_id'] ?? 0,
      'name' => $_POST['category_name'] ?? '',
      'delete' => $_POST['delete_category_id'] ?? 0
    ];

    $message = [];

    // Adiciona uma nova categoria para o usuário
    if ($category['name'] and empty($category['id'])) {
      $message = $this->createCategory($category);
    }

    // Edita uma categoria já existente
    if ($category['id']) {
      $message = $this->editCategory($category);
    }

    // Apaga uma categoria
    if ($category['delete']) {
      $message = $this->deleteCategory($category);
    }

    if (empty($message)) {
      Logger::log(['method' => 'PanelController->categories', 'result' => $category ]);
    }

    // Prepara conteúdo para a View
    $this->actionRoute = 'panel/categories/' . $this->userId;
    $categories = $this->panelModel->get_categories($this->userId);
    $this->activeTab = 'categories';

    // View e conteúdo para o menu de navegação
    $navViewName = 'panel/templates/nav';
    $navViewContent = [
      'user_id' => $this->userId,
      'active_tab' => $this->activeTab,
      'action_route' => $this->actionRoute,
      'user_first_name' => $this->userFirstName,
      'user_last_name' => $this->userLastName,
    ];

    // View e conteúdo para a página de categorias
    $categoriesViewName = 'panel/categories';
    $categoriesViewContent = [
      'categories' => $categories, 
      'user_id' => $this->userId,
      'message' => $message,
    ];

    return [ $navViewName => $navViewContent, $categoriesViewName => $categoriesViewContent ];
  }

  // Adiciona uma nova receita ao formulário
  public function createIncome($income)
  {
    $createIncome = $this->panelModel->createIncome($this->userId, $income);

    if (empty($createIncome)) {
      return ['error_transaction' => 'Erro ao cadastrar receita'];
    }

    return [];
  }

  // Edita uma receita existente
  public function editIncome($income)
  {
    $editIncome = $this->panelModel->editIncome($this->userId, $income);

    if (empty($editIncome)) {
      return ['error_transaction' => 'Erro ao editar receita'];
    }

    return [];
  }

  // Adiciona uma nova despesa ao formulário
  public function createExpense($expense)
  {
    $createExpense = $this->panelModel->createExpense($this->userId, $expense);

    if (empty($createExpense)) {
      return ['error_transaction' => 'Erro ao cadastrar despesa'];
    }

    return [];
  }

  // Edita uma despesa existente
  public function editExpense($expense)
  {
    $editExpense = $this->panelModel->editExpense($this->userId, $expense);

    if (empty($editExpense)) {
      return ['error_transaction' => 'Erro ao editar despesa'];
    }

    return [];
  }

  public function deleteTransaction($transaction)
  {
    $deleteTransaction = $this->panelModel->deleteTransaction($this->userId, $transaction);

    if (empty($deleteTransaction)) {
      return ['error_transaction' => 'Erro ao apagar transação'];
    }

    return [];
  }

  public function changeTransactionStatus($transaction)
  {
    $changeTransactionStatus = $this->panelModel->changeTransactionStatus($this->userId, $transaction);;

    if (empty($changeTransactionStatus)) {
      return ['error_transaction' => 'Erro ao alterar status da transação'];
    }

    return [];
  }

  // Cria uma nova conta
  public function createAccount($account)
  {

    // Verifica se a conta existe
    $accountExists = $this->panelModel->accountExists($this->userId, ['name' => $account['name'] ]);

    if ($accountExists) {
      return ['error_account' => 'Conta já existe'];
    }

    // Cria a conta
    $createAccount = $this->panelModel->createAccount($this->userId, $account['name']);

    if (empty($createAccount)) {
      return ['error_account' => 'Erro ao cadastrar conta'];
    }

    return [];
  }

  // Edita uma conta já existente
  public function editAccount($account)
  {

    // Verifica se a conta existe
    $accountExists = $this->panelModel->accountExists($this->userId, ['id' => $account['id'] ]);

    if (empty($accountExists)) {
      return ['error_account' => 'Conta inexistente'];
    }

    // Edita a conta
    $editAccount = $this->panelModel->editAccount($this->userId, ['id' => $account['id'], 'name' => $account['name'] ]);

    if (empty($editAccount)) {
      return ['error_account' => 'Erro ao editar conta'];
    }

    return [];
  }

  // Apaga uma conta do banco de dados
  public function deleteAccount($account)
  {

    // Não apaga conta em uso
    $accountInUse = $this->panelModel->accountInUse($this->userId, $account['delete']);

    if ($accountInUse) {
      return ['error_account' => 'Conta em uso não pode ser apagada'];
    }

    // Apaga a conta
    $deleteAccount = $this->panelModel->deleteAccount($this->userId, $account['delete']);

    if (empty($deleteAccount)) {
      return ['error_account' => 'Erro ao apagar conta'];
    }

    return [];
  }

  // Cria uma nova categoria
  public function createCategory($category)
  {

    // Verifica se a categoria existe
    $categoryExists = $this->panelModel->categoryExists($this->userId, ['name' => $category['name'] ]);

    if ($categoryExists) {
      return ['error_category' => 'Conta já existe'];
    }

    // Cria a categoria
    $createCategory = $this->panelModel->createCategory($this->userId, $category['name']);

    if (empty($createCategory)) {
      return ['error_category' => 'Erro ao cadastrar categoria'];
    }

    return [];
  }

  // Edita uma categoria já existente
  public function editCategory($category)
  {

    // Verifica se a categoria existe
    $categoryExists = $this->panelModel->categoryExists($this->userId, ['id' => $category['id'] ]);

    if (empty($categoryExists)) {
      return ['error_category' => 'Conta inexistente'];
    }

    // Edita a categoria
    $editCategory = $this->panelModel->editCategory($this->userId, ['id' => $category['id'], 'name' => $category['name'] ]);

    if (empty($editCategory)) {
      return ['error_category' => 'Erro ao editar categoria'];
    }

    return [];
  }

  // Apaga uma categoria do banco de dados
  public function deleteCategory($category)
  {

    // Não apaga categoria em uso
    $categoryInUse = $this->panelModel->categoryInUse($this->userId, $category['delete']);

    if ($categoryInUse) {
      return ['error_category' => 'Conta em uso não pode ser apagada'];
    }

    // Apaga a categoria
    $deleteCategory = $this->panelModel->deleteCategory($this->userId, $category['delete']);

    if (empty($deleteCategory)) {
      return ['error_category' => 'Erro ao apagar categoria'];
    }

    return [];
  }

  // Exibe e altera dados do usuário
  public function myaccount($userId) 
  {
    $this->userId = $userId;

    // Valida se o usuário está logado
    if ($this->checkSession() or $this->checkLogout()) {
      Logger::log(['method' => 'PanelController->myaccount', 'result' => 'Usuario Desconectado'], 'alert');
    }

    // Recupera novos dados do formulário
    $message = [];
    $responseUpdate = [];
    $errorPassword = false;
    $userUpdate = ['user_id' => $this->userId ];

    if (isset($_POST['user_first_name']) and $_POST['user_first_name']) {
      $userUpdate['user_first_name'] = $_POST['user_first_name'];
    }

    if (isset($_POST['user_last_name']) and $_POST['user_last_name']) {
      $userUpdate['user_last_name'] = $_POST['user_last_name'];
    }

    if (isset($_POST['user_email']) and $_POST['user_email']) {
      $userUpdate['user_email'] = $_POST['user_email'];
    }

    if (isset($_POST['user_new_password']) and $_POST['user_new_password']) {
      $userUpdate['user_new_password'] = trim($_POST['user_new_password']);
    }

    if (isset($_POST['user_confirm_new_password']) and $_POST['user_confirm_new_password']) {
      $userUpdate['user_confirm_new_password'] = trim($_POST['user_confirm_new_password']);
    }

    // Atualiza cadastro
    if ($userUpdate['user_first_name']) {
      $responseUpdate['user'] = $this->usersModel->update_myaccount($userUpdate);
    }

    // Atualiza senha
    if ($userUpdate['user_new_password']) {

      if ($userUpdate['user_new_password'] == $userUpdate['user_confirm_new_password']) {
        $responseUpdate['password'] = $this->usersModel->update_myaccount_password($userUpdate);
      }
      else {
        $errorPassword = true;
      }
    }

    // Mensagens para o usuário
    foreach ($responseUpdate as $key => $value) :

      if ($value == true) {
        $message = ['success_update' => 'Cadastro atualizado com sucesso!'];

        // Armazena novos dados do usuário
        $this->userId = $userUpdate['user_id'];
        $this->userFirstName = $userUpdate['user_first_name'];
        $this->userLastName = $userUpdate['user_last_name'];
        $this->userEmail = $userUpdate['user_email'];
        
        // Substitui dados da sessão atual
        $_SESSION['user'] = [
          'user_id' => $this->userId,
          'user_first_name' => $this->userFirstName,
          'user_last_name' => $this->userLastName,
          'user_email' => $this->userEmail,
        ];
      }

      if ($value === false) {
        $message = ['error_update' => 'Erro ao atualizar cadastro'];
        Logger::log(['method' => 'PanelController->myaccount', 'result' => ['message' => $message, 'local' => $key ]], 'error');
      }
    endforeach;

    if ($errorPassword) {
      $message = ['error_update' => 'As senhas não coincidem'];
      Logger::log(['method' => 'PanelController->myaccount', 'result' => $message ], 'alert');
    }

    // Prepara conteúdo para a View
    $this->actionRoute = 'panel/myaccount/' . $this->userId;
    $myaccount = $this->usersModel->get_myaccount($this->userId);
    $this->activeTab = 'myaccount';

    // View e conteúdo para o menu de navegação
    $navViewName = 'panel/templates/nav';
    $navViewContent = [
      'user_id' => $this->userId,
      'active_tab' => $this->activeTab,
      'action_route' => $this->actionRoute,
      'user_first_name' => $this->userFirstName,
      'user_last_name' => $this->userLastName,
    ];

    // View e conteúdo para a página Minha Conta
    $myaccountViewName = 'panel/myaccount';
    $myaccountViewContent = [
      'myaccount' => $myaccount, 
      'user_id' => $this->userId,
      'message' => $message,
    ];

    return [ $navViewName => $navViewContent, $myaccountViewName => $myaccountViewContent ];
  }

  // Verifica se o usuário possui sessão ativa
  private function checkSession()
  {
    if (empty($_SESSION['user'])) {
      header('Location: ' . BASE . '/users/login');
      return true;
    }

    return false;
  }

  // Verifica se o usuário existe e está logado
  private function checkLogout()
  {
    $userExists = $this->panelModel->check_user_exists($this->userId);

    if ($userExists['success'] and empty($_POST['logout'])) {
      return false;
    }

    // Encerra sessão e redireciona para a home
    unset($_SESSION['user']);
    session_destroy();

    header('Location: ' . BASE);
    return true;
  }
}