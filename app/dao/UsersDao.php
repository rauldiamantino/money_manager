<?php
require_once '../app/Database.php';

class UsersDAO
{
  private $database;

  public function __construct()
  {
    $this->database = new Database();
  }

  public function get_user_db($email)
  {
    $params = ['where' => 'email = \'' . $email . '\''];
    $result = $this->database->select($params);
    return $result;
  }

  public function register_user_db($user_data)
  {
    $user = [
      'first_name' => $user_data['user_first_name'],
      'last_name' => $user_data['user_last_name'],
      'email' => $user_data['user_email'],
      'password' => password_hash($user_data['user_password'], PASSWORD_DEFAULT),
    ];

    $result = $this->database->insert('users', $user);
    return $result;
  }

  public function create_database_user($database)
  {
    $result = $this->database->create_user_tables($database);
    return $result;
  }
}
