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
    $accounts = $this->panelModel->getAccounts($this->userId);
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
      $responseUpdate['user'] = $this->usersModel->updateMyaccount($userUpdate);
    }

    // Atualiza senha
    if ($userUpdate['user_new_password']) {

      if ($userUpdate['user_new_password'] == $userUpdate['user_confirm_new_password']) {

        // Altera a senha se o usuário existir
        $getUser = $this->usersModel->getUser('', $userUpdate['user_id']);

        if ($getUser) {
          $responseUpdate['password'] = $this->usersModel->updateMyaccountPassword($userUpdate);
        }
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
    $myaccount = $this->usersModel->getMyaccount($this->userId);
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
  public function checkSession()
  {
    if (empty($_SESSION['user'])) {
      header('Location: ' . BASE . 'login');
      return true;
    }

    return false;
  }

  // Verifica se o usuário existe e está logado
  public function checkLogout()
  {
    $userExists = $this->panelModel->checkUserExists($this->userId);

    if ($userExists and empty($_POST['logout'])) {
      return false;
    }

    // Encerra sessão e redireciona para a home
    unset($_SESSION['user']);
    session_destroy();

    header('Location: ' . BASE);
    return true;
  }
}