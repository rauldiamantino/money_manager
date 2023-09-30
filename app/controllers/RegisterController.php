<?php
require_once '../app/models/RegisterModel.php';
require_once '../app/controllers/UsersController.php';

class RegisterController extends UsersController
{
  public $message;

  // Retorna a view conforme rota
  public function start()
  {
    parent::checkSession();

    // Verifica se o form de cadastro foi submetido
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $getForm = $this->getForm();

      if ($getForm) {
        $this->registerUser($getForm);
      }
    }

    $renderView = ['register' => ['message' => $this->message ]];
    return $renderView;
  }

  // Realiza validações antes de registrar
  private function getForm()
  {
    $user['email'] = $_POST['user_email'] ?? '';
    $user['firstName'] = $_POST['user_first_name'] ?? '';
    $user['lastName'] = $_POST['user_last_name'] ?? '';
    $user['password'] = trim($_POST['user_password']) ?? '';
    $user['confirmPassword'] = trim($_POST['user_confirm_password']) ?? '';

    // Não aceita campos vazios
    if (in_array('', $user, true)) {
      $this->message = ['error_register' => 'Todos os campos precisam ser preenchidos'];
      return false;
    }

    if ($user['password'] == $user['confirmPassword']) {
      return $user;
    }

    $this->message = ['error_password' => 'As senhas não coincidem'];
    return false;
  }

  // Cadastra o usuário e cria tabelas
  private function registerUser($user)
  {
    // Verifica se o usuário já existe
    $registerModel = new RegisterModel();
    $userExists = $registerModel->getUser($user['email']);

    if ($userExists) {
      $this->message = ['error_register' => 'Email já registrado'];
      return false;
    }

    // Cadastra o usuário
    $registerUser = $registerModel->registerUser($user);
    $getUser = $registerModel->getUser(($user['email']));
    $this->message = ['error_register' => 'Erro ao cadastrar o usuário'];
    
    if (empty($registerUser) or empty($getUser)) {
      return false;
    }

    // Cria a base de dados para o usuário
    $databaseName = 'm_user_' . $getUser[0]['id'];
    $createDatabase = $registerModel->createUserDatabase($databaseName);

    if (empty($createDatabase)) {
      return false;
    }

    // Cria as tabelas padrões do usuário
    $createTables = $registerModel->createUserTables($databaseName);

    if (empty($createTables)) {
      return false;
    }

    $this->message = ['success_register' => 'Cadastro efetuado com sucesso!'];
    return true;
  }
}