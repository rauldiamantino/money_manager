<?php
require_once '../app/helpers/ViewRenderes.php';
require_once '../app/models/UsersModel.php';

class UsersController
{
  protected $usersModel;

  public function __construct()
  {
    $this->usersModel = new UsersModel();
  }

  public function registration()
  {
    // verifica se o usuário está logado
    $this->check_session();

    // somente se o formulário for submetido
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      try {
        $user_first_name = $_POST['user_first_name'] ?? '';
        $user_last_name = $_POST['user_last_name'] ?? '';
        $user_email = $_POST['user_email'] ?? '';
        $user_password = $_POST['user_password'] ?? '';
        $user_confirm_password = $_POST['user_confirm_password'] ?? '';
        $response = [];

        if ($user_password == $user_confirm_password) {
          $data = [
          'user_first_name' => $user_first_name,
          'user_last_name' => $user_last_name,
          'user_email' => $user_email,
          'user_password' => $user_password,
          ];
          
          $response = $this->usersModel->register_user($data);
        }
        else {
          throw new Exception('As senhas não coincidem');
        }

        if (isset($response['error_register'])) {
          $message = ['error_register' => $response['error_register']];
        }
        elseif (isset($response['success_register'])) {
          $message = ['success_register' => $response['success_register']];
        }
      }
      catch (Exception $e) {
        $message = ['error_password' => $e->getMessage()];
      }
    }

    ViewRenderer::render('user_register', ['message' => $message ?? [], 'user_email' => $user_email ?? []]);
  }

  public function login()
  {
    // verifica se o usuário está logado
    $this->check_session();

    // somente se o formulário for submetido
    if ($_SERVER['REQUEST_METHOD'] === 'POST' and isset($_POST['user_password'])) {
      try {
        $user_email = $_POST['user_email'] ?? '';
        $user_password = $_POST['user_password'] ?? '';

        $data = [
          'user_email' => $user_email,
          'user_password' => $user_password,
        ];

        $response = $this->usersModel->login_user($data);

        if (isset($response['error_login'])) {
          $message = ['error_login' => $response['error_login']];
        }
        elseif (isset($response['success_login']) and empty($_SESSION['user'])) {
          $message = ['success_login' => $response['success_login']['message']];

          $_SESSION['user'] = [
            'user_id' => $response['success_login']['user_id'],
            'user_first_name' => $response['success_login']['user_first_name'],
            'user_last_name' => $response['success_login']['user_last_name'],
            'user_email' => $response['success_login']['user_email'],
          ];

          // se o usuário for localizado, redireciona para o painel
          header('Location: ' . BASE . '/panel/display');
          exit();
        }
      }
      catch (Exception $e) {
        $message = ['error_login' => 'Erro ao fazer login: ' . $e->getMessage()];
      }
    }

    ViewRenderer::render('user_login', ['message' => $message ?? [] ]);
  }

  private function check_session()
  {
    if (isset($_SESSION['user']) and $_SESSION['user']) {
      header('Location: ' . BASE . '/panel/display');
      exit();
    }
  }
}
