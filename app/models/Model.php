<?php
require_once '../app/Database.php';

class Model
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

    Logger::log(['method' => 'PanelModel->getUser', 'result' => $log ]);

    return $result;
  }
}