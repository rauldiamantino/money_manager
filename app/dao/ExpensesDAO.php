<?php
require_once '../app/Database.php';

class ExpensesDAO
{
  private $database;

  public function getAllExpenses()
  {
    $this->database = new Database();
    $sql = 'SELECT * FROM expenses';
    $result = $this->database->query($sql);

    return $result->fetch_all(MYSQLI_ASSOC);
  }
}