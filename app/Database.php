<?php

class Database
{
  protected $connection;
  protected $host;
  protected $database;
  protected $username;
  protected $password;

  public function __construct($database_name = null)
  {
    $this->host = DB_HOST;
    $this->database = $database_name ?? DB_NAME;
    $this->username = DB_USER;
    $this->password = DB_PASSWORD;

    $this->connection = new mysqli($this->host, $this->username, $this->password, $this->database);

    if ($this->connection->connect_error) {
      die('Falha na conexão com o banco de dados: ' . $this->connection->connect_error);
    }
  }

  public function query($sql)
  {
    $result = $this->connection->query($sql);

    if ($result === false) {
      die('Erro na consulta: ' . $this->connection->error);
    }

    return $result;
  }

  public function close()
  {
    $this->connection->close();
  }
}

class Database2
{
  private $connection;

  public function __construct($host, $dbname, $username, $password)
  {
    try {
      $this->connection = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
      $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
      die("Erro na conexão: " . $e->getMessage());
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
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
