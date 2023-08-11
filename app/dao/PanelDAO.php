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
}
