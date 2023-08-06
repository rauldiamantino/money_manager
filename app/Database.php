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
    } catch (PDOException $e) {
      die('Error connection: ' . $e->getMessage());
    }
  }

  public function insert($table, $data)
  {
    $columns = implode(", ", array_keys($data));
    $placeholders = ":" . implode(", :", array_keys($data));

    $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
    $stmt = $this->connection->prepare($sql);

    foreach ($data as $key => &$value) {
      $stmt->bindParam(":$key", $value);
    }

    return $stmt->execute();
  }

  public function select($params = [], $database_name = '')
  {
    $columns = $params['columns'] ?? '*';
    $table = $params['table'] ?? 'users';
    $where = $params['where'] ?? '';
    $sql_param = $params['sql_param'] ?? '';

    $sql = 'SELECT ' . $columns . ' FROM ' . $table;

    if ($where) {
      $sql .= ' WHERE ' . $where;
    }

    if ($sql_param) {
      $sql = $sql_param;
      $this->connection->exec('USE ' . $database_name);
    }

    $stmt = $this->connection->prepare($sql);

    try {
      $stmt->execute();
    } catch (PDOException $e) {
      die('Error: ' . $e->getMessage());
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function create_database($database)
  {
    $sql = 'CREATE DATABASE ' . $database;

    try {
      $stmt = $this->connection->prepare($sql);
      $stmt->execute();
      return $stmt;
    } catch (PDOException $e) {
      die('Error creating database: ' . $e->getMessage());
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
                                  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                                  FOREIGN KEY (category_id) REFERENCES categories(id),
                                  FOREIGN KEY (account_id) REFERENCES accounts(id)
                                )';

      $create_incomes_table = 'CREATE TABLE incomes (
                                id INT AUTO_INCREMENT PRIMARY KEY,
                                description VARCHAR(255) NOT NULL,
                                amount DECIMAL(10, 2) NOT NULL,
                                category_id INT,
                                account_id INT,
                                date DATE NOT NULL,
                                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                                FOREIGN KEY (category_id) REFERENCES categories(id),
                                FOREIGN KEY (account_id) REFERENCES accounts(id)
                              )';

      $this->connection->exec($create_categories_table);
      $this->connection->exec($create_accounts_table);
      $this->connection->exec($create_expenses_table);
      $this->connection->exec($create_incomes_table);

      return true;
    } catch (PDOException $e) {
      die('Error creating user tables: ' . $e->getMessage());
      return false;
    }
  }
}
