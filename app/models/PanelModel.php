<?php
require_once '../app/Database.php';

class PanelModel
{
  public $database;

  public function __construct()
  {
    $this->database = new Database();
  }

  // Obtém conteúdo a ser exibido no painel principal
  public function getContentPanel()
  {
    $result = [];
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

    $this->database->switch_database($databaseName);
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

    $this->database->switch_database($databaseName);
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

    $this->database->switch_database($databaseName);
    $result = $this->database->select($sql, ['params' => $params, 'database_name' => $databaseName]);
    Logger::log(['method' => 'PanelModel->getCategories', 'result' => $result]);

    return $result;
  }

  //---------------------- Nova Model ----------------------//

  // Verifica se o usuário existe na tabela de usuários
  public function checkUserExists($userId)
  {
    $sql = 'SELECT * FROM users WHERE id = :id';
    $params = ['id' => $userId];

    $this->database->switch_database(DB_NAME);
    $result = $this->database->select($sql, ['params' => $params, 'database_name' => DB_NAME]);
    Logger::log(['method' => 'PanelModel->checkUserExists', 'result' => $result]);

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

    $this->database->switch_database($databaseName);
    $result = $this->database->insert($sql, $params);

    Logger::log(['method' => 'PanelModel->createIncome', 'result' => $result]);

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

    $this->database->switch_database($databaseName);
    $result = $this->database->insert($sql, $params);

    Logger::log(['method' => 'PanelModel->editIncome', 'result' => $result]);

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

    $this->database->switch_database($databaseName);
    $result = $this->database->insert($sql, $params);

    Logger::log(['method' => 'PanelModel->createExpense', 'result' => $result]);

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

    $this->database->switch_database($databaseName);
    $result = $this->database->insert($sql, $params);

    Logger::log(['method' => 'PanelModel->editExpense', 'result' => $result]);

    return $result;
  }

  // Altera status da transação
  public function changeStatus($userId, $transaction)
  {
    $database_name = 'm_user_' . $userId;
    $sql = 'UPDATE ' . $transaction['table'] . ' SET status = :status WHERE id = :id;';
    $params = ['id' => $transaction['id'], 'status' => $transaction['status']];

    $this->database->switch_database($database_name);
    $result = $this->database->insert($sql, $params);

    Logger::log(['method' => 'PanelModel->changeStatus', 'result' => $result]);

    return $result;
  }

  // Apaga transação
  public function deleteTransaction($userId, $transaction)
  {
    $database_name = 'm_user_' . $userId;
    $sql = 'DELETE FROM ' . $transaction['table'] . ' WHERE id = :id;';
    $params = ['id' => $transaction['id']];

    $this->database->switch_database($database_name);
    $result = $this->database->delete($sql, $params);

    Logger::log(['method' => 'PanelModel->deleteTransaction', 'result' => $result], 'error');

    return true;
  }

  // Cria uma conta para o usuário
  public function createAccount($userId, $accountName)
  {
    $databaseName = 'm_user_' . $userId;
    $sql = 'INSERT INTO accounts (name) VALUES (:name);';
    $params = ['name' => $accountName];

    $this->database->switch_database(($databaseName));
    $result = $this->database->insert($sql, $params);

    Logger::log(['method' => 'PanelModel->createAccount', 'result' => $result]);

    return $result;
  }

  // Edita uma conta já existente
  public function editAccount($userId, $account)
  {
    $databaseName = 'm_user_' . $userId;
    $sql = 'UPDATE accounts SET name = :name WHERE id = :id;';
    $params = ['id' => $account['id'], 'name' => $account['name']];

    $this->database->switch_database(($databaseName));
    $result = $this->database->insert($sql, $params);

    Logger::log(['method' => 'PanelModel->editAccount', 'result' => $result]);

    return $result;
  }

  // Apaga conta
  public function deleteAccount($userId, $accountId)
  {
    $databaseName = 'm_user_' . $userId;
    $sql = 'DELETE FROM accounts WHERE id = :id;';
    $params = ['id' => $accountId];

    $this->database->switch_database($databaseName);
    $result = $this->database->delete($sql, $params);

    Logger::log(['method' => 'PanelModel->deleteAccount', 'result' => $result]);

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

    $this->database->switch_database($databaseName);
    $result = $this->database->select($sql, ['params' => $params, 'database_name' => $databaseName]);

    Logger::log(['method' => 'PanelModel->accountInUse', 'result' => $result]);

    return $result;
  }

  // Verifica se a conta já existe para o usuário
  public function accountExists($user_id, $account)
  {
    $databaseName = 'm_user_' . $user_id;
    $paramWhere = array_key_first($account);

    $sql = 'SELECT * FROM accounts WHERE ' . $paramWhere . ' = :' . $paramWhere;
    $params = [$paramWhere => reset($account)];

    $this->database->switch_database($databaseName);
    $result = $this->database->select($sql, ['params' => $params, 'database_name' => $databaseName]);

    Logger::log(['method' => 'PanelModel->accountExists', 'result' => $result]);

    return $result;
  }

  // Cria uma categoria para o usuário
  public function createCategory($userId, $categoryName)
  {
    $databaseName = 'm_user_' . $userId;
    $sql = 'INSERT INTO categories (name) VALUES (:name);';
    $params = ['name' => $categoryName];

    $this->database->switch_database(($databaseName));
    $result = $this->database->insert($sql, $params);

    Logger::log(['method' => 'PanelModel->createCategory', 'result' => $result]);

    return $result;
  }

  // Edita uma categoria já existente
  public function editCategory($userId, $category)
  {
    $databaseName = 'm_user_' . $userId;
    $sql = 'UPDATE categories SET name = :name WHERE id = :id;';
    $params = ['id' => $category['id'], 'name' => $category['name']];

    $this->database->switch_database(($databaseName));
    $result = $this->database->insert($sql, $params);

    Logger::log(['method' => 'PanelModel->editCategory', 'result' => $result]);

    return $result;
  }

  // Apaga categoria
  public function deleteCategory($userId, $categoryId)
  {
    $databaseName = 'm_user_' . $userId;
    $sql = 'DELETE FROM categories WHERE id = :id;';
    $params = ['id' => $categoryId];

    $this->database->switch_database($databaseName);
    $result = $this->database->delete($sql, $params);

    Logger::log(['method' => 'PanelModel->deleteCategory', 'result' => $result]);

    return true;
  }

  // Verifica se a categoria está em uso em alguma transação
  public function categoryInUse($userId, $categoryId)
  {
    $databaseName = 'm_user_' . $userId;
    $sql = 'SELECT category_id, description FROM incomes WHERE category_id = :category_id
            UNION
            SELECT category_id, description FROM expenses WHERE category_id = :category_id';

    $params = ['category_id' => $categoryId];

    $this->database->switch_database($databaseName);
    $result = $this->database->select($sql, ['params' => $params, 'database_name' => $databaseName]);

    Logger::log(['method' => 'PanelModel->categoryInUse', 'result' => $result]);

    return $result;
  }

  // Verifica se a categoria já existe para o usuário
  public function categoryExists($user_id, $category)
  {
    $databaseName = 'm_user_' . $user_id;
    $paramWhere = array_key_first($category);

    $sql = 'SELECT * FROM categories WHERE ' . $paramWhere . ' = :' . $paramWhere;
    $params = [$paramWhere => reset($category)];

    $this->database->switch_database($databaseName);
    $result = $this->database->select($sql, ['params' => $params, 'database_name' => $databaseName]);

    Logger::log(['method' => 'PanelModel->categoryExists', 'result' => $result]);

    return $result;
  }
}
