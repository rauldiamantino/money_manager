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
    if (isset($_SESSION['user']) and $_SESSION['user']) {
      header('Location: ' . $this->base_uri . '/panel/display');
      exit();
    }

    $user_first_name = $_POST['user_first_name'] ?? '';
    $user_last_name = $_POST['user_last_name'] ?? '';
    $user_email = $_POST['user_email'] ?? '';
    $user_password = $_POST['user_password'] ?? '';
    $user_confirm_password = $_POST['user_confirm_password'] ?? '';

    $data = [
      'user_first_name' => $user_first_name,
      'user_last_name' => $user_last_name,
      'user_email' => $user_email,
      'user_password' => $user_password,
    ];


    $message = [];
    $response = [];

    if ($_POST) {
      $message = ['error_password' => 'Digite a mesma senha nos dois campos'];
    }

    if ($user_password == $user_confirm_password and $_POST) {
      $response = $this->usersModel->register_user($data);
    }

    if (isset($response['error_register'])) {
      $message = ['error_register' => $response['error_register']];
    }

    if (isset($response['success_register'])) {
      $message = ['success_register' => $response['success_register']];
    }

    require_once '../app/views/user_register.php';
  }

  public function login()
  {
    if (isset($_SESSION['user']) and $_SESSION['user']) {
      header('Location: ' . $this->base_uri . '/panel/display');
      exit();
    }

    $user_email = $_POST['user_email'] ?? '';
    $user_password = $_POST['user_password'] ?? '';

    $data = [
      'user_email' => $user_email,
      'user_password' => $user_password,
    ];

    $message = [];
    $response = $this->usersModel->login_user($data);

    if (isset($response['error_login']) and $user_email) {
      $message = ['error_login' => $response['error_login']];
    }

    if (isset($response['success_login']) and empty($_SESSION['user'])) {
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

    require_once '../app/views/user_login.php';
  }
}
