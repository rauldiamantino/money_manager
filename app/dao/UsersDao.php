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
    $where = 'WHERE email = :email';
    $params = ['email' => $email];

    // Busca usuãrio por id
    if ($user_id) {
      $where = 'WHERE id = :id';
      $params = ['id' => $user_id];
    }

    $sql = 'SELECT * FROM users ' . $where;
    $result = $this->database->select($sql, ['params' => $params ]);

    if (empty($result)) {
      Logger::log(['method' => 'UsersDao->get_user_db', 'result' => $result ]);
    }

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

    if (empty($result)) {
      Logger::log(['method' => 'UsersDao->register_user_db', 'result' => $result ]);
    }

    return $result;
  }

  // Cria o database para cada usuário adicionado
  public function create_database_user($database)
  {
    $result = $this->database->create_user_tables($database);

    if ($result) {
      $this->add_default_category();
      $this->add_default_account();
    }

    if (empty($result)) {
      Logger::log(['method' => 'UsersDao->create_database_user', 'result' => $result ]);
    }

    return $result;
  }

  // Cria conta padrão para cada usuário adicionado
  private function add_default_account()
  {
    $sql = 'INSERT INTO accounts (name) VALUES (:name)';
    $params = ['name' => 'Conta Corrente'];
    $result = $this->database->insert($sql, $params);

    if (empty($result)) {
      Logger::log(['method' => 'UsersDao->add_default_account', 'result' => $result ]);
    }
  }

  // Cria categoria padrão para cada usuário adicionado
  private function add_default_category()
  {
    $sql = 'INSERT INTO categories (name) VALUES (:name)';
    $params = ['name' => 'Geral'];
    $result = $this->database->insert($sql, $params);

    if (empty($result)) {
      Logger::log(['method' => 'UsersDao->add_default_category', 'result' => $result ]);
    }
  }
}