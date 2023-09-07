<?php
require_once '../app/helpers/Logger.php';

class Database
{
  private $connection;
  private $host;
  private $dbname;
  private $username;
  private $password;

  
  // Inicia classe e já estabelece a conexão com o database principal, onde os usuários são adicionados 
  public function __construct()
  {
    $this->host = DB_HOST;
    $this->dbname = DB_NAME;
    $this->username = DB_USER;
    $this->password = DB_PASSWORD;

    try {
      $this->connection = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';charset=utf8', $this->username, $this->password);
      $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      return true;
    } 
    catch (PDOException $e) {
      Logger::log('Database->__construct: ' . $e->getMessage());
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
      $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      return true;
    } 
    catch (PDOException $e) {
      Logger::log('Database::switch_database: ' . $e->getMessage());
      $this->check_invalid_database($e->getCode());

      return false;
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
      $stmt->execute();
      return true;
    }
    catch (PDOException $e) {
      Logger::log('Database->insert: ' . $e->getMessage());
      $this->check_invalid_database($e->getCode());

      return false;
    }
  }

  // Realiza buscas no banco de dados
  public function select($sql, $params = [])
  {
    $params_consult = $params['params'] ?? '';
    $database_name = $params['database_name'] ?? '';

    try {

      if ($database_name) {
        $this->switch_database($database_name);
      }
      
      $stmt = $this->connection->prepare($sql);

      if ($params_consult) {
        foreach ($params_consult as $key => &$value) {
          $stmt->bindParam(":$key", $value);
        }
      }
 
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

      return $result;
    }
    catch (PDOException $e) {
      Logger::log('Database->select: ' . $e->getMessage());
      $this->check_invalid_database($e->getCode());

      return false;
    }
  }

  // Cria novo banco de dados
  public function create_database($database)
  {
    $sql = 'CREATE DATABASE IF NOT EXISTS ' . $database;

    try {
      $stmt = $this->connection->prepare($sql);
      $stmt->execute();

      return true;
    } 
    catch (PDOException $e) {
      Logger::log('Database->create_database' . $e->getMessage());
      $this->check_invalid_database($e->getCode());

      return false;
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

      // Cria as tabelas
      $this->connection->exec($create_categories_table);
      $this->connection->exec($create_accounts_table);
      $this->connection->exec($create_expenses_table);
      $this->connection->exec($create_incomes_table);

      return true;
    }
    catch (PDOException $e) {
      Logger::log('Database->create_user_tables: ' . $e->getMessage());
      $this->check_invalid_database($e->getCode());

      return false;
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