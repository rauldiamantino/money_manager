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
      Logger::log(['method' => 'Database->__construct', 'result' => $e->getMessage()]);
      $this->check_invalid_database($e->getCode());

      return false;
    }
  }

  // Encerra conexão atual e alterna banco de dados
  public function switch_database($databaseName)
  {
    $this->connection = null;
    $this->dbname = $databaseName;

    try {
      $this->connection = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';charset=utf8', $this->username, $this->password);
      $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      return true;
    } 
    catch (PDOException $e) {
      Logger::log(['method' => 'Database->switch_database', 'result' => $e->getMessage()]);
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
      Logger::log(['method' => 'Database->insert', 'result' => $e->getMessage()]);
      $this->check_invalid_database($e->getCode());

      return false;
    }
  }

  // Apaga transação do banco de dados
  public function delete($sql, $params = [])
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
      Logger::log(['method' => 'Database->delete', 'result' => $e->getMessage()]);
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
      Logger::log(['method' => 'Database->select', 'result' => $e->getMessage()]);
      $this->check_invalid_database($e->getCode());

      return false;
    }
  }

  // Cria novo banco de dados
  public function createDatabase($sql)
  {
    try {
      $stmt = $this->connection->prepare($sql);
      $stmt->execute();

      return true;
    } 
    catch (PDOException $e) {
      Logger::log(['method' => 'Database->createDatabase', 'result' => $e->getMessage()]);
      $this->check_invalid_database($e->getCode());

      return false;
    }
  }

  // Cria tabelas do usuário após se cadastrar
  public function createTables($database, $sql)
  {
    try {
      $this->connection->exec('USE ' . $database);

      // Cria as tabelas
      foreach ($sql as $value) :
        $this->connection->exec($value);
      endforeach;
      
      return true;
    }
    catch (PDOException $e) {
      Logger::log(['method' => 'Database->createTables', 'result' => $e->getMessage()]);
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