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
    $user = [];
    $message = [];
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

    // Valida se o usuário está logado
    if ($this->checkSession()) {
      Logger::log(['method' => 'UsersController->login', 'result' => 'Usuario possui sessão ativa']);
    }

    // View e conteúdo da página
    $user = [];
    $renderView = ['user_login' => ['message' => []]];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

      // Recupera formulário
      $user['email'] = $_POST['user_email'] ?? '';
      $user['password'] = trim($_POST['user_password']) ?? '';

      if (in_array('', $user, true)) {
        $message = ['error_login' => 'Todos os campos precisam ser preenchidos'];
        $renderView['user_login']['message'] = $message;

        return $renderView;
      }

      // Verifica se o usuário existe
      $message = ['error_login' => 'Dados inválidos'];
      $renderView['user_login']['message'] = $message;

      $getUser = $this->usersModel->getUser($user['email']);

      if (empty($getUser)) {
        return $renderView;
      }

      if (password_verify(trim($user['password']), $getUser[0]['password'])) {
        $message = ['success_login' => 'Dados corretos'];
        $renderView['user_login']['message'] = $message;
      }
    }

    // Se o usuário for localizado, redireciona para o painel
    if (isset($renderView['user_login']['message']['success_login']) and empty($_SESSION['user'])) {

      $_SESSION['user'] = [
        'user_id' => $getUser[0]['id'],
        'user_first_name' => $getUser[0]['first_name'],
        'user_last_name' => $getUser[0]['last_name'],
        'user_email' => $getUser[0]['email'],
      ];

      header('Location: /panel/display');
      exit();
    }


    return $renderView;
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