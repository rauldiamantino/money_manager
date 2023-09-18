<?php
require_once '../app/controllers/PanelController.php';

class MyaccountController extends PanelController
{

  // Exibe e altera dados do usuário
  public function myaccount($userId) 
  {
    $this->userId = $userId;

    // Valida se o usuário está logado
    if (parent::checkSession() or parent::checkLogout()) {
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
    $this->actionRoute = 'myaccount/' . $this->userId;
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
}