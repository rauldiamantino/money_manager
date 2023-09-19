<?php
require_once '../app/models/PanelModel.php';

class AccountsModel extends PanelModel
{

  // Cria uma conta para o usuário
  public function createAccount($userId, $accountName)
  {
    $databaseName = 'm_user_' . $userId;
    $sql = 'INSERT INTO accounts (name) VALUES (:name);';
    $params = ['name' => $accountName];

    $this->database->switchDatabase(($databaseName));
    $result = $this->database->insert($sql, $params);

    Logger::log(['method' => 'AccountsModel->createAccount', 'result' => $result]);

    return $result;
  }

  // Edita uma conta já existente
  public function editAccount($userId, $account)
  {
    $databaseName = 'm_user_' . $userId;
    $sql = 'UPDATE accounts SET name = :name WHERE id = :id;';
    $params = ['id' => $account['id'], 'name' => $account['name']];

    $this->database->switchDatabase(($databaseName));
    $result = $this->database->insert($sql, $params);

    Logger::log(['method' => 'AccountsModel->editAccount', 'result' => $result]);

    return $result;
  }

  // Apaga conta
  public function deleteAccount($userId, $accountId)
  {
    $databaseName = 'm_user_' . $userId;
    $sql = 'DELETE FROM accounts WHERE id = :id;';
    $params = ['id' => $accountId];

    $this->database->switchDatabase($databaseName);
    $result = $this->database->delete($sql, $params);

    Logger::log(['method' => 'AccountsModel->deleteAccount', 'result' => $result]);

    return true;
  }

  // Verifica se a conta está em uso em alguma transação
  public function accountInUse($userId, $accountId)
  {
    $databaseName = 'm_user_' . $userId;
    $sql = 'SELECT account_id, description FROM incomes WHERE account_id = :account_id
            UNION
            SELECT account_id, description FROM expenses WHERE account_id = :account_id';

    $params = ['account_id' => $accountId];

    $this->database->switchDatabase($databaseName);
    $result = $this->database->select($sql, ['params' => $params, 'database_name' => $databaseName]);

    Logger::log(['method' => 'AccountsModel->accountInUse', 'result' => $result]);

    return $result;
  }

  // Verifica se a conta já existe para o usuário
  public function accountExists($user_id, $account)
  {
    $databaseName = 'm_user_' . $user_id;
    $paramWhere = array_key_first($account);

    $sql = 'SELECT * FROM accounts WHERE ' . $paramWhere . ' = :' . $paramWhere;
    $params = [$paramWhere => reset($account)];

    $this->database->switchDatabase($databaseName);
    $result = $this->database->select($sql, ['params' => $params, 'database_name' => $databaseName]);

    Logger::log(['method' => 'AccountsModel->accountExists', 'result' => $result]);

    return $result;
  }
}
