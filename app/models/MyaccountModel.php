<?php
require_once '../app/models/Model.php';

class MyaccountModel extends Model
{

  // Atualiza os dados da conta do usuário
  public function updateMyaccount($newData)
  {
    $sql = 'UPDATE users
            SET first_name = :first_name, last_name = :last_name, email = :email
            WHERE id = :id';

    $params = [
      'first_name' => $newData['firstName'],
      'last_name' => $newData['lastName'],
      'email' => $newData['email'],
      'id' => $newData['userId'],
    ];

    $result = $this->database->insert($sql, $params);
    Logger::log(['method' => 'PanelModel->updateMyaccount', 'result' => $result ]);

    return true;
  }
}