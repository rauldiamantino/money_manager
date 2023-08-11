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
    $sql = 'SELECT * FROM users WHERE email = :email';
    $params = ['email' => $email];
    $result = $this->database->select($sql, ['params' => $params ]);

    return $result;
  }

  public function register_user_db($user_data)
  {
    $sql = 'INSERT INTO users (first_name, last_name, email, password) 
            VALUES (:first_name, :last_name, :email, :password)';

    $params = [
      'first_name' => $user_data['user_first_name'],
      'last_name' => $user_data['user_last_name'],
      'email' => $user_data['user_email'],
      'password' => password_hash($user_data['user_password'], PASSWORD_DEFAULT),
    ];

    $result = $this->database->insert($sql, $params);
    return $result;
  }

  public function create_database_user($database)
  {
    $result = $this->database->create_user_tables($database);

    if ($result) {
      $this->add_default_category($database);
      $this->add_default_account($database);
    }

    return $result;
  }

  private function add_default_account($database)
  {
    $sql = 'INSERT INTO accounts (name) VALUES (:name)';
    $params = ['name' => 'Conta Corrente'];
    $this->database->insert($sql, $params);
  }

  private function add_default_category($database)
  {
    $sql = 'INSERT INTO categories (name) VALUES (:name)';
    $params = ['name' => 'Geral'];
    $this->database->insert($sql, $params);
  }
}
