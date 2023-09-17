<?php
require_once '../app/models/RegisterModel.php';

class RegisterController
{
  public $user;
  public $message;
  public $registerModel;

  public function __construct()
  {
    $this->registerModel = new RegisterModel();

    // Se o usuário estiver logado redireciona para o painel
    if (isset($_SESSION['user']) and $_SESSION['user']) {
      header('Location: /panel/display');
    }
  }

  // Retorna a view conforme rota
  public function start()
  {

    // Verifica se o form de cadastro foi submetido
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $getForm = $this->getForm();

      if ($getForm) {
        $this->registerUser();
      }
    }

    $renderView = [
      'register' => ['message' => $this->message ],
    ];

    return $renderView;
  }

  // Realiza validações antes de registrar
  private function getForm()
  {
    $this->user['email'] = $_POST['user_email'] ?? '';
    $this->user['firstName'] = $_POST['user_first_name'] ?? '';
    $this->user['lastName'] = $_POST['user_last_name'] ?? '';
    $this->user['password'] = trim($_POST['user_password']) ?? '';
    $this->user['confirmPassword'] = trim($_POST['user_confirm_password']) ?? '';

    // Não aceita campos vazios
    if (in_array('', $this->user, true)) {
      $this->message = ['error_register' => 'Todos os campos precisam ser preenchidos'];
      return false;
    }

    if ($this->user['password'] == $this->user['confirmPassword']) {
      return true;
    }

    $this->message = ['error_password' => 'As senhas não coincidem'];
    return false;
  }

  // Cadastra o usuário e cria tabelas
  private function registerUser()
  {

    // Verifica se o usuário já existe
    $userExists = $this->registerModel->getUser($this->user['email']);

    if ($userExists) {
      $this->message = ['error_register' => 'Email já registrado'];
      return false;
    }

    // Cadastra o usuário
    $registerUser = $this->registerModel->registerUser($this->user);
    $getUser = $this->registerModel->getUser(($this->user['email']));
    $this->message = ['error_register' => 'Erro ao cadastrar o usuário'];
    
    if (empty($registerUser) or empty($getUser)) {
      return false;
    }

    // Cria a base de dados para o usuário
    $databaseName = 'm_user_' . $getUser[0]['id'];
    $createDatabase = $this->registerModel->createUserDatabase($databaseName);

    if (empty($createDatabase)) {
      return false;
    }

    // Cria as tabelas padrões do usuário
    $createTables = $this->registerModel->createUserTables($databaseName);

    if (empty($createTables)) {
      return false;
    }

    $this->message = ['success_register' => 'Cadastro efetuado com sucesso!'];

    return true;
  }
}