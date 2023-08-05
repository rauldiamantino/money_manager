<?php

class Database
{
  private $connection;

  public function __construct($host, $dbname, $username, $password)
  {
    try {
      $this->connection = new PDO('mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8', $username, $password);
      $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } 
    catch (PDOException $e) {
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

  public function select($params = [])
  {
    $columns = $params['columns'] ?? '*';
    $table = $params['table'] ?? 'users';
    $where = $params['where'] ?? '';

    $sql = 'SELECT ' . $columns . ' FROM ' . $table;

    if ($where) {
      $sql .= ' WHERE ' . $where;
    }

    $stmt = $this->connection->prepare($sql);
    
    try {
      $stmt->execute();
    } 
    catch (PDOException $e) {
      die('Error: ' . $e->getMessage());
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
