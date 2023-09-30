<?php
require_once '../app/models/PanelModel.php';

class TransactionsModel extends PanelModel
{

  // Obtém receitas e despesas do usuário
  public function getTransactions($userId, $month = '')
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
    Logger::log(['method' => 'TransactionsModel->getTransactions', 'result' => $result]);

    return $result;
  }

  // Obtém receitas usuário
  public function getIncomes($userId)
  {
    $databaseName = 'm_user_' . $userId;
    $sql = 'SELECT id, date, type, status, amount, created_at, updated_at, description, account_name, category_name
            FROM
            (
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
    Logger::log(['method' => 'TransactionsModel->getIncomes', 'result' => $result]);

    return $result;
  }

  // Obtém despesas do usuário
  public function getExpenses($userId)
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
            )
            AS combined_data
            ORDER BY combined_data.date ASC, combined_data.created_at ASC;';

    $this->database->switchDatabase($databaseName);
    $result = $this->database->select($sql, ['database_name' => $databaseName]);
    Logger::log(['method' => 'TransactionsModel->getExpenses', 'result' => $result]);

    return $result;
  }

  // Adiciona receita
  public function createIncome($userId, $income)
  {
    $databaseName = 'm_user_' . $userId;
    $sql = 'INSERT INTO incomes (description, amount, type, category_id, account_id, date, status)
            VALUES (:description, :amount, :type, :category_id, :account_id, :date, :status);';

    $params = [
      'type' => $income['type'],
      'date' => $income['date'],
      'status' => $income['status'],
      'amount' => $income['amount'],
      'account_id' => $income['account_id'],
      'description' => $income['description'],
      'category_id' => $income['category_id'],
    ];

    $this->database->switchDatabase($databaseName);
    $result = $this->database->insert($sql, $params);

    Logger::log(['method' => 'TransactionsModel->createIncome', 'result' => $result]);

    return $result;
  }

  // Editar receita já existente
  public function editIncome($userId, $income)
  {
    $databaseName = 'm_user_' . $userId;
    $sql = 'UPDATE incomes
              SET date = :date,
                  type = :type,
                  amount = :amount,
                  status = :status,
                  account_id = :account_id,
                  category_id = :category_id,
                  description = :description
              WHERE id = :id;';

    $params = [
      'id' => $income['transaction_id'],
      'type' => $income['type'],
      'date' => $income['date'],
      'status' => $income['status'],
      'amount' => $income['amount'],
      'account_id' => $income['account_id'],
      'description' => $income['description'],
      'category_id' => $income['category_id'],
    ];

    $this->database->switchDatabase($databaseName);
    $result = $this->database->insert($sql, $params);

    Logger::log(['method' => 'TransactionsModel->editIncome', 'result' => $result]);

    return $result;
  }

  // Adiciona despesa
  public function createExpense($userId, $expense)
  {
    $databaseName = 'm_user_' . $userId;
    $sql = 'INSERT INTO expenses (description, amount, type, category_id, account_id, date, status)
            VALUES (:description, :amount, :type, :category_id, :account_id, :date, :status);';

    $params = [
      'type' => $expense['type'],
      'date' => $expense['date'],
      'status' => $expense['status'],
      'amount' => $expense['amount'],
      'account_id' => $expense['account_id'],
      'description' => $expense['description'],
      'category_id' => $expense['category_id'],
    ];

    $this->database->switchDatabase($databaseName);
    $result = $this->database->insert($sql, $params);

    Logger::log(['method' => 'TransactionsModel->createExpense', 'result' => $result]);

    return $result;
  }

  // Editar receita já existente
  public function editExpense($userId, $expense)
  {
    $databaseName = 'm_user_' . $userId;
    $sql = 'UPDATE expenses
              SET date = :date,
                  type = :type,
                  amount = :amount,
                  status = :status,
                  account_id = :account_id,
                  category_id = :category_id,
                  description = :description
              WHERE id = :id;';

    $params = [
      'type' => $expense['type'],
      'date' => $expense['date'],
      'status' => $expense['status'],
      'amount' => $expense['amount'],
      'id' => $expense['transaction_id'],
      'account_id' => $expense['account_id'],
      'description' => $expense['description'],
      'category_id' => $expense['category_id'],
    ];

    $this->database->switchDatabase($databaseName);
    $result = $this->database->insert($sql, $params);

    Logger::log(['method' => 'TransactionsModel->editExpense', 'result' => $result]);

    return $result;
  }

  // Altera status da transação
  public function changeStatus($userId, $transaction)
  {
    $database_name = 'm_user_' . $userId;
    $sql = 'UPDATE ' . $transaction['table'] . ' SET status = :status WHERE id = :id;';
    $params = ['id' => $transaction['id'], 'status' => $transaction['status']];

    $this->database->switchDatabase($database_name);
    $result = $this->database->insert($sql, $params);

    Logger::log(['method' => 'TransactionsModel->changeStatus', 'result' => $result]);

    return $result;
  }

  // Apaga transação
  public function deleteTransaction($userId, $transaction)
  {
    $database_name = 'm_user_' . $userId;
    $sql = 'DELETE FROM ' . $transaction['table'] . ' WHERE id = :id;';
    $params = ['id' => $transaction['id']];

    $this->database->switchDatabase($database_name);
    $result = $this->database->delete($sql, $params);

    Logger::log(['method' => 'TransactionsModel->deleteTransaction', 'result' => $result], 'error');

    return true;
  }
}