<?php
require_once '../app/models/LoginModel.php';
require_once '../app/controllers/UsersController.php';

class LoginController extends UsersController
{
  public $user;
  public $message;
  public $loginModel;

  // Retorna a view conforme rota
  public function start()
  {
    parent::checkSession();

    // Verifica se o form de login foi submetido
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $getForm = $this->getForm();

      if ($getForm) {

        // Redireciona para o painel
        header('Location: ' . BASE . '/panel/' . $this->user['user_id']);
        exit();
      }
    }

    $renderView = [
      'login' => ['message' => $this->message ],
    ];

    return $renderView;
  }

  private function getForm()
  {
    $this->user['email'] = $_POST['user_email'] ?? '';
    $this->user['password'] = trim($_POST['user_password']) ?? '';

    // Não aceita campos vazios
    if (in_array('', $this->user, true)) {
      $this->message = ['error_login' => 'Todos os campos precisam ser preenchidos'];
      return false;
    }

    // Verifica se o usuário existe
    $this->loginModel = new LoginModel();
    $getUser = $this->loginModel->getUser($this->user['email']);
    $this->message = ['error_login' => 'Dados inválidos'];

    if (empty($getUser)) {
      return false;
    }

    // Se o usuário for localizado e a senha estiver correta
    if (password_verify(trim($this->user['password']), $getUser[0]['password'])) {

      // Armazena id da sessão no banco de dados
      $sessionId = session_id();
      $saveSession = $this->loginModel->saveSession($getUser[0]['id'], $sessionId);

      // Gera um novo id para cada login realizado
      session_regenerate_id();

      if ($saveSession) {
        $this->user['user_id'] = $getUser[0]['id'];
        $this->message = ['success_login' => 'Dados corretos'];

        // Grava dados do usuário na nova sessão
        $_SESSION['user'] = [
          'session_id' => $sessionId,
          'user_id' => $getUser[0]['id'],
          'user_email' => $getUser[0]['email'],
          'user_first_name' => $getUser[0]['first_name'],
          'user_last_name' => $getUser[0]['last_name'],
        ];

        return true;
      }
    }

    return false;
  }
}