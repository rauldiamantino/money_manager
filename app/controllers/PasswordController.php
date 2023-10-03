<?php
require_once '../app/models/PasswordModel.php';
require_once '../app/controllers/PanelController.php';

class PasswordController extends PanelController
{
  public $message;

  // Exibe e altera dados do usuário
  public function start($userId) 
  {
    // Valida sessão e login
    parent::checkLogout($userId);
    parent::checkSession($userId);

    // Recupera formulário de alteração da senha
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $getForm = $this->getForm();

      if ($getForm) {
        $this->updatePassword($getForm, $userId);
      }
    }

    // Prepara view para tela de senha
    $renderView = [
      'panel/templates/nav' => [
        'user_id' => $userId,
        'active_tab' => 'password',
        'action_route' => 'password/' . $userId,
        'user_first_name' => $_SESSION['user']['user_first_name'],
        'user_last_name' => $_SESSION['user']['user_last_name'],
      ],
      'panel/password' => [
        'user_id' => $userId,
        'message' => $this->message,
      ],
    ];

    return $renderView;
  }

  private function getForm()
  {
    $form['password'] = $_POST['password'] ?? '';
    $form['newPassword'] = $_POST['user_new_password'] ?? '';
    $form['confirmNewPassword'] = $_POST['user_confirm_new_password'] ?? '';

    // Não aceita campos vazios
    if (in_array('', $form, true)) {
      $this->message = ['error_update' => 'Todos os campos precisam ser preenchidos'];
      return false;
    }

    if ($form['newPassword'] == $form['confirmNewPassword']) {
      return $form;
    }

    $this->message = ['error_update' => 'As senhas não coincidem'];
    return false;
  }

  private function updatePassword($form, $userId)
  {
    // Verifica se o usuário já existe
    $passwordModel = new PasswordModel();
    $getUser = $passwordModel->getUser('', $userId);
    $this->message = ['error_update' => 'Erro ao atualizar a senha'];

    if (empty($getUser)) {
      return false;
    }

    // Se o usuário for localizado e a senha estiver correta
    if (password_verify(trim($form['password']), $getUser[0]['password'])) {

      // Aualiza a senha
      $form['userId'] = $userId;
      $updatePassword = $passwordModel->updatePassword($form);

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