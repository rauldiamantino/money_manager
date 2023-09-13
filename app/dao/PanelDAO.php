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

    // Edita a receita se houver ID
    if ($income['transaction_id']) {
      $sql = 'UPDATE incomes
              SET date = :date,
                  type = :type,
                  amount = :amount,
                  status = :status,
                  account_id = :account_id,
                  category_id = :category_id,
                  description = :description
              WHERE id = :id;';

      $params['id'] = $income['transaction_id'];
    }    

    $this->database->switch_database($database_name);
    $result = $this->database->insert($sql, $params);

    if (empty($result)) {
      Logger::log('PanelDAO->add_income_db: Erro ao adicionar receita');
    }
    elseif (empty($result) and $income['transaction_id']) {
      Logger::log('PanelDAO->add_income_db: Erro ao editar receita');
    }

    return $result;
  }

  // Adiciona despesa no banco de dados
  public function add_expense_db($user_id, $expense)
  {
    $database_name = 'm_user_' . $user_id;
    $sql = 'INSERT INTO expenses (description, amount, type, category_id, account_id, date, status)
            VALUES (:description, :amount, :type, :category_id, :account_id, :date, :status);';

    $params = [
      'type' => $expense['type'],
      'date' => $expense['date'],
      'status' => $expense['status'],
      'amount' => $expense['amount'],
      'account_id' => $expense['account_id'],
      'category_id' => $expense['category_id'],
      'description' => $expense['description'],
    ];

    // Edita a despesa se houver ID
    if ($expense['transaction_id']) {
      $sql = 'UPDATE expenses
              SET date = :date,
                  type = :type,
                  status = :status,
                  amount = :amount,
                  account_id = :account_id,
                  category_id = :category_id,
                  description = :description
              WHERE id = :id;';

      $params['id'] = $expense['transaction_id'];
    }

    $this->database->switch_database($database_name);
    $result = $this->database->insert($sql, $params);

    if (empty($result)) {
      Logger::log('PanelDAO->add_expense_db: Erro ao adicionar despesa');
    }
    elseif (empty($result) and $expense['transaction_id']) {
      Logger::log('PanelDAO->add_expense_db: Erro ao editar despesa');
    }

    return $result;
  }

  // Remove transação do banco de dados
  public function delete_transaction_db($user_id, $transaction)
  {
    $database_name = 'm_user_' . $user_id;
    $table = 'incomes';

    if ($transaction['transaction_type'] == 'E') {
      $table = 'expenses';
    }

    $sql = 'DELETE FROM ' . $table . ' WHERE id = :id;';
    $params = ['id' => $transaction['transaction_id'] ];

    $this->database->switch_database($database_name);
    $result = $this->database->delete($sql, $params);

    if (empty($result)) {
      Logger::log('PanelDAO->delete_transaction_db: Erro ao apagar transação');
    }

    return $result;
  }

  // Remove conta do banco de dados
  public function delete_account_db($user_id, $account_id)
  {
    $database_name = 'm_user_' . $user_id;
    $sql = 'DELETE FROM accounts WHERE id = :id;';
    $params = ['id' => $account_id ];

    $this->database->switch_database($database_name);
    $result = $this->database->delete($sql, $params);

    if (empty($result)) {
      Logger::log('PanelDAO->delete_account_db: Erro ao apagar conta');
    }

    return $result;
  }

  // Remove categoria do banco de dados
  public function delete_category_db($user_id, $category_id)
  {
    $database_name = 'm_user_' . $user_id;
    $sql = 'DELETE FROM categories WHERE id = :id;';
    $params = ['id' => $category_id ];

    $this->database->switch_database($database_name);
    $result = $this->database->delete($sql, $params);

    if (empty($result)) {
      Logger::log('PanelDAO->delete_category_db: Erro ao apagar categoria');
    }

    return $result;
  }

  // Remove transação do banco de dados
  public function edit_transaction_status_db($user_id, $transaction)
  {
    $database_name = 'm_user_' . $user_id;

    $sql = 'UPDATE incomes 
            SET status = :status
            WHERE id = :id;';

    if ($transaction['transaction_type'] == 'E') {
      $sql = 'UPDATE expenses
              SET status = :status
              WHERE id = :id;';
    }

    $params = ['id' => $transaction['transaction_id'], 'status' => $transaction['transaction_status'] ];

    $this->database->switch_database($database_name);
    $result = $this->database->insert($sql, $params);

    if (empty($result)) {
      Logger::log('PanelDAO->edit_transaction_status_db: Erro ao alterar status da transação');
    }

    return $result;
  }

  // Busca transações no banco de dados
  public function get_transactions_db($user_id)
  {
    $database_name = 'm_user_' . $user_id;
    $sql = 'SELECT 
                id,
                date,
                type,
                status,
                amount,
                created_at,
                updated_at,
                description,
                account_name,
                category_name
            FROM (
                SELECT
                    expenses.id,
                    expenses.date,
                    expenses.type,
                    expenses.amount,
                    expenses.status,
                    expenses.created_at,
                    expenses.updated_at,
                    expenses.description,
                    accounts.name AS account_name,
                    categories.name AS category_name
                FROM expenses
                LEFT JOIN accounts ON expenses.account_id = accounts.id
                LEFT JOIN categories ON expenses.category_id = categories.id
                UNION ALL
                SELECT
                    incomes.id,
                    incomes.date,
                    incomes.type,
                    incomes.amount,
                    incomes.status,
                    incomes.created_at,
                    incomes.updated_at,
                    incomes.description,
                    accounts.name AS account_name,
                    categories.name AS category_name
                FROM incomes
                LEFT JOIN accounts ON incomes.account_id = accounts.id
                LEFT JOIN categories ON incomes.category_id = categories.id
            ) AS combined_data
            ORDER BY combined_data.date ASC, combined_data.created_at ASC;';

    $this->database->switch_database($database_name);
    $result = $this->database->select($sql, ['database_name' => $database_name ]);

    if (empty($result)) {
      Logger::log('PanelDAO->get_transactions_db: Transações não encontradas');
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

  // Verifica transações e retorna se a conta está sendo utilizada
  public function verify_account_in_use_db($user_id, $account_id)
  {
    $database_name = 'm_user_' . $user_id;
    $sql = 'SELECT account_id, description FROM incomes WHERE account_id = :account_id
            UNION
            SELECT account_id, description FROM expenses WHERE account_id = :account_id';

    $params = ['account_id' => $account_id ];

    $this->database->switch_database($database_name);
    $result = $this->database->select($sql, ['params' => $params, 'database_name' => $database_name ]);

    if ($result) {
      Logger::log(['method' => 'PanelDAO->verify_account_in_use_db', 'result' => $result, 'message' => 'Conta em uso não pode ser apagada'], 'alert');
    }

    return $result;
  }

  // Verifica transações e retorna se a categoria está sendo utilizada
  public function verify_category_in_use_db($user_id, $category_id)
  {
    $database_name = 'm_user_' . $user_id;
    $sql = 'SELECT category_id, description FROM incomes WHERE category_id = :category_id
            UNION
            SELECT category_id, description FROM expenses WHERE category_id = :category_id';

    $params = ['category_id' => $category_id ];

    $this->database->switch_database($database_name);
    $result = $this->database->select($sql, ['params' => $params, 'database_name' => $database_name ]);

    if ($result) {
      Logger::log(['method' => 'PanelDAO->verify_category_in_use_db', 'result' => $result, 'message' => 'Categoria em uso não pode ser apagada'], 'alert');
    }

    return $result;
  }

  // Adiciona conta no banco de dados
  public function add_account_db($user_id, $account)
  {
    $database_name = 'm_user_' . $user_id;
    $sql = 'INSERT INTO accounts (name) VALUES (:name);';
    $params = ['name' => $account['name'] ];

    // Edita a conta se houver ID
    if ($account['id']) {
      $sql = 'UPDATE accounts SET name = :name WHERE id = :id;';
      $params['id'] = $account['id'];
    }

    $this->database->switch_database($database_name);
    $result = $this->database->insert($sql, $params);

    if (empty($result)) {
      Logger::log('PanelDAO->add_account_db: Erro ao adicionar conta no banco de dados do usuário');
    }
    elseif (empty($result) and $account['id']) {
      Logger::log('PanelDAO->add_account_db: Erro ao editar conta');
    }

    return $result;
  }

  // Adiciona categoria no banco de dados
  public function add_category_db($user_id, $category)
  {
    $database_name = 'm_user_' . $user_id;
    $sql = 'INSERT INTO categories (name) VALUES (:name);';
    $params = ['name' => $category['name'] ];

    // Edita a categoria se houver ID
    if ($category['id']) {
      $sql = 'UPDATE categories SET name = :name WHERE id = :id;';
      $params['id'] = $category['id'];
    }

    $this->database->switch_database($database_name);
    $result = $this->database->insert($sql, $params);

    if (empty($result)) {
      Logger::log('PanelDAO->add_category_db: Erro ao adicionar categoria no banco de dados do usuário');
    }
    elseif (empty($result) and $category['id']) {
      Logger::log('PanelDAO->add_category_db: Erro ao editar categoria');
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