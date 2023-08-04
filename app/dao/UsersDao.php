<?php
require_once '../app/Database.php';

class UsersDAO
{
  private $database;
  private $database2;

  public function __construct()
  {
    $this->database = new Database(); 
    $this->database2 = new Database2(DB_HOST, DB_NAME, DB_USER, DB_PASSWORD);
  }

  public function get_user_db($email)
  {
    $params = ['where' => 'email = \'' . $email . '\''];
    $result = $this->database2->select($params);
    return $result;
  }

  public function register_user_db($user_data)
  {
    $user = [
      'name' => $user_data['user_name'],
      'email' => $user_data['user_email'],
      'password' => password_hash($user_data['user_password'], PASSWORD_DEFAULT),
    ];

    $result = $this->database2->insert('users', $user);
    return $result;
  }
}