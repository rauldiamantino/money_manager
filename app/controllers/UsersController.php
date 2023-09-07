<?php
require_once '../app/models/UsersModel.php';

class UsersController
{
  protected $usersModel;

  public function __construct()
  {
    $this->usersModel = new UsersModel();
  }

  // Cadastro de usuário
  public function registration()
  {
    // Verifica se o usuário possui sessão ativa
    $this->check_session();
    
    // View e conteúdo da página
    $view_name = 'user_register';
    $view_content = [];

    if (empty($_POST['user_email'])) {
      return [ $view_name => $view_content ];
    }

    // Recupera formulário
    $user = [
      'user_first_name' => $_POST['user_first_name'],
      'user_last_name' => $_POST['user_last_name'],
      'user_email' => $_POST['user_email'],
      'user_password' => trim($_POST['user_password']),
      ];
    $user_confirm_password = trim($_POST['user_confirm_password']);
    $message = ['error_password' => 'As senhas não coincidem'];

    // Faz requisição do cadastro
    if ($user['user_password'] == $user_confirm_password) {
      $response = $this->usersModel->register_user($user);
    }

    if (isset($response['error_register'])) {
      $message = ['error_register' => $response['error_register']];
    }

    if (isset($response['success_register'])) {
      $message = ['success_register' => $response['success_register']];
    }

    // Retorna view e seu conteúdo
    $view_content = ['message' => $message, 'user_email' => $user['user_email']];

    return [ $view_name => $view_content ];
  }

  public function login()
  {
    // Verifica se o usuário está logado
    $this->check_session();

    // View e conteúdo da página
    $view_name = 'user_login';
    $view_content = [];

    if (empty($_POST['user_password'])) {
      return [$view_name => $view_content];
    }

    $user = ['user_email' => $_POST['user_email'], 'user_password' => trim($_POST['user_password'])];
    $response = $this->usersModel->login_user($user);

    if (isset($response['error_login'])) {
      $message = ['error_login' => $response['error_login']];
    }

    // Se o usuário for localizado, redireciona para o painel
    if (isset($response['success_login']) and empty($_SESSION['user'])) {
      $_SESSION['user'] = [
        'user_id' => $response['success_login']['user_id'],
        'user_first_name' => $response['success_login']['user_first_name'],
        'user_last_name' => $response['success_login']['user_last_name'],
        'user_email' => $response['success_login']['user_email'],
      ];

      $message = ['success_login' => $response['success_login']['message']];

      header('Location: ' . BASE . '/panel/display');
      exit();
    }

    // Se o usuario não for localizado, retorna  mensagem de erro
    $view_content = ['message' => $message ];

    return [$view_name => $view_content];
  }

  // Verifica se o usuário possui sessão ativa
  private function check_session()
  {
    if (isset($_SESSION['user']) and $_SESSION['user']) {
      header('Location: ' . BASE . '/panel/display');
      exit();
    }
  }
}