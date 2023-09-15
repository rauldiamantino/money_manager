<?php
require_once '../app/dao/PanelDAO.php';
require_once '../app/Database.php';

class PanelModel {
  public $panelDAO;
  public $database;

  public function __construct()
  {
    $this->panelDAO = new PanelDAO();
    $this->database = new Database();
  }

  // Obtém conteúdo a ser exibido no painel principal
  public function getContentPanel()
  {
    $result = [];
    return $result;
  }

  // Obtém receitas e despesas do usuário
  public function get_transactions($user_id)
  {
    $result = $this->panelDAO->get_transactions_db($user_id);

    if (empty($result)) {
      Logger::log(['method' => 'PanelModel->get_transactions', 'result' => $result ], 'alert');
    }

    return $result;
  }

  // Obtém todas as contas cadastradas do usuário
  public function get_accounts($user_id)
  {
    $result = $this->panelDAO->get_accounts_db($user_id);

    if (empty($result)) {
      Logger::log(['method' => 'PanelModel->get_accounts', 'result' => $result ], 'alert');
    }

    return $result;
  }

  // Obtém todas as categorias cadastradas do usuário
  public function get_categories($user_id)
  {
    $result = $this->panelDAO->get_categories_db($user_id);

    if (empty($result)) {
      Logger::log(['method' => 'PanelModel->get_categories', 'result' => $result ], 'alert');
    }

    return $result;
  }

  // Adiciona receita
  public function add_income($user_id, $income)
  {
    $result = $this->panelDAO->add_income_db($user_id, $income);

    if (empty($result)) {
      Logger::log(['method' => 'PanelModel->add_income', 'result' => $result ], 'error');
      return false;
    }

    return true;
  }
  
  // Adiciona despesa
  public function add_expense($user_id, $expense)
  {
    $result = $this->panelDAO->add_expense_db($user_id, $expense);

    if (empty($result)) {
      Logger::log(['method' => 'PanelModel->add_expense', 'result' => $result ], 'error');
      return false;
    }

    return true;
  }

  // Apaga transação
  public function delete_transaction($user_id, $transaction)
  {
    $result = $this->panelDAO->delete_transaction_db($user_id, $transaction);

    if (empty($result)) {
      Logger::log(['method' => 'PanelModel->delete_transaction', 'result' => $result ], 'error');
      return false;
    }

    return true;
  }

  // Altera status da transação
  public function edit_transaction_status($user_id, $transaction)
  {
    $result = $this->panelDAO->edit_transaction_status_db($user_id, $transaction);

    if (empty($result)) {
      Logger::log(['method' => 'PanelModel->edit_transaction_status', 'result' => $result ], 'error');
      return false;
    }

    return true;
  }

  // Verifica se o usuário existe na tabela de usuários
  public function check_user_exists($user_id)
  {
    $check_user = $this->panelDAO->check_user_db($user_id);
    $response = ['success' => 'Usuário existe'];

    if (empty($check_user)) {
      $response = ['error_user' => 'Usuário não existe na tabela users'];
      Logger::log(['method' => 'PanelModel->check_user_exists', 'result' => $check_user ], 'error');
    }

    return $response;
  }

  //---------------------- Nova Model ----------------------//

  // Cria uma conta para o usuário
  public function createAccount($userId, $accountName)
  {
    $databaseName = 'm_user_' . $userId;
    $sql = 'INSERT INTO accounts (name) VALUES (:name);';
    $params = ['name' => $accountName ];

    $this->database->switch_database(($databaseName));
    $result = $this->database->insert($sql, $params);

    Logger::log(['method' => 'PanelModel->createAccount', 'result' => $result ]);

    return $result;
  }

  // Edita uma conta já existente
  public function editAccount($userId, $account)
  {
    $databaseName = 'm_user_' . $userId;
    $sql = 'UPDATE accounts SET name = :name WHERE id = :id;';
    $params = ['id' => $account['id'], 'name' => $account['name'] ];

    $this->database->switch_database(($databaseName));
    $result = $this->database->insert($sql, $params);

    Logger::log(['method' => 'PanelModel->editAccount', 'result' => $result ]);

    return $result;
  }

  // Apaga conta
  public function deleteAccount($userId, $accountId)
  {
    $databaseName = 'm_user_' . $userId;
    $sql = 'DELETE FROM accounts WHERE id = :id;';
    $params = ['id' => $accountId ];

    $this->database->switch_database($databaseName);
    $result = $this->database->delete($sql, $params);

    Logger::log(['method' => 'PanelModel->deleteAccount', 'result' => $result ]);

    return true;
  }

  // Verifica se a conta está em uso em alguma transação
  public function accountInUse($userId, $accountId)
  {
    $databaseName = 'm_user_' . $userId;
    $sql = 'SELECT account_id, description FROM incomes WHERE account_id = :account_id
            UNION
            SELECT account_id, description FROM expenses WHERE account_id = :account_id';

    $params = ['account_id' => $accountId ];

    $this->database->switch_database($databaseName);
    $result = $this->database->select($sql, ['params' => $params, 'database_name' => $databaseName ]);

    Logger::log(['method' => 'PanelModel->accountInUse', 'result' => $result ]);

    return $result;
  }

  // Verifica se a conta já existe para o usuário
  public function accountExists($user_id, $account)
  {
    $databaseName = 'm_user_' . $user_id;
    $paramWhere = array_key_first($account);

    $sql = 'SELECT * FROM accounts WHERE ' . $paramWhere . ' = :' . $paramWhere;
    $params = [ $paramWhere => reset($account)];

    $this->database->switch_database($databaseName);
    $result = $this->database->select($sql, ['params' => $params, 'database_name' => $databaseName ]);

    Logger::log(['method' => 'PanelModel->accountExists', 'result' => $result ]);

    return $result;
  }

  // Cria uma categoria para o usuário
  public function createCategory($userId, $categoryName)
  {
    $databaseName = 'm_user_' . $userId;
    $sql = 'INSERT INTO categories (name) VALUES (:name);';
    $params = ['name' => $categoryName ];

    $this->database->switch_database(($databaseName));
    $result = $this->database->insert($sql, $params);

    Logger::log(['method' => 'PanelModel->createCategory', 'result' => $result ]);

    return $result;
  }

  // Edita uma categoria já existente
  public function editCategory($userId, $category)
  {
    $databaseName = 'm_user_' . $userId;
    $sql = 'UPDATE categories SET name = :name WHERE id = :id;';
    $params = ['id' => $category['id'], 'name' => $category['name'] ];

    $this->database->switch_database(($databaseName));
    $result = $this->database->insert($sql, $params);

    Logger::log(['method' => 'PanelModel->editCategory', 'result' => $result ]);

    return $result;
  }

  // Apaga categoria
  public function deleteCategory($userId, $categoryId)
  {
    $databaseName = 'm_user_' . $userId;
    $sql = 'DELETE FROM categories WHERE id = :id;';
    $params = ['id' => $categoryId ];

    $this->database->switch_database($databaseName);
    $result = $this->database->delete($sql, $params);

    Logger::log(['method' => 'PanelModel->deleteCategory', 'result' => $result ]);

    return true;
  }

  // Verifica se a categoria está em uso em alguma transação
  public function categoryInUse($userId, $categoryId)
  {
    $databaseName = 'm_user_' . $userId;
    $sql = 'SELECT category_id, description FROM incomes WHERE category_id = :category_id
            UNION
            SELECT category_id, description FROM expenses WHERE category_id = :category_id';

    $params = ['category_id' => $categoryId ];

    $this->database->switch_database($databaseName);
    $result = $this->database->select($sql, ['params' => $params, 'database_name' => $databaseName ]);

    Logger::log(['method' => 'PanelModel->categoryInUse', 'result' => $result ]);

    return $result;
  }

  // Verifica se a categoria já existe para o usuário
  public function categoryExists($user_id, $category)
  {
    $databaseName = 'm_user_' . $user_id;
    $paramWhere = array_key_first($category);

    $sql = 'SELECT * FROM categories WHERE ' . $paramWhere . ' = :' . $paramWhere;
    $params = [ $paramWhere => reset($category)];

    $this->database->switch_database($databaseName);
    $result = $this->database->select($sql, ['params' => $params, 'database_name' => $databaseName ]);

    Logger::log(['method' => 'PanelModel->categoryExists', 'result' => $result ]);

    return $result;
  }
}