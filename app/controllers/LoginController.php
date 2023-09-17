<?php
require_once '../app/models/LoginModel.php';

class LoginController
{
  public $user;
  public $message;
  public $loginModel;

  public function __construct()
  {
    $this->loginModel = new LoginModel();

    // Valida se o usuário está logado
    if (isset($_SESSION['user']) and $_SESSION['user']) {
      header('Location: /panel/display');
      return true;
    }
  }

  public function start()
  {

    // Verifica se o form de login foi submetido
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $getForm = $this->getForm();

      if ($getForm) {

        // Redireciona para o painel
        header('Location: /panel/display');
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
    $getUser = $this->loginModel->getUser($this->user['email']);

    if (empty($getUser)) {
      return false;
    }

    // Se o usuário for localizado e a senha estiver correta
    if (password_verify(trim($this->user['password']), $getUser[0]['password'])) {
      $this->message = ['success_login' => 'Dados corretos'];

      $_SESSION['user'] = [
        'user_id' => $getUser[0]['id'],
        'user_email' => $getUser[0]['email'],
        'user_first_name' => $getUser[0]['first_name'],
        'user_last_name' => $getUser[0]['last_name'],
      ];

      return true;
    }

    $this->message = ['error_login' => 'Dados inválidos'];
    return false;
  }
}