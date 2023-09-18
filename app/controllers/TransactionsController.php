<?php 
require_once '../app/controllers/PanelController.php';

class TransactionsController extends PanelController
{

  // Exibe todas as transações
  public function transactions($userId)
  {
    $this->userId = $userId;

    // Valida se o usuário está logado
    if (parent::checkSession() or parent::checkLogout()) {
      Logger::log(['method' => 'PanelController->transactions', 'result' => 'Usuario Desconectado'], 'alert');
    }

    $message = [];

    // Recupera informações da transação
    $transactions = [
      'operation' => [
        'add_income' => $_POST['add_income'] ?? 0,
        'add_expense' => $_POST['add_expense'] ?? 0,
        'edit_income' => $_POST['edit_income'] ?? 0,
        'edit_expense' => $_POST['edit_expense'] ?? 0,
        'delete_transaction' => $_POST['delete_transaction_id'] ?? 0,
        'change_status' => $_POST['change_status_transaction_id'] ?? 0,
      ],
      'transaction' => [
        'status' => 0,
        'date' => $_POST['transaction_date'],
        'amount' => $_POST['transaction_amount'],
        'account_id' => $_POST['transaction_account'],
        'category_id' => $_POST['transaction_category'],
        'description' => $_POST['transaction_description'],
      ]
    ];

    // Adiciona ou edita uma receita
    if ($transactions['operation']['add_income']) {

      // Campos específicos para uma receita
      $transactions['transaction']['type'] = 'I';
      $transactions['transaction']['amount'] = $_POST['transaction_amount'];
      $transactions['transaction']['transaction_id'] = $_POST['edit_income'];

      if ($transactions['operation']['edit_income']) {
        $message = $this->editIncome($transactions['transaction']);
      }
      else {
        $message = $this->createIncome($transactions['transaction']);
      }
    }

    // Adiciona ou edita uma despesa
    if ($transactions['operation']['add_expense']) {

      // Campos específicos para uma despesa
      $transactions['transaction']['type'] = 'E';
      $transactions['transaction']['amount'] = -1 * $_POST['transaction_amount'];
      $transactions['transaction']['transaction_id'] = $_POST['edit_expense'];

      // Edita despesa
      if ($transactions['operation']['edit_expense']) {
        $message = $this->editExpense($transactions['transaction']);
      }
      else {
        $message = $this->createExpense($transactions['transaction']);
      }
    }

    // Apaga transação
    if ($transactions['operation']['delete_transaction']) {

      $transactions['transaction'] = [
        'id' => $_POST['delete_transaction_id'],
        'table' => $_POST['delete_transaction_type'] == 'E' ? 'expenses' : 'incomes',
      ];

      $message = $this->deleteTransaction($transactions['transaction']);
    }

    // Altera status da transação
    if ($transactions['operation']['change_status']) {

      $transactions['transaction'] = [
        'id' => $_POST['change_status_transaction_id'],
        'status' => intval($_POST['edit_transaction_status']),
        'table' => $_POST['change_status_transaction_type'] == 'E' ? 'expenses' : 'incomes',
      ];

      $message = $this->changeStatus($transactions['transaction']);
    }

    // Prepara totais das transações
    $getTransactions = [
      'items' => $this->panelModel->getTransactions($this->userId), 
      'totals' => ['incomes' => 0, 'expenses' => 0],
    ];

    foreach ($getTransactions['items'] as $key => $value) :
      
      if ($value['type'] == 'E') {
        $expense_amount = $value['amount'] * -1;
        $getTransactions['totals']['expenses'] += $expense_amount;
      }

      if ($value['type'] == 'I') {
        $getTransactions['totals']['incomes'] += $value['amount'];
      }

    endforeach;

    $getTransactions['totals']['balance'] = $getTransactions['totals']['incomes'] - $getTransactions['totals']['expenses'];

    // View e conteúdo para o menu de navegação
    $this->activeTab = 'transactions';
    $navViewName = 'panel/templates/nav';
    $this->actionRoute = 'transactions/' . $this->userId;

    $navViewContent = [
      'user_id' => $this->userId,
      'active_tab' => $this->activeTab,
      'action_route' => $this->actionRoute,
      'user_first_name' => $this->userFirstName,
      'user_last_name' => $this->userLastName,
    ];

    // View e conteúdo para a página de transações
    $transactionsViewName = 'panel/transactions';
    $accounts = $this->panelModel->getAccounts($this->userId);
    $categories = $this->panelModel->getCategories($this->userId);

    $transactionsViewContent = [
      'transactions' => $getTransactions,
      'user_id' => $this->userId,
      'categories' => $categories,
      'accounts' => $accounts,
      'message' => $message,
    ];

    return [ $navViewName => $navViewContent, $transactionsViewName => $transactionsViewContent ];
  }

  // Adiciona uma nova receita ao formulário
  public function createIncome($income)
  {
    $createIncome = $this->panelModel->createIncome($this->userId, $income);

    if (empty($createIncome)) {
      return ['error_transaction' => 'Erro ao cadastrar receita'];
    }

    return [];
  }

  // Edita uma receita existente
  public function editIncome($income)
  {
    $editIncome = $this->panelModel->editIncome($this->userId, $income);

    if (empty($editIncome)) {
      return ['error_transaction' => 'Erro ao editar receita'];
    }

    return [];
  }

  // Adiciona uma nova despesa ao formulário
  public function createExpense($expense)
  {
    $createExpense = $this->panelModel->createExpense($this->userId, $expense);

    if (empty($createExpense)) {
      return ['error_transaction' => 'Erro ao cadastrar despesa'];
    }

    return [];
  }

  // Edita uma despesa existente
  public function editExpense($expense)
  {
    $editExpense = $this->panelModel->editExpense($this->userId, $expense);

    if (empty($editExpense)) {
      return ['error_transaction' => 'Erro ao editar despesa'];
    }

    return [];
  }

  public function deleteTransaction($transaction)
  {
    $deleteTransaction = $this->panelModel->deleteTransaction($this->userId, $transaction);

    if (empty($deleteTransaction)) {
      return ['error_transaction' => 'Erro ao apagar transação'];
    }

    return [];
  }

  public function changeStatus($transaction)
  {
    $changeStatus = $this->panelModel->changeStatus($this->userId, $transaction);;

    if (empty($changeStatus)) {
      return ['error_transaction' => 'Erro ao alterar status da transação'];
    }

    return [];
  }
}