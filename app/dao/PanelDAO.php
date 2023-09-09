<?php
require_once '../app/Database.php';

class PanelDAO
{
  private $database;

  public function __construct()
  {
    $this->database = new Database();
  }

  // Adiciona receita no banco de dados
  public function add_income_db($user_id, $income)
  {
    $database_name = 'm_user_' . $user_id;
    $sql = 'INSERT INTO incomes (description, amount, type, category_id, account_id, date)
            VALUES (:description, :amount, :type, :category_id, :account_id, :date);';

    $params = [
      'description' => $income['description'],
      'amount' => $income['amount'],
      'type' => $income['type'],
      'category_id' => $income['category_id'],
      'account_id' => $income['account_id'],
      'date' => $income['date'],
    ];

    $this->database->switch_database($database_name);
    $result = $this->database->insert($sql, $params);

    if (empty($result)) {
      Logger::log('PanelDAO->add_income_db: Erro ao adicionar receita');
    }

    return $result;
  }

  // Adiciona despesa no banco de dados
  public function add_expense_db($user_id, $expense)
  {
    $database_name = 'm_user_' . $user_id;
    $sql = 'INSERT INTO expenses (description, amount, type, category_id, account_id, date)
            VALUES (:description, :amount, :type, :category_id, :account_id, :date);';

    $params = [
      'description' => $expense['description'],
      'amount' => $expense['amount'],
      'type' => $expense['type'],
      'category_id' => $expense['category_id'],
      'account_id' => $expense['account_id'],
      'date' => $expense['date'],
    ];

    $this->database->switch_database($database_name);
    $result = $this->database->insert($sql, $params);

    if (empty($result)) {
      Logger::log('PanelDAO->add_expenses_db: Erro ao adicionar despesa');
    }

    return $result;
  }

  // Busca transações no banco de dados
  public function get_transactions_db($user_id)
  {
    $database_name = 'm_user_' . $user_id;
    $sql = 'SELECT 
                expenses.id, 
                expenses.description, 
                expenses.amount, 
                expenses.type, 
                categories.name AS category_name, 
                accounts.name AS account_name, 
                expenses.date, 
                expenses.created_at, 
                expenses.updated_at
            FROM expenses
            LEFT JOIN categories ON expenses.category_id = categories.id
            LEFT JOIN accounts ON expenses.account_id = accounts.id
            UNION
            SELECT 
                incomes.id, 
                incomes.description, 
                incomes.amount, 
                incomes.type, 
                categories.name AS category_name, 
                accounts.name AS account_name, 
                incomes.date, 
                incomes.created_at, 
                incomes.updated_at
            FROM incomes
            LEFT JOIN categories ON incomes.category_id = categories.id
            LEFT JOIN accounts ON incomes.account_id = accounts.id
            ORDER BY date;';

    $this->database->switch_database($database_name);
    $result = $this->database->select($sql, ['database_name' => $database_name ]);

    if (empty($result)) {
      Logger::log('PanelDAO->get_transactions_db: Erro ao buscar transações');
    }

    return $result;
  }

  // Busca contas no banco de dados
  public function get_accounts_db($user_id, $account = '')
  {
    $database_name = 'm_user_' . $user_id;
    $account_name = $account;
    $sql = 'SELECT * FROM accounts';
    $params = '';

    if ($account_name) {
      $sql .= ' WHERE name = :name';
      $params = ['name' => $account_name ];
    }

    $this->database->switch_database($database_name);
    $result = $this->database->select($sql, ['params' => $params, 'database_name' => $database_name ]);

    if (empty($result)) {
      Logger::log('PanelDAO->get_accounts_db: Conta inexistente');
    }

    return $result;
  }

  // Busca categorias no banco de dados
  public function get_categories_db($user_id, $category = '')
  {
    $database_name = 'm_user_' . $user_id;
    $category_name = $category;
    $sql = 'SELECT * FROM categories';
    $params = '';

    if ($category_name) {
      $sql .= ' WHERE name = :name';
      $params = ['name' => $category ];
    }

    $this->database->switch_database($database_name);
    $result = $this->database->select($sql, ['params' => $params, 'database_name' => $database_name ]);

    if (empty($result)) {
      Logger::log('PanelDAO->get_categories_db: Categoria inexistente');
    }

    return $result;
  }

  // Adiciona conta no banco de dados
  public function add_account_db($user_id, $account)
  {
    $database_name = 'm_user_' . $user_id;
    $sql = 'INSERT INTO accounts (name) VALUES (:name);';
    $params = ['name' => $account ];

    $this->database->switch_database($database_name);
    $result = $this->database->insert($sql, $params);

    if (empty($result)) {
      Logger::log('PanelDAO->add_account_db: Erro ao adicionar conta no banco de dados do usuário');
    }

    return $result;
  }

  // Adiciona categoria no banco de dados
  public function add_category_db($user_id, $category)
  {
    $database_name = 'm_user_' . $user_id;
    $sql = 'INSERT INTO categories (name) VALUES (:name);';
    $params = ['name' => $category ];

    $this->database->switch_database($database_name);
    $result = $this->database->insert($sql, $params);

    if (empty($result)) {
      Logger::log('PanelDAO->add_category_db: Erro ao adicionar categoria no banco de dados do usuário');
    }

    return $result;
  }

  public function check_user_db($user_id)
  {
    $sql = 'SELECT * FROM users WHERE id = :id';
    $params = ['id' => $user_id ];

    $this->database->switch_database(DB_NAME);
    $result = $this->database->select($sql, ['params' => $params, 'database_name' => DB_NAME]);

    if (empty($result)) {
      Logger::log('PanelDAO->check_user_db: Erro ao verificar usuário');
    }

    return $result;
  }
}