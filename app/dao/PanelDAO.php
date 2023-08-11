<?php
require_once '../app/Database.php';

class PanelDAO
{
  private $database;

  public function __construct()
  {
    $this->database = new Database();
  }

  public function add_income_db($user_id, $income)
  {
    $database_name = 'm_user_' . $user_id;
    $sql = 'INSERT INTO incomes (description, amount, category_id, account_id, date)
            VALUES (:description, :amount, :category_id, :account_id, :date);';

    $params = [
      'description' => $income['description'],
      'amount' => $income['amount'],
      'category_id' => $income['category_id'],
      'account_id' => $income['account_id'],
      'date' => $income['date'],
    ];

    $this->database->switch_database($database_name);
    $result = $this->database->insert($sql, $params);

    return $result;
  }

  public function get_transactions_db($user_id)
  {
    $database_name = 'm_user_' . $user_id;
    $sql = 'SELECT 
                expenses.id, 
                expenses.description, 
                expenses.amount, 
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
                categories.name AS category_name, 
                accounts.name AS account_name, 
                incomes.date, 
                incomes.created_at, 
                incomes.updated_at
            FROM incomes
            LEFT JOIN categories ON incomes.category_id = categories.id
            LEFT JOIN accounts ON incomes.account_id = accounts.id
            ORDER BY date;';

    $result = $this->database->select($sql, ['database_name' => $database_name ]);

    return $result;
  }

  public function get_accounts_db($user_id, $account = '')
  {
    $database_name = 'm_user_' . $user_id;
    $account_name = $account;
    $sql = 'SELECT * FROM accounts';

    if ($account_name) {
      $sql .= ' WHERE name = :name';
    }
    $params = ['name' => $account];

    $result = $this->database->select($sql, ['params' => $params , 'database_name' => $database_name ]);
    return $result;
  }

  public function get_categories_db($user_id, $category = '')
  {
    $database_name = 'm_user_' . $user_id;
    $category_name = $category;
    $sql = 'SELECT * FROM categories';

    if ($category_name) {
      $sql .= ' WHERE name = :name';
    }
    $params = ['name' => $category];

    $result = $this->database->select($sql, ['params' => $params , 'database_name' => $database_name ]);
    return $result;
  }

  public function add_account_db($user_id, $account)
  {
    $database_name = 'm_user_' . $user_id;
    $sql = 'INSERT INTO accounts (name) VALUES (:name);';
    $params = ['name' => $account ];

    $this->database->switch_database($database_name);
    $result = $this->database->insert($sql, $params);

    return $result;
  }

  public function add_category_db($user_id, $category)
  {
    $database_name = 'm_user_' . $user_id;
    $sql = 'INSERT INTO categories (name) VALUES (:name);';
    $params = ['name' => $category ];

    $this->database->switch_database($database_name);
    $result = $this->database->insert($sql, $params);

    return $result;
  }
}
