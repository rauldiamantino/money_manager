<?php

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
      $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } 
    catch (PDOException $e) {
      die('Database Error: ' . $e->getMessage());
    }
  }

  public function switch_database($database_name)
  {
    // encerra conexão atual
    $this->connection = null;

    // novo database
    $this->dbname = $database_name;
    
    // nova conexão
    try {
      $this->connection = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';charset=utf8', $this->username, $this->password);
      $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } 
    catch (PDOException $e) {
      die('Error connection: ' . $e->getMessage());
    }
  }

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
      die('Database Error: ' . $e->getMessage());
    }
  }

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
    }
    catch (PDOException $e) {
      die('Database Error: ' . $e->getMessage());
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function create_database($database)
  {
    $sql = 'CREATE DATABASE IF NOT EXISTS ' . $database;

    try {
      $stmt = $this->connection->prepare($sql);
      $stmt->execute();
      return $stmt;
    } 
    catch (PDOException $e) {
      die('Database Error: ' . $e->getMessage());
    }
  }

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
      die('Database Error: ' . $e->getMessage());
      return false;
    }
  }
}