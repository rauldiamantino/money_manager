<?php
require_once '../app/models/Model.php';

class PanelModel extends Model
{

  // Obtém conteúdo a ser exibido no painel principal
  public function getContentPanel()
  {
    $result = [];
    return $result;
  }

  // Verifica se o usuário existe na tabela de usuários
  public function checkUserExists($userId)
  {
    $sql = 'SELECT * FROM users WHERE id = :id';
    $params = ['id' => $userId];

    $this->database->switchDatabase(DB_NAME);
    $result = $this->database->select($sql, ['params' => $params, 'database_name' => DB_NAME]) ? true : false;

    Logger::log(['method' => 'PanelModel->checkUserExists', 'result' => $result]);

    return $result;
  }

  // Obtém todas as contas cadastradas do usuário
  public function getAccounts($userId, $account = '')
  {
    $sql = 'SELECT * FROM accounts';
    $databaseName = 'm_user_' . $userId;

    $params = '';
    $accountName = $account;

    if ($accountName) {
      $sql .= ' WHERE name = :name';
      $params = ['name' => $accountName];
    }

    $this->database->switchDatabase($databaseName);
    $result = $this->database->select($sql, ['params' => $params, 'database_name' => $databaseName]);
    Logger::log(['method' => 'PanelModel->getAccounts', 'result' => $result]);

    return $result;
  }

  // Obtém todas as categorias cadastradas do usuário
  public function getCategories($userId, $category = '')
  {
    $sql = 'SELECT * FROM categories';
    $databaseName = 'm_user_' . $userId;

    $params = '';
    $categoryName = $category;

    if ($categoryName) {
      $sql .= ' WHERE name = :name';
      $params = ['name' => $category];
    }

    $this->database->switchDatabase($databaseName);
    $result = $this->database->select($sql, ['params' => $params, 'database_name' => $databaseName]);
    Logger::log(['method' => 'PanelModel->getCategories', 'result' => $result]);

    return $result;
  }
}
