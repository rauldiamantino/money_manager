<?php
require_once '../app/Database.php';

class UsersDAO
{
  private $database;

  public function __construct()
  {
    $this->database = new Database();
  }

  // Busca o usuário no Banco de Dados
  public function get_user_db($email, $user_id = 0)
  {
    $where = 'WHERE email = :email';
    $params = ['email' => $email];

    // Busca usuãrio por id
    if ($user_id) {
      $where = 'WHERE id = :id';
      $params = ['id' => $user_id];
    }

    $sql = 'SELECT * FROM users ' . $where;
    $result = $this->database->select($sql, ['params' => $params ]);

    if (empty($result)) {
      Logger::log('UsersDao->get_user_db: Usuário inexistente');
    }

    return $result;
  }
}