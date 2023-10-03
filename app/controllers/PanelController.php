<?php
require_once '../app/models/PanelModel.php';

class PanelController
{
  public $panelModel;

  public function __construct()
  {
    $this->panelModel = new PanelModel();
  }

  // Exibe visão geral do painel
  public function display($userId)
  {
    // Valida sessão e login
    $this->checkLogout($userId);
    $this->checkSession($userId);

    // Renderiza
    $renderView = [
      'panel/templates/nav' => [
        'user_id' => $userId,
        'active_tab' => 'overview',
        'action_route' => 'panel/' . $userId,
        'user_first_name' => $_SESSION['user']['user_first_name'],
        'user_last_name' => $_SESSION['user']['user_last_name'],
      ],
      'panel/panel' => [],
    ];

    return $renderView;
  }

  // Verifica se possui sessão ativa
  public function checkSession($userId)
  {
    $getUser = $this->panelModel->getUser('', $userId);
    $sessionIdDb = $getUser[0]['session_id'] ?? '';

    if (empty($sessionIdDb) or $sessionIdDb != $_SESSION['user']['session_id']) {
      header('Location: ' . BASE . '/login');
      Logger::log(['method' => 'PanelController->checkSession', 'result' => 'Usuario não possui sessão ativa']);
    }
  }

  // Verifica se o usuário fez logout
  public function checkLogout($userId)
  {
    $logout = $_POST['logout'] ?? '';

    if ($logout) {
      // Encerra sessão
      unset($_SESSION['user']);
      session_destroy();

      $this->panelModel->saveSession($userId, null);
      Logger::log(['method' => 'PanelController->checkLogout', 'result' => 'Usuario se desconectou']);

      header('Location: ' . BASE . '/');
    }
  }
}