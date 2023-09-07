<?php
require_once '../app/Database.php';

class UsersDAO
{
  private $database;

  public function __construct()
  {
    $this->database = new Database();
  }

  // Busca o usuário no Banco de Dados
  public function get_user_db($email, $user_id = 0)
  {
    $sql = 'SELECT * FROM users WHERE email = :email';
    $params = ['email' => $email];

    if ($user_id) {
      $sql = 'SELECT * FROM users WHERE id = :id';
      $params = ['id' => $user_id];
    }

    $result = $this->database->select($sql, ['params' => $params ]);

    return $result;
  }

  // Adiciona usuário no Banco de Dados
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

  // Atualiza usuário no Banco de Dados
  public function update_users_db($new_data)
  {
    $sql = 'UPDATE users
            SET first_name = :first_name, last_name = :last_name, email = :email
            WHERE id = :id';

    $params = [
      'first_name' => $new_data['user_first_name'],
      'last_name' => $new_data['user_last_name'],
      'email' => $new_data['user_email'],
      'id' => $new_data['user_id'],
    ];

    $result = $this->database->insert($sql, $params);
    return $result;
  }

  // Atualiza senha do usuário no Banco de Dados
  public function update_password_user_db($new_data)
  {
    $sql = 'UPDATE users
            SET password = :password
            WHERE id = :id';

    $params = [
      'password' => password_hash($new_data['user_new_password'], PASSWORD_DEFAULT),
      'id' => $new_data['user_id'],
    ];

    $result = $this->database->insert($sql, $params);
    return $result;
  }

  // Cria o database para cada usuário adicionado
  public function create_database_user($database)
  {
    $result = $this->database->create_user_tables($database);

    if ($result) {
      $this->add_default_category($database);
      $this->add_default_account($database);
    }

    return $result;
  }

  // Cria conta padrão para cada usuário adicionado
  private function add_default_account($database)
  {
    $sql = 'INSERT INTO accounts (name) VALUES (:name)';
    $params = ['name' => 'Conta Corrente'];
    $this->database->insert($sql, $params);
  }

  // Cria categoria padrão para cada usuário adicionado
  private function add_default_category($database)
  {
    $sql = 'INSERT INTO categories (name) VALUES (:name)';
    $params = ['name' => 'Geral'];
    $this->database->insert($sql, $params);
  }
}
