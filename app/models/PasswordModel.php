<?php
require_once '../app/models/Model.php';

class PasswordModel extends Model
{

  // Atualiza senha do conta do usuário
  public function updatePassword($newData)
  {
    $sql = 'UPDATE users
            SET password = :password
            WHERE id = :id';

    $params = [
      'password' => password_hash($newData['newPassword'], PASSWORD_DEFAULT),
      'id' => $newData['userId'],
    ];

    $result = $this->database->insert($sql, $params);
    Logger::log(['method' => 'PasswordModel->updatePassword', 'result' => $result ]);

    return $result;
  }
}