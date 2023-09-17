<?php
require_once '../app/models/UsersModel.php';

class UsersController
{
  protected $usersModel;

  public function __construct()
  {
    $this->usersModel = new UsersModel();
  }

  // Cadastro de usuário
  public function registration()
  {

    // Valida se o usuário está logado
    if ($this->checkSession()) {
      Logger::log(['method' => 'UsersController->registration', 'result' => 'Usuario possui sessão ativa']);
    }

    // View e conteúdo da página
    $message = [];
    $user = [];
    $renderView = ['user_register' => []];
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

      // Recupera formulário
      $user['email'] = $_POST['user_email'] ?? '';
      $user['firstName'] = $_POST['user_first_name'] ?? '';
      $user['lastName'] = $_POST['user_last_name'] ?? '';
      $user['password'] = trim($_POST['user_password']) ?? '';
      $user['confirmPassword'] = trim($_POST['user_confirm_password']) ?? '';

      // Não aceita campos vazios
      if (in_array('', $user, true)) {
        $message = ['error_register' => 'Todos os campos precisam ser preenchidos'];
        $renderView['user_register'] = ['message' => $message ];

        return $renderView;
      }

      // Registra o usuário
      $message = $user['password'] == $user['confirmPassword'] ? $this->registerUser($user) : ['error_password' => 'As senhas não coincidem']; 
    }

    $renderView['user_register'] = ['message' => $message, 'user_email' => $user['user_email'] ?? ''];

    return $renderView;
  }

  // Cadastra o usuário e cria tabelas
  private function registerUser($user)
  {
    $userExists = $this->usersModel->getUser($user['email']);

    // Verifica se o usuário já existe
    if ($userExists) {
      return ['error_register' => 'Email já registrado'];
    }

    $registerUser = $this->usersModel->registerUser($user);
    $getUser = $this->usersModel->getUser(($user['email']));
    $message = ['error_register' => 'Erro ao cadastrar o usuário'];
    
    // Cadastra o usuário
    if (empty($registerUser) or empty($getUser)) {
      return $message;
    }

    $databaseName = 'm_user_' . $getUser[0]['id'];
    $createDatabase = $this->usersModel->createUserDatabase($databaseName);

    // Cria a base de dados para o usuário
    if (empty($createDatabase)) {
      return $message;
    }

    $createTables = $this->usersModel->createUserTables($databaseName);

    // Cria as tabelas padrões do usuário
    if (empty($createTables)) {
      return $message;
    }

    $message = ['success_register' => 'Cadastro efetuado com sucesso!'];

    return $message;
  }

  public function login()
  {
    // Verifica se o usuário está logado
    $this->checkSession();

    // View e conteúdo da página
    $view_name = 'user_login';
    $view_content = [];

    if (empty($_POST['user_password'])) {
      return [$view_name => $view_content];
    }

    $user = ['user_email' => $_POST['user_email'], 'user_password' => trim($_POST['user_password'])];
    $response = $this->usersModel->login_user($user);

    if (isset($response['error_login'])) {
      $message = ['error_login' => $response['error_login']];
      Logger::log('UsersController->login: ' . $response['error_login']);
    }

    // Se o usuário for localizado, redireciona para o painel
    if (isset($response['success_login']) and empty($_SESSION['user'])) {
      $_SESSION['user'] = [
        'user_id' => $response['success_login']['user_id'],
        'user_first_name' => $response['success_login']['user_first_name'],
        'user_last_name' => $response['success_login']['user_last_name'],
        'user_email' => $response['success_login']['user_email'],
      ];

      $message = ['success_login' => $response['success_login']['message']];

      header('Location: /panel/display');
      exit();
    }

    // Se o usuario não for localizado, retorna  mensagem de erro
    $view_content = ['message' => $message ];

    return [$view_name => $view_content];
  }

  // Verifica se o usuário possui sessão ativa
  private function checkSession()
  {
    if (isset($_SESSION['user']) and $_SESSION['user']) {
      header('Location: /panel/display');
      return true;
    }

    return false;
  }
}