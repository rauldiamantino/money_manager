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
    $data = [
      'user_name' => $_POST['user_name'] ?? '',
      'user_email' => $_POST['user_email'] ?? '',
      'user_password' => $_POST['user_password'] ?? '',
      'confirm_user_password' => $_POST['confirm_user_password'] ?? '',
    ];

    $message = ['error_password' => 'Digite a mesma senha nos dois campos'];
    $response = [];

    if ($data['user_password'] == $data['confirm_user_password']) {
      $response = $this->usersModel->register_user($data);
    }

    if (isset($response['error_register'])) {
      $message = ['error_register' => $response['error_register']];
    }

    if (isset($response['success_register'])) {
      $_SESSION['alert_message'] = $response['success_register'];
      $message = ['success_register' => $_SESSION['alert_message']];
      header('Location: ' . $this->base_uri);
    }

    require_once '../app/views/user_register.php';
  }
}
