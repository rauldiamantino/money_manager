<?php
require_once '../app/dao/UsersDAO.php';
require_once '../app/Database.php';

class UsersModel
{
  public $user_email;
  public $usersDao;
  public $database;
  public $database_user;

  public function __construct()
  {
    $this->usersDao = new UsersDAO();
    $this->database = new Database();
  }

  // Registra o usuário caso ele ainda não exista
  public function register_user($data)
  {
    $this->user_email = $data['user_email'] ?? '';
    $get_user = $this->get_user();

    $response = [];

    if ($get_user) {
      $response = ['error_register' => 'E-mail já cadastrado'];
      Logger::log(['method' => 'PanelModel->register_user', 'result' => $response ]);
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
      Logger::log(['method' => 'PanelModel->create_database_user', 'result' => $response ]);
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
      Logger::log(['method' => 'PanelModel->login_user', 'result' => $response ]);

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
        Logger::log(['method' => 'PanelModel->login_user', 'result' => $response ]);
      }
    endforeach;

    return $response;
  }

  // Obtém os dados da conta do usuário
  public function getMyaccount($userId)
  {
    $databaseName = DB_NAME;
    $sql = 'SELECT * FROM users WHERE id = :id';
    $params = ['id' => $userId ];

    $this->database->switch_database($databaseName);
    $result = $this->database->select($sql, ['params' => $params, 'database_name' => $databaseName ]);

    Logger::log(['method' => 'PanelModel->getMyaccount', 'result' => $result ]);

    return $result;
  }

  // Atualiza os dados da conta do usuário
  public function updateMyaccount($newData)
  {
    $sql = 'UPDATE users
            SET first_name = :first_name, last_name = :last_name, email = :email
            WHERE id = :id';

    $params = [
      'first_name' => $newData['user_first_name'],
      'last_name' => $newData['user_last_name'],
      'email' => $newData['user_email'],
      'id' => $newData['user_id'],
    ];

    $result = $this->database->insert($sql, $params);
    Logger::log(['method' => 'PanelModel->updateMyaccount', 'result' => $result ]);

    return true;
  }

  // Atualiza senha do conta do usuário
  public function updateMyaccountPassword($newData)
  {
    $sql = 'UPDATE users
            SET password = :password
            WHERE id = :id';

    $params = [
      'password' => password_hash($newData['user_new_password'], PASSWORD_DEFAULT),
      'id' => $newData['user_id'],
    ];

    $result = $this->database->insert($sql, $params);
    Logger::log(['method' => 'PanelModel->updateMyaccountPassword', 'result' => $result ]);

    return $result;
  }

  // Busca o usuário
  public function getUser($userEmail, $userId = 0)
  {
    // Busca por e-mail
    $where = 'WHERE email = :email';
    $params = ['email' => $userEmail ];

    // Busca por id
    if ($userId) {
      $where = 'WHERE id = :id';
      $params = ['id' => $userId ];
    }

    $sql = 'SELECT * FROM users ' . $where;
    $result = $this->database->select($sql, ['params' => $params ]);

    Logger::log(['method' => 'PanelModel->getUser', 'result' => $result ]);

    return $result;
  }

  // Busca usuário no Banco de Dados
  private function get_user($user_id = 0)
  {
    $response = $this->usersDao->get_user_db($this->user_email, $user_id);

    if (empty($response)) {
      Logger::log(['method' => 'PanelModel->get_user', 'result' => $response ]);
    }

    return $response;
  }
}