<?php
require_once '../app/models/MyaccountModel.php';
require_once '../app/controllers/PanelController.php';

class MyaccountController extends PanelController
{
  public $message;
  public $myaccountModel;

  // Exibe e altera dados do usuário
  public function start($userId) 
  {
    // Valida se o usuário está logado
    if (parent::checkSession($userId) or parent::checkLogout($userId)) {
      Logger::log(['method' => 'MyaccountController->myaccount', 'result' => 'Usuario Desconectado'], 'alert');
    }

    // Busca dados atuais do usuário
    $this->myaccountModel = new MyaccountModel();
    $getUser = $this->myaccountModel->getUser('', $userId);

    // Recupera alterações do cadastro
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $getForm = $this->getForm();

      if ($getForm) {
        $this->updateMyaccount($getForm, $getUser);
      }
    }

    // Renderiza
    $renderView = [
      'panel/templates/nav' => [
        'user_id' => $userId,
        'active_tab' => 'myaccount',
        'action_route' => 'myaccount/' . $userId,
        'user_first_name' => $_SESSION['user']['user_first_name'],
        'user_last_name' => $_SESSION['user']['user_last_name'],
      ],
      'panel/myaccount' => [
        'myaccount' => $_SESSION['user'],
        'user_id' => $userId,
        'message' => $this->message,
      ],
    ];

    return $renderView;
  }

  private function getForm()
  {
    $form = [
      'password' => $_POST['password'] ?? '',
      'firstName' => $_POST['user_first_name'] ?? '',
      'lastName' => $_POST['user_last_name'] ?? '',
      'email' => $_POST['user_email'] ?? '',
    ];

    // Não aceita campos vazios
    if (in_array('', $form, true)) {
      $this->message = ['error_update' => 'Todos os campos precisam ser preenchidos'];
      return false;
    }
    
    return $form;
  }

  private function updateMyaccount($form, $getUser)
  {
    // Se o usuário for localizado e a senha estiver correta
    if ($getUser and password_verify(trim($form['password']), $getUser[0]['password'])) {

      // Novo e-mail não pode existir para nenhum outro usuário
      $emailExists = $this->myaccountModel->getUser($form['email']);

      if ($emailExists and $getUser[0]['id'] != $emailExists[0]['id']) {
        $this->message = ['error_update' => 'Este e-mail já está em uso'];
        return false;
      }

      // Aualiza o cadastro
      $form['userId'] = $getUser[0]['id'];
      $updateMyaccount = $this->myaccountModel->updateMyaccount($form);

      if (empty($updateMyaccount)) {
        return false;
      }

      $this->message = ['success_update' => 'Cadastro atualizado com sucesso'];

      // Grava dados do usuário na nova sessão
      $_SESSION['user']['user_email'] = $form['email'];
      $_SESSION['user']['user_first_name'] = $form['firstName'];
      $_SESSION['user']['user_last_name'] = $form['lastName'];

      return true;
    }

    $this->message = ['error_update' => 'Senha atual inválida'];
    return false;
  }
}