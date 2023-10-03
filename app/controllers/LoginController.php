<?php
require_once '../app/models/LoginModel.php';
require_once '../app/controllers/UsersController.php';

class LoginController extends UsersController
{
  public $message;

  public function start()
  {
    parent::checkSession();

    // Verifica se o form de login foi submetido
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $getForm = $this->getForm();

      if ($getForm) {
        // Redireciona para o painel se os dados estiverem corretos
        header('Location: ' . BASE . '/panel/' . $getForm['user_id']);
        exit();
      }
    }

    $renderView = ['login' => ['message' => $this->message ]];
    return $renderView;
  }

  private function getForm()
  {
    $user = [
      'email' => $_POST['user_email'] ?? '',
      'password' => trim($_POST['user_password']) ?? '',
    ];

    // Não aceita campos vazios
    if (in_array('', $user, true)) {
      $this->message = ['error_login' => 'Todos os campos precisam ser preenchidos'];
      return false;
    }

    // Verifica se o usuário existe
    $loginModel = new LoginModel();
    $getUser = $loginModel->getUser($user['email']);
    $this->message = ['error_login' => 'Dados inválidos'];

    if (empty($getUser)) {
      return false;
    }

    // Se o usuário for localizado e a senha estiver correta
    if (password_verify(trim($user['password']), $getUser[0]['password'])) {

      // Armazena id da sessão
      $sessionId = session_id();
      $saveSession = $loginModel->saveSession($getUser[0]['id'], $sessionId);

      // Gera um novo id
      session_regenerate_id();

      if ($saveSession) {
        $user['user_id'] = $getUser[0]['id'];
        $this->message = ['success_login' => 'Dados corretos'];

        // Atuaiza sessão
        $_SESSION['user'] = [
          'session_id' => $sessionId,
          'user_id' => $getUser[0]['id'],
          'user_email' => $getUser[0]['email'],
          'user_first_name' => $getUser[0]['first_name'],
          'user_last_name' => $getUser[0]['last_name'],
        ];

        return $user;
      }
    }

    return false;
  }
}