<?php
require_once '../app/Database.php';

class PanelDAO
{
  private $database;

  public function __construct()
  {
    $this->database = new Database();
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