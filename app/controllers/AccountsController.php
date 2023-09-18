<?php
require_once '../app/controllers/PanelController.php';

class AccountsController extends PanelController
{

  // Exibe todas as contas
  public function accounts($userId)
  {
    $this->userId = $userId;

    // Valida se o usuário está logado
    if (parent::checkSession() or parent::checkLogout()) {
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
    $this->actionRoute = 'accounts/' . $this->userId;
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
}