<?php
require_once '../app/helpers/Logger.php';

class Database
{
  private $connection;
  private $host;
  private $dbname;
  private $username;
  private $password;

  public function __construct()
  {
    $this->host = DB_HOST;
    $this->dbname = DB_NAME;
    $this->username = DB_USER;
    $this->password = DB_PASSWORD;

    try {
      $this->connection = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';charset=utf8', $this->username, $this->password);
      return $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } 
    catch (PDOException $e) {
      $error_message = 'Database Error: ' . $e->getMessage();
      Logger::log($error_message);
      $this->check_invalid_database($e->getCode());
      return false;
    }
  }

  // Encerra conexão atual e alterna banco de dados
  public function switch_database($database_name)
  {
    $this->connection = null;
    $this->dbname = $database_name;

    try {
      $this->connection = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';charset=utf8', $this->username, $this->password);
      return $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } 
    catch (PDOException $e) {
      $error_message = 'Error connection: ' . $e->getMessage();
      Logger::log($error_message);
      $this->check_invalid_database($e->getCode());
    }
  }

  // Adiciona entradas no banco de dados
  public function insert($sql, $params = [])
  {
    $stmt = $this->connection->prepare($sql);

    foreach ($params as $key => &$value) {
      $stmt->bindParam(":$key", $value);
    }

    try {
      return $stmt->execute();
    }
    catch (PDOException $e) {
      $error_message = 'Database Error: ' . $e->getMessage();
      Logger::log($error_message);
      $this->check_invalid_database($e->getCode());
    }
  }

  // Realiza buscas no banco de dados
  public function select($sql, $params = [])
  {
    $params_consult = $params['params'] ?? '';
    $database_name = $params['database_name'] ?? '';

    $stmt = $this->connection->prepare($sql);

    if ($params_consult) {
      foreach ($params_consult as $key => &$value) {
        $stmt->bindParam(":$key", $value);
      }
    }

    try {

      if ($database_name) {
        $this->connection->exec('USE ' . $database_name);
      }
 
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (PDOException $e) {
      $error_message = 'Database Error: ' . $e->getMessage();
      Logger::log($error_message);
      $this->check_invalid_database($e->getCode());
    }
  }

  // Cria novo banco de dados
  public function create_database($database)
  {
    $sql = 'CREATE DATABASE IF NOT EXISTS ' . $database;

    try {
      $stmt = $this->connection->prepare($sql);
      $stmt->execute();
      return $stmt;
    } 
    catch (PDOException $e) {
      $error_message = 'Database Error: ' . $e->getMessage();
      Logger::log($error_message);
      $this->check_invalid_database($e->getCode());
    }
  }

  // Cria tabelas do usuário após se cadastrar
  public function create_user_tables($database)
  {
    try {
      $this->create_database($database);
      $this->connection->exec('USE ' . $database);

      $create_categories_table = 'CREATE TABLE categories (
                                    id INT AUTO_INCREMENT PRIMARY KEY,
                                      name VARCHAR(255) NOT NULL
                                  )';

      $create_accounts_table = 'CREATE TABLE accounts (
                                  id INT AUTO_INCREMENT PRIMARY KEY,
                                    name VARCHAR(255) NOT NULL
                                )';

      $create_expenses_table = 'CREATE TABLE expenses (
                                  id INT AUTO_INCREMENT PRIMARY KEY,
                                  description VARCHAR(255) NOT NULL,
                                  amount DECIMAL(10, 2) NOT NULL,
                                  category_id INT,
                                  account_id INT,
                                  date DATE NOT NULL,
                                  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                                )';

      $create_incomes_table = 'CREATE TABLE incomes (
                                id INT AUTO_INCREMENT PRIMARY KEY,
                                description VARCHAR(255) NOT NULL,
                                amount DECIMAL(10, 2) NOT NULL,
                                category_id INT,
                                account_id INT,
                                date DATE NOT NULL,
                                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                              )';

      $this->connection->exec($create_categories_table);
      $this->connection->exec($create_accounts_table);
      $this->connection->exec($create_expenses_table);
      $this->connection->exec($create_incomes_table);

      return true;
    }
    catch (PDOException $e) {
      $error_message = 'Database Error: ' . $e->getMessage();
      Logger::log($error_message);
      $this->check_invalid_database($e->getCode());
    }
  }

  // Redireciona para página de erro caso o db não exista
  private function check_invalid_database($error_code)
  {
    if ($error_code == '1049' or $error_code == '42S02') {
      unset($_SESSION['user']);
      session_destroy();
      
      header('Location: ' . BASE . '/error/not_found');
      exit();
    }
  }
}