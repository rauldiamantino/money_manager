<?php
require_once '../app/models/AccountsModel.php';
require_once '../app/controllers/PanelController.php';

class AccountsController extends PanelController
{
  public $accountsModel;

  // Exibe todas as contas
  public function accounts($userId)
  {
    // Valida sessão e login
    parent::checkLogout($userId);
    parent::checkSession($userId);
    
    $account = [
      'id' => $_POST['account_id'] ?? 0, 
      'name' => $_POST['account_name'] ?? '', 
      'delete' => $_POST['delete_account_id'] ?? 0
    ];

    $message = [];
    $this->accountsModel = new AccountsModel();

    // Adiciona uma nova conta para o usuário
    if ($account['name'] and empty($account['id'])) {
      $message = $this->createAccount($userId, $account);
    }

    // Edita uma conta já existente
    if ($account['id']) {
      $message = $this->editAccount($userId, $account);
    }

    // Apaga uma conta
    if ($account['delete']) {
      $message = $this->deleteAccount($userId, $account);
    }

    if (empty($message)) {
      Logger::log(['method' => 'AccountsController->accounts', 'result' => $account ]);
    }

    // Renderiza menu e conteúdo
    $user = $this->accountsModel->getUser('', $userId);
    $accounts = $this->accountsModel->getAccounts($userId);

    $renderView = [
      'panel/templates/nav' => [
        'user_id' => $userId,
        'active_tab' => 'accounts',
        'action_route' => 'accounts/' . $userId,
        'user_first_name' => $user[0]['first_name'],
        'user_last_name' => $user[0]['last_name'],
      ],
      'panel/accounts' => [
        'accounts' => $accounts, 
        'user_id' => $userId,
        'message' => $message,
      ],
    ];

    return $renderView;
  }
  
  // Cria uma nova conta
  private function createAccount($userId, $account)
  {
    // Verifica se a conta existe
    $accountExists = $this->accountsModel->accountExists($userId, ['name' => $account['name'] ]);

    if ($accountExists) {
      return ['error_account' => 'Conta já existe'];
    }

    // Cria a conta
    $createAccount = $this->accountsModel->createAccount($userId, $account['name']);

    if (empty($createAccount)) {
      return ['error_account' => 'Erro ao cadastrar conta'];
    }

    return [];
  }

  // Edita uma conta já existente
  private function editAccount($userId, $account)
  {
    // Verifica se a conta existe
    $accountExists = $this->accountsModel->accountExists($userId, ['id' => $account['id'] ]);

    if (empty($accountExists)) {
      return ['error_account' => 'Conta inexistente'];
    }

    // Edita a conta
    $editAccount = $this->accountsModel->editAccount($userId, ['id' => $account['id'], 'name' => $account['name'] ]);

    if (empty($editAccount)) {
      return ['error_account' => 'Erro ao editar conta'];
    }

    return [];
  }

  // Apaga uma conta do banco de dados
  private function deleteAccount($userId, $account)
  {
    // Não apaga conta em uso
    $accountInUse = $this->accountsModel->accountInUse($userId, $account['delete']);

    if ($accountInUse) {
      return ['error_account' => 'Conta em uso não pode ser apagada'];
    }

    // Apaga a conta
    $deleteAccount = $this->accountsModel->deleteAccount($userId, $account['delete']);

    if (empty($deleteAccount)) {
      return ['error_account' => 'Erro ao apagar conta'];
    }

    return [];
  }
}