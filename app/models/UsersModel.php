<?php
require_once '../app/dao/UsersDAO.php';

class UsersModel
{
  protected $user_email;
  protected $usersDao;
  protected $database_user;

  public function __construct()
  {
    $this->usersDao = new UsersDAO();
  }

  public function register_user($data)
  {
    $this->user_email = $data['user_email'] ?? '';
    $get_user = $this->get_user();

    $response = [];

    if ($get_user) {
      $response = ['error_register' => 'E-mail já cadastrado'];
    }

    if (empty($get_user) and $this->usersDao->register_user_db($data)) {
      $user = $this->get_user();
      $database_user = 'm_user_' . $user[0]['id'];
      $this->create_database_user($database_user);

      $response = ['success_register' => 'Cadastro realizado com sucesso!'];
    }

    return $response;
  }

  private function create_database_user($database_user)
  {
    $result = $this->usersDao->create_database_user($database_user);
    $response = ['error_register' => $result];

    if ($result) {
      $response = ['success_create' => 'Database criado com sucesso'];
    }

    return $response;
  }

  public function login_user($data)
  {
    $this->user_email = $data['user_email'] ?? '';
    $get_user = $this->get_user();

    if (empty($get_user)) {
      return ['error_login' => 'Dados inválidos'];
    }

    $validation_user = ['user_email' => false, 'user_password' => false];

    if ($data['user_email'] == $get_user[0]['email']) {
      $validation_user['user_email'] = true;
    }

    if (password_verify($data['user_password'], $get_user[0]['password'])) {
      $validation_user['user_password'] = true;
    }

    $response = [
      'success_login' => [
        'message' => 'Dados corretos!',
        'user_id' => $get_user[0]['id'],
        'user_first_name' => $get_user[0]['first_name'],
        'user_last_name' => $get_user[0]['last_name'],
        'user_email' => $get_user[0]['email'],
      ],
    ];
    foreach ($validation_user as $linha) :
      if (empty($linha)) {
        $response = ['error_login' => 'Dados inválidos'];
      }
    endforeach;

    return $response;
  }

  private function get_user()
  {
    $response = $this->usersDao->get_user_db($this->user_email);
    return $response;
  }
}
