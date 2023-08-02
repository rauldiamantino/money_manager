<?php
require_once '../app/Database.php';

class IncomesDAO
{
  private $database;

  public function getAllIncomes()
  {
    $this->database = new Database();
    $sql = 'SELECT * FROM incomes';
    $result = $this->database->query($sql);

    return $result->fetch_all(MYSQLI_ASSOC);
  }
}