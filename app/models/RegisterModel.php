<?php
require_once '../app/Database.php';
require_once '../app/models/UsersModel.php';

class RegisterModel extends UsersModel
{

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
    Logger::log(['method' => 'RegisterModel->registerUser', 'result' => $result]);

    return $result;
  }

  // Cria database do usuário após ter sido cadastrado
  public function createUserDatabase($databaseName)
  {
    $sql = 'CREATE DATABASE IF NOT EXISTS ' . $databaseName;

    $result = $this->database->createDatabase($sql);
    Logger::log(['method' => 'RegisterModel->createUserDatabase', 'result' => $result]);

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

    Logger::log(['method' => 'RegisterModel->createUserTables', 'result' => $result]);

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
}