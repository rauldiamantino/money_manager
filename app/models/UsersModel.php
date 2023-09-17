<?php
require_once '../app/dao/UsersDAO.php';
require_once '../app/Database.php';

class UsersModel
{
  public $user_email;
  public $usersDao;
  public $database;
  public $database_user;

  public function __construct()
  {
    $this->usersDao = new UsersDAO();
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
    Logger::log(['method' => 'UsersDao->defaultAccount', 'result' => $result ]);

    return $result;
  }

  // Cria categoria padrão para cada usuário adicionado
  private function defaultCategory()
  {
    $sql = 'INSERT INTO categories (name) VALUES (:name)';
    $params = ['name' => 'Geral'];

    $result = $this->database->insert($sql, $params);
    Logger::log(['method' => 'UsersDao->add_default_category', 'result' => $result ]);

    return $result;
  }

  // Verifica se o usuário possui conta e faz login
  public function login_user($data)
  {
    $this->user_email = $data['user_email'] ?? '';
    $get_user = $this->get_user();
    $response = [];

    if (empty($get_user)) {
      $response = ['error_login' => 'Dados inválidos'];
      Logger::log(['method' => 'PanelModel->login_user', 'result' => $response ]);

      return $response;
    }

    $validation_user = ['user_email' => false, 'user_password' => false];

    if ($data['user_email'] == $get_user[0]['email']) {
      $validation_user['user_email'] = true;
    }

    if (password_verify(trim($data['user_password']), $get_user[0]['password'])) {
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
        Logger::log(['method' => 'PanelModel->login_user', 'result' => $response ]);
      }
    endforeach;

    return $response;
  }

  // Obtém os dados da conta do usuário
  public function getMyaccount($userId)
  {
    $databaseName = DB_NAME;
    $sql = 'SELECT * FROM users WHERE id = :id';
    $params = ['id' => $userId ];

    $this->database->switch_database($databaseName);
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

    Logger::log(['method' => 'PanelModel->getUser', 'result' => $result ]);

    return $result;
  }

  // Busca usuário no Banco de Dados
  private function get_user($user_id = 0)
  {
    $response = $this->usersDao->get_user_db($this->user_email, $user_id);

    if (empty($response)) {
      Logger::log(['method' => 'PanelModel->get_user', 'result' => $response ]);
    }

    return $response;
  }
}