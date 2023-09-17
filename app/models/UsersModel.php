<?php
require_once '../app/Database.php';
require_once '../app/models/Model.php';

class UsersModel extends Model
{
  public $database;

  public function __construct()
  {
    $this->database = new Database();
  }

  // Obtém os dados da conta do usuário
  public function getMyaccount($userId)
  {
    $databaseName = DB_NAME;
    $sql = 'SELECT * FROM users WHERE id = :id';
    $params = ['id' => $userId ];

    $this->database->switchDatabase($databaseName);
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
}