<?php
require_once '../app/models/PanelModel.php';
require_once '../app/models/UsersModel.php';

class PanelController
{
  public $panelModel;
  public $usersModel;
  public $userId;
  public $userFirstName;
  public $userLastName;
  public $userEmail;
  public $activeTab;
  public $actionRoute;

  public function __construct()
  {
    $this->panelModel = new PanelModel();
    $this->usersModel = new UsersModel();

    // Recupera dados de sessão do usuário
    $this->userId = $_SESSION['user']['user_id'] ?? '';
    $this->userFirstName = $_SESSION['user']['user_first_name'] ?? '';
    $this->userLastName = $_SESSION['user']['user_last_name'] ?? '';
    $this->userEmail = $_SESSION['user']['user_email'] ?? '';
  }

  // Exibe visão geral do painel
  public function display($userId)
  {

    // Valida se o usuário está logado
    if ($this->checkSession($userId) or $this->checkLogout()) {
      Logger::log(['method' => 'PanelController->display', 'result' => 'Usuario Desconectado'], 'alert');
    }

    $this->activeTab = 'overview';
    $this->actionRoute = '/panel/' . $userId;

    // View e conteúdo para o menu de navegação
    $navViewName = 'panel/templates/nav';
    $navViewContent = [
      'user_id' => $this->userId,
      'active_tab' => $this->activeTab,
      'action_route' => $this->actionRoute,
      'user_first_name' => $this->userFirstName,
      'user_last_name' => $this->userLastName,
    ];

    // View e conteúdo para o painel
    $panelViewName = 'panel/panel';
    $panelViewContent = [];

    return [ $navViewName => $navViewContent, $panelViewName => $panelViewContent ];
  }

  // Verifica se o usuário possui sessão ativa
  public function checkSession($userId)
  {
    $sessionIdDb = '';
    $getUser = $this->panelModel->getUser('', $userId);

    if ($getUser) {
      $sessionIdDb = $getUser[0]['session_id'];
    }

    if ($sessionIdDb != $_SESSION['user']['session_id']) {
      header('Location: ' . BASE . 'login');
      return true;
    }

    if (empty($_SESSION['user'])) {
      header('Location: ' . BASE . 'login');
      return true;
    }

    return false;
  }

  // Verifica se o usuário existe e está logado
  public function checkLogout()
  {
    $userExists = $this->panelModel->checkUserExists($this->userId);

    if ($userExists and empty($_POST['logout'])) {
      return false;
    }

    // Encerra sessão e redireciona para a home
    unset($_SESSION['user']);
    $this->panelModel->saveSession($this->userId, null);
    session_destroy();

    header('Location: ' . BASE);
    return true;
  }
}