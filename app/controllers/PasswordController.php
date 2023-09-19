<?php
require_once '../app/models/PasswordModel.php';
require_once '../app/controllers/PanelController.php';

class PasswordController extends PanelController
{
  public $form;
  public $message;
  public $passwordModel;

  // Exibe e altera dados do usuário
  public function start($userId) 
  {

    // Valida se o usuário está logado
    if (parent::checkSession($userId) or parent::checkLogout($userId)) {
      Logger::log(['method' => 'PasswordController->myaccount', 'result' => 'Usuario Desconectado'], 'alert');
    }

    // Armazena ID do usuário
    $this->userId = $userId;

    // Recupera formulário de alteração da senha
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $getForm = $this->getForm();

      if ($getForm) {
        $this->updatePassword();
      }
    }

    // Prepara view para tela de senha
    $renderView = [
      'panel/templates/nav' => [
        'user_id' => $this->userId,
        'active_tab' => 'password',
        'action_route' => 'password/' . $this->userId,
        'user_first_name' => $this->userFirstName,
        'user_last_name' => $this->userLastName,
      ],
      'panel/password' => [
        'user_id' => $this->userId,
        'message' => $this->message,
      ],
    ];

    return $renderView;
  }

  private function getForm()
  {
    $this->form['password'] = $_POST['password'] ?? '';
    $this->form['newPassword'] = $_POST['user_new_password'] ?? '';
    $this->form['confirmNewPassword'] = $_POST['user_confirm_new_password'] ?? '';

    // Não aceita campos vazios
    if (in_array('', $this->form, true)) {
      $this->message = ['error_update' => 'Todos os campos precisam ser preenchidos'];
      return false;
    }

    if ($this->form['newPassword'] == $this->form['confirmNewPassword']) {
      return true;
    }

    $this->message = ['error_update' => 'As senhas não coincidem'];
    return false;
  }

  private function updatePassword()
  {

    // Verifica se o usuário já existe
    $this->passwordModel = new PasswordModel();
    $getUser = $this->passwordModel->getUser('', $this->userId);
    $this->message = ['error_update' => 'Erro ao atualizar a senha'];

    if (empty($getUser)) {
      return false;
    }

    // Se o usuário for localizado e a senha estiver correta
    if (password_verify(trim($this->form['password']), $getUser[0]['password'])) {

      // Aualiza a senha
      $this->form['userId'] = $this->userId;
      $updatePassword = $this->passwordModel->updatePassword($this->form);

      if (empty($updatePassword)) {
        return false;
      }

      $this->message = ['success_update' => 'Senha atualizada com sucesso'];
      return true;
    }

    $this->message = ['error_update' => 'Senha atual inválida'];
    return false;
  }
}