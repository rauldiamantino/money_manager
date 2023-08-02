<?php
require_once '../app/Database.php';

class HomeDAO
{
  private $database;

  public function contentHome()
  {
    $this->database = new Database();
    // $sql = 'SELECT * FROM expenses';
    // $result = $this->database->query($sql);
    
    $result = [
      'titulo' => 'Página Inicial',
      'mensagem' => 'Money Manager',
    ];

    // return $result->fetch_all(MYSQLI_ASSOC);
    return $result;
  }
}