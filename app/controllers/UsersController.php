<?php
require_once '../app/models/UsersModel.php';

class UsersController
{
  public $usersModel;

  // Verifica se está logado
  public function checkSession()
  {
    // Sessão do navegador
    $userId = $_SESSION['user']['user_id'] ?? '';
    $sessionId = $_SESSION['user']['session_id'] ?? '';

    // Sessão armazenada no banco de dados
    $this->usersModel = new UsersModel();
    $getUser = $this->usersModel->getUser('', $userId);
    $sessionDb = $getUser ? $getUser[0]['session_id'] : '';

    // Se estiver logado redireciona para o painel
    if ($userId and $sessionDb == $sessionId) {
      header('Location: panel/' . $userId);
      exit();
    }
  }
}