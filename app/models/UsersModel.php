<?php
require_once '../app/models/Model.php';

class UsersModel extends Model
{

  // Busca o usuário
  public function getUser($userEmail, $userId = 0)
  {
    // Busca por e-mail
    $where = 'WHERE email = :email';
    $params = ['email' => $userEmail ];

    // Busca por id
    if ($userId) {
      $where = 'WHERE id = :id';
      $params = ['id' => $userId ];
    }

    $sql = 'SELECT * FROM users ' . $where;
    $result = $this->database->select($sql, ['params' => $params ]);
    $log = $result;

    if ($log) {
      $log[0]['password'] = '**************';
    }

    Logger::log(['method' => 'UsersModel->getUser', 'result' => $log ]);

    return $result;
  }

  // Armazena ID de sessão após o login
  public function saveSession($userId, $sessionId)
  {
    $sql = 'UPDATE users
              SET session_id = :session_id
              WHERE id = :id;';

    $params = [
      'id' => $userId,
      'session_id' => $sessionId,
    ];

    $result = $this->database->insert($sql, $params);
    Logger::log(['method' => 'UsersModel->saveSession', 'result' => $result ]);

    return $result;
  }
}