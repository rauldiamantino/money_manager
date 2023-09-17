<?php
require_once '../app/models/LoginModel.php';

class LoginController
{
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

    // View e conteúdo da página
    $user = [];
    $message = [];
    $renderView = ['login' => []];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

      // Recupera formulário
      $user['email'] = $_POST['user_email'] ?? '';
      $user['password'] = trim($_POST['user_password']) ?? '';

      if (in_array('', $user, true)) {
        $message = ['error_login' => 'Todos os campos precisam ser preenchidos'];
        $renderView['login']['message'] = $message;

        return $renderView;
      }

      // Verifica se o usuário existe
      $message = ['error_login' => 'Dados inválidos'];
      $renderView['login']['message'] = $message;

      $getUser = $this->loginModel->getUser($user['email']);

      if (empty($getUser)) {
        return $renderView;
      }

      if (password_verify(trim($user['password']), $getUser[0]['password'])) {
        $message = ['success_login' => 'Dados corretos'];
      }
    }

    // Se o usuário for localizado, redireciona para o painel
    if (isset($message['success_login']) and empty($_SESSION['user'])) {

      $_SESSION['user'] = [
        'user_id' => $getUser[0]['id'],
        'user_first_name' => $getUser[0]['first_name'],
        'user_last_name' => $getUser[0]['last_name'],
        'user_email' => $getUser[0]['email'],
      ];

      header('Location: /panel/display');
      exit();
    }

    $renderView['login']['message'] = $message;
    return $renderView;
  }
}