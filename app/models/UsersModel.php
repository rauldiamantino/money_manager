<?php
require_once '../app/dao/UsersDAO.php';

class UsersModel {
  protected $user_email;
  protected $usersDao;

  public function __construct()
  {
    $this->usersDao = new UsersDAO();
  }

  public function register_user($data)
  {
    // print_r($data);
    $this->user_email = $data['user_email'] ?? '';
    $get_user = $this->get_user();

    $response = [];

    if ($get_user) {
      $response = ['error_register' => 'E-mail já cadastrado'];
    }
    
    if (empty($get_user) and $this->usersDao->register_user_db($data)) {
      $response = ['success_register' => 'Cadastro realizado com sucesso!'];
    }
    
    return $response;
  }

  private function get_user() {
    $response = $this->usersDao->get_user_db($this->user_email);

    return $response;
  }
}