<?php
class UsersController {
  protected $usersModel;

  public function __construct()
  {
    require_once '../app/models/UsersModel.php';
    $this->usersModel = new UsersModel();
  }

  public function registration()
  {
    $register = '';

    $data = [
      'name_user' => $_POST['name_user'] ?? '',
      '$email_user' => $_POST['email_user'] ?? '',
      'password_user' => $_POST['password_user'] ?? '',
      'confirm_password_user' => $_POST['confirm_password_user'] ?? '',
    ];

    $register = 'As senhas não conferem';

    if ($data['password_user'] == $data['confirm_password_user']) {
      $register = $this->usersModel->get_data_register($data);
    }
    
    require_once '../app/views/user_register.php';
  }
}