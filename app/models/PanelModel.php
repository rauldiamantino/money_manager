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

  // Obtém receitas e despesas do usuário
  public function getTransactions($userId)
  {
    $databaseName = 'm_user_' . $userId;
    $sql = 'SELECT id, date, type, status, amount, created_at, updated_at, description, account_name, category_name
            FROM
            (
              SELECT expenses.id, expenses.date, expenses.type, expenses.amount, expenses.status, expenses.created_at,
                      expenses.updated_at, expenses.description, accounts.name AS account_name, categories.name AS category_name
              FROM expenses
              LEFT JOIN accounts ON expenses.account_id = accounts.id
              LEFT JOIN categories ON expenses.category_id = categories.id
              UNION ALL
              SELECT incomes.id, incomes.date, incomes.type, incomes.amount, incomes.status, incomes.created_at,
                      incomes.updated_at, incomes.description, accounts.name AS account_name, categories.name AS category_name
              FROM incomes
              LEFT JOIN accounts ON incomes.account_id = accounts.id
              LEFT JOIN categories ON incomes.category_id = categories.id
            )
            AS combined_data
            ORDER BY combined_data.date ASC, combined_data.created_at ASC;';

    $this->database->switchDatabase($databaseName);
    $result = $this->database->select($sql, ['database_name' => $databaseName]);
    Logger::log(['method' => 'PanelModel->getTransactions', 'result' => $result]);

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
