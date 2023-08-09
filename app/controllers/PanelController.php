<?php
require_once '../app/models/PanelModel.php';
require_once '../app/helpers/ViewRenderes.php';

class PanelController
{
  private $panelModel;
  private $base_uri;
  private $user_id;
  private $user_first_name;
  private $user_last_name;
  private $user_email;
  private $active_tab;
  private $action_route;

  public function __construct()
  {
    $this->panelModel = new PanelModel();
    // $this->base_uri = str_replace('/public/', '', dirname($_SERVER['SCRIPT_NAME']));
    $this->base_uri = dirname($_SERVER['SCRIPT_NAME']);
    $this->user_id = $_SESSION['user']['user_id'] ?? '';
    $this->user_first_name = $_SESSION['user']['user_first_name'] ?? '';
    $this->user_last_name = $_SESSION['user']['user_last_name'] ?? '';
    $this->user_email = $_SESSION['user']['user_email'] ?? '';
  }

  public function display()
  {
    $this->check_session();
    $this->check_logout();
    
    try {
      $panel_content = $this->panelModel->getContentPanel();
      $this->active_tab = 'overview';
      $this->action_route = '../panel/display';

      ViewRenderer::render('panel/templates/nav', [
        'user_id' => $this->user_id,
        'active_tab' => $this->active_tab,
        'action_route' => $this->action_route,
        'user_first_name' => $this->user_first_name,
        'user_last_name' => $this->user_last_name,
        'base_uri' => $this->base_uri,
        ]);

      ViewRenderer::render('panel/panel');
    }
    catch (Exception $e) {
      $error_message = 'Erro ao buscar conteúdo: ' . $e->getMessage();
    }
  }

  public function transactions($user_id)
  {
    $this->check_session();
    $this->check_logout();

    try {
      $this->action_route = '../transactions/' . $user_id;
      $transactions = $this->panelModel->getTransactions($user_id);
      $this->active_tab = 'transactions';

      ViewRenderer::render('panel/templates/nav', [
        'user_id' => $this->user_id,
        'active_tab' => $this->active_tab,
        'action_route' => $this->action_route,
        'user_first_name' => $this->user_first_name,
        'user_last_name' => $this->user_last_name,
        'base_uri' => $this->base_uri,
        ]);

      ViewRenderer::render('panel/transactions');
    } 
    catch (Exception $e) {
      $error_message = 'Erro ao buscar transações: ' . $e->getMessage();
    }
  }

  private function check_session()
  {
    if (empty($_SESSION['user'])) {
      header('Location: ' . $this->base_uri . '/users/login');
      exit();
    }

  }

  private function check_logout()
  {
    if (isset($_POST['logout']) and $_POST['logout']) {
      unset($_SESSION['user']);
      session_destroy();
      header('Location: ' . $this->base_uri);
      exit();
    }
  }
}
