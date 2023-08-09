<?php
class UsersController
{
  protected $usersModel;
  protected $base_uri;

  public function __construct()
  {
    require_once '../app/models/UsersModel.php';
    $this->usersModel = new UsersModel();
    $this->base_uri = str_replace('/public/', '', dirname($_SERVER['SCRIPT_NAME']));
  }

  public function registration()
  {
    $this->check_session();

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

    require_once '../app/views/user_register.php';
  }

  public function login()
  {
    $this->check_session();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

          header('Location: ' . $this->base_uri . '/panel/display');
          exit();
        }
      }
      catch (Exception $e) {
        $message = ['error_login' => 'Erro ao fazer login: ' . $e->getMessage()];
      }
    }

    require_once '../app/views/user_login.php';
  }

  private function check_session()
  {
    if (isset($_SESSION['user']) and $_SESSION['user']) {
      header('Location: ' . $this->base_uri . '/panel/display');
      exit();
    }
  }
}
