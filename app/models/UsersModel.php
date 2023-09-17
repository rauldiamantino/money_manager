<?php
require_once '../app/Database.php';

class UsersModel
{
  public $database;

  public function __construct()
  {
    $this->database = new Database();
  }

  // Cadastra o usuário
  public function registerUser($user)
  {
    $sql = 'INSERT INTO users (first_name, last_name, email, password) 
            VALUES (:first_name, :last_name, :email, :password)';

    $params = [
      'first_name' => $user['firstName'],
      'last_name' => $user['lastName'],
      'email' => $user['email'],
      'password' => password_hash($user['password'], PASSWORD_DEFAULT),
    ];

    $result = $this->database->insert($sql, $params);
    Logger::log(['method' => 'UsersModel->registerUser', 'result' => $result]);

    return $result;
  }

  // Cria database do usuário após ter sido cadastrado
  public function createUserDatabase($databaseName)
  {
    $sql = 'CREATE DATABASE IF NOT EXISTS ' . $databaseName;

    $result = $this->database->createDatabase($sql);
    Logger::log(['method' => 'UsersModel->createUserDatabase', 'result' => $result]);

    return $result;
  }

  // Cria tabelas padrões do usuário
  public function createUserTables($databaseName)
  {
    $sql = [
      'expenses' => 'CREATE TABLE expenses (
                                    id INT AUTO_INCREMENT PRIMARY KEY,
                                    description VARCHAR(255) NOT NULL,
                                    amount DECIMAL(10, 2) NOT NULL,
                                    type VARCHAR(255) NOT NULL,
                                    category_id INT,
                                    account_id INT,
                                    date DATE NOT NULL,
                                    status INT,
                                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                                  )',
      'incomes' => 'CREATE TABLE incomes (
                                   id INT AUTO_INCREMENT PRIMARY KEY,
                                   description VARCHAR(255) NOT NULL,
                                   amount DECIMAL(10, 2) NOT NULL,
                                   type VARCHAR(255) NOT NULL,
                                   category_id INT,
                                   account_id INT,
                                   date DATE NOT NULL,
                                   status INT,
                                   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                   updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                                 )',
      'accounts' => 'CREATE TABLE accounts (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255) NOT NULL)',
      'categories' => 'CREATE TABLE categories (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255) NOT NULL)',
    ];

    $result = $this->database->createTables($databaseName, $sql);

    if ($result) {
      $result = $this->defaultCategory() and $this->defaultAccount() ? true : false;
    }

    Logger::log(['method' => 'UsersModel->createUserTables', 'result' => $result]);

    return $result;
  }

  // Cria conta padrão para cada usuário adicionado
  private function defaultAccount()
  {
    $sql = 'INSERT INTO accounts (name) VALUES (:name)';
    $params = ['name' => 'Conta Corrente'];

    $result = $this->database->insert($sql, $params);
    Logger::log(['method' => 'UsersPanel->defaultAccount', 'result' => $result ]);

    return $result;
  }

  // Cria categoria padrão para cada usuário adicionado
  private function defaultCategory()
  {
    $sql = 'INSERT INTO categories (name) VALUES (:name)';
    $params = ['name' => 'Geral'];

    $result = $this->database->insert($sql, $params);
    Logger::log(['method' => 'UsersPanel->add_default_category', 'result' => $result ]);

    return $result;
  }

  // Obtém os dados da conta do usuário
  public function getMyaccount($userId)
  {
    $databaseName = DB_NAME;
    $sql = 'SELECT * FROM users WHERE id = :id';
    $params = ['id' => $userId ];

    $this->database->switchDatabase($databaseName);
    $result = $this->database->select($sql, ['params' => $params, 'database_name' => $databaseName ]);

    Logger::log(['method' => 'PanelModel->getMyaccount', 'result' => $result ]);

    return $result;
  }

  // Atualiza os dados da conta do usuário
  public function updateMyaccount($newData)
  {
    $sql = 'UPDATE users
            SET first_name = :first_name, last_name = :last_name, email = :email
            WHERE id = :id';

    $params = [
      'first_name' => $newData['user_first_name'],
      'last_name' => $newData['user_last_name'],
      'email' => $newData['user_email'],
      'id' => $newData['user_id'],
    ];

    $result = $this->database->insert($sql, $params);
    Logger::log(['method' => 'PanelModel->updateMyaccount', 'result' => $result ]);

    return true;
  }

  // Atualiza senha do conta do usuário
  public function updateMyaccountPassword($newData)
  {
    $sql = 'UPDATE users
            SET password = :password
            WHERE id = :id';

    $params = [
      'password' => password_hash($newData['user_new_password'], PASSWORD_DEFAULT),
      'id' => $newData['user_id'],
    ];

    $result = $this->database->insert($sql, $params);
    Logger::log(['method' => 'PanelModel->updateMyaccountPassword', 'result' => $result ]);

    return $result;
  }

  // Busca o usuário
  public function getUser($userEmail, $userId = 0)
  {
    // Busca por e-mail
    $where = 'WHERE email = :email';
    $params = ['email' => $userEmail ];

    // Busca por id
    if ($userId) {
      $where = 'WHERE id = :id';
      $params = ['id' => $userId ];
    }

    $sql = 'SELECT * FROM users ' . $where;
    $result = $this->database->select($sql, ['params' => $params ]);
    $log = $result;

    if ($log) {
      $log[0]['password'] = '**************';
    }

    Logger::log(['method' => 'PanelModel->getUser', 'result' => $log ]);

    return $result;
  }
}