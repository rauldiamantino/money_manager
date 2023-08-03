<?php
require_once '../app/Database.php';

class FinancesDAO
{
  private $database;

  public function getFinancesCombinedDb()
  {
    $this->database = new Database();
    $sql = 'SELECT * FROM expenses
            UNION 
            SELECT * FROM incomes
            ORDER BY date';

    $result = $this->database->query($sql);

    return $result->fetch_all(MYSQLI_ASSOC);
  }
}