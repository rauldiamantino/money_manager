<?php
require_once '../app/models/MyaccountModel.php';
require_once '../app/controllers/PanelController.php';

class MyaccountController extends PanelController
{
  public $form;
  public $getUser;
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
    $this->userId = $userId;
    $this->myaccountModel = new MyaccountModel();
    $this->getUser = $this->myaccountModel->getUser('', $this->userId);

    // Recupera formulário de alteração da senha
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $getForm = $this->getForm();

      if ($getForm) {
        $this->updateMyaccount();
      }
    }

    // Prepara view para tela de senha
    $renderView = [
      'panel/templates/nav' => [
        'user_id' => $this->userId,
        'active_tab' => 'myaccount',
        'action_route' => 'myaccount/' . $this->userId,
        'user_first_name' => $_SESSION['user']['user_first_name'],
        'user_last_name' => $_SESSION['user']['user_last_name'],
      ],
      'panel/myaccount' => [
        'myaccount' => $_SESSION['user'],
        'user_id' => $this->userId,
        'message' => $this->message,
      ],
    ];

    return $renderView;
  }

  private function getForm()
  {
    $this->form['password'] = $_POST['password'] ?? '';
    $this->form['firstName'] = $_POST['user_first_name'] ?? '';
    $this->form['lastName'] = $_POST['user_last_name'] ?? '';
    $this->form['email'] = $_POST['user_email'] ?? '';


    // Não aceita campos vazios
    if (in_array('', $this->form, true)) {
      $this->message = ['error_update' => 'Todos os campos precisam ser preenchidos'];
      return false;
    }
    
    return true;
  }

  private function updateMyaccount()
  {

    // Se o usuário for localizado e a senha estiver correta
    if ($this->getUser and password_verify(trim($this->form['password']), $this->getUser[0]['password'])) {

      // Aualiza a senha
      $this->form['userId'] = $this->userId;
      $updateMyaccount = $this->myaccountModel->updateMyaccount($this->form);

      if (empty($updateMyaccount)) {
        return false;
      }

      $this->message = ['success_update' => 'Cadastro atualizado com sucesso'];

      // Grava dados do usuário na nova sessão
      $_SESSION['user']['user_email'] = $this->form['email'];
      $_SESSION['user']['user_first_name'] = $this->form['firstName'];
      $_SESSION['user']['user_last_name'] = $this->form['lastName'];

      return true;
    }

    $this->message = ['error_update' => 'Senha atual inválida'];
    return false;
  }
}