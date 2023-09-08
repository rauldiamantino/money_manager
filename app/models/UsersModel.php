<?php
require_once '../app/dao/UsersDAO.php';

class UsersModel
{
  public $user_email;
  public $usersDao;
  public $database_user;

  public function __construct()
  {
    $this->usersDao = new UsersDAO();
  }

  // Registra o usuário caso ele ainda não exista
  public function register_user($data)
  {
    $this->user_email = $data['user_email'] ?? '';
    $get_user = $this->get_user();

    $response = [];

    if ($get_user) {
      $response = ['error_register' => 'E-mail já cadastrado'];
      Logger::log('UsersModel->register_user' . $response);
    }

    if (empty($get_user) and $this->usersDao->register_user_db($data)) {
      $user = $this->get_user();
      $database_user = 'm_user_' . $user[0]['id'];
      $this->create_database_user($database_user);

      $response = ['success_register' => 'Cadastro realizado com sucesso!'];
    }

    return $response;
  }

  // Cria database do usuário após ter sido cadastrado
  private function create_database_user($database_user)
  {
    $result = $this->usersDao->create_database_user($database_user);
    $response = ['success_create' => 'Database criado com sucesso'];

    if (empty($result)) {
      $response = ['error_register' => $result];
      Logger::log('UsersModel->create_database_user: ' . $response['error_register'] . ' retorno vazio');
    }

    return $response;
  }

  // Verifica se o usuário possui conta e faz login
  public function login_user($data)
  {
    $this->user_email = $data['user_email'] ?? '';
    $get_user = $this->get_user();
    $response = [];

    if (empty($get_user)) {
      $response = ['error_login' => 'Dados inválidos'];
      Logger::log('UsersModel->login_user: ' . $response['error_login']);

      return $response;
    }

    $validation_user = ['user_email' => false, 'user_password' => false];

    if ($data['user_email'] == $get_user[0]['email']) {
      $validation_user['user_email'] = true;
    }

    if (password_verify(trim($data['user_password']), $get_user[0]['password'])) {
      $validation_user['user_password'] = true;
    }

    $response = [
      'success_login' => [
        'message' => 'Dados corretos!',
        'user_id' => $get_user[0]['id'],
        'user_first_name' => $get_user[0]['first_name'],
        'user_last_name' => $get_user[0]['last_name'],
        'user_email' => $get_user[0]['email'],
      ],
    ];
    foreach ($validation_user as $linha) :
      if (empty($linha)) {
        $response = ['error_login' => 'Dados inválidos'];
        Logger::log('UsersModel->login_user: ' . $response['error_login']);
      }
    endforeach;

    return $response;
  }

  // Obtém os dados da conta do usuário
  public function get_myaccount($user_id)
  {
    $result = $this->usersDao->get_myaccount_db($user_id);
    
    if (empty($result)) {
      Logger::log('UsersModel->get_myaccount: Erro ao buscar conta do usuário');
    }

    return $result;
  }

  // Atualiza os dados da conta do usuário
  public function update_myaccount($new_data)
  {
    $result = $this->usersDao->update_users_db($new_data);

    if (empty($result)) {
      Logger::log(['method' => 'UsersModel->update_myaccount', 'result' => $result ], 'error');
      return false;
    }

    return true;
  }

  // Atualiza senha do conta do usuário
  public function update_myaccount_password($new_data)
  {
    $get_user = $this->get_user($new_data['user_id']);
    $result_update = '';

    if ($get_user) {
      $result_update = $this->usersDao->update_password_user_db($new_data);

      if ($result_update) {
        return true;
      }

      Logger::log(['method' => 'UsersModel->update_myaccount_password', 'result' => $result_update ], 'error');
      return false;
    }

    Logger::log(['method' => 'UsersModel->update_myaccount_password', 'result' => $get_user ], 'error');
    return false;
  }

  // Busca usuário no Banco de Dados
  private function get_user($user_id = 0)
  {
    $response = $this->usersDao->get_user_db($this->user_email, $user_id);

    if (empty($response)) {
      Logger::log('UsersModel->get_user: Erro ao buscar usuário');
    }

    return $response;
  }
}