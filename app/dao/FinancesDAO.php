<?php
require_once '../app/Database.php';

class FinancesDAO
{
  private $database;

  public function get_finances_db($data)
  {
    $user = $data;
    $this->database = new Database($user);
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
            ORDER BY date;
            ';

    $result = $this->database->query($sql);

    return $result->fetch_all(MYSQLI_ASSOC);
  }
}