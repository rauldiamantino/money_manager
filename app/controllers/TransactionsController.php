<?php
require_once '../app/models/TransactionsModel.php';
require_once '../app/controllers/PanelController.php';

class TransactionsController extends PanelController
{
  public $transactionsModel;

  // Exibe transações
  public function transactions($userId)
  {
    $this->transactionsModel = new TransactionsModel();

    // Valida sessão e login
    parent::checkLogout($userId);
    parent::checkSession($userId);

    $message = [];

    // Nova transação
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
      $transactions['transaction']['type'] = 'I';
      $transactions['transaction']['amount'] = $_POST['transaction_amount'];
      $transactions['transaction']['transaction_id'] = $_POST['edit_income'];

      if ($transactions['operation']['edit_income']) {
        $message = $this->editIncome($userId, $transactions['transaction']);
      }
      else {
        $message = $this->createIncome($userId, $transactions['transaction']);
      }
    }

    // Adiciona ou edita uma despesa
    if ($transactions['operation']['add_expense']) {
      $transactions['transaction']['type'] = 'E';
      $transactions['transaction']['amount'] = -1 * $_POST['transaction_amount'];
      $transactions['transaction']['transaction_id'] = $_POST['edit_expense'];

      // Edita despesa
      if ($transactions['operation']['edit_expense']) {
        $message = $this->editExpense($userId, $transactions['transaction']);
      }
      else {
        $message = $this->createExpense($userId, $transactions['transaction']);
      }
    }

    // Apaga transação
    if ($transactions['operation']['delete_transaction']) {
      $transactions['transaction'] = [
        'id' => $_POST['delete_transaction_id'],
        'table' => $_POST['delete_transaction_type'] == 'E' ? 'expenses' : 'incomes',
      ];

      $message = $this->deleteTransaction($userId, $transactions['transaction']);
    }

    // Altera status da transação
    if ($transactions['operation']['change_status']) {

      $transactions['transaction'] = [
        'id' => $_POST['change_status_transaction_id'],
        'status' => intval($_POST['edit_transaction_status']),
        'table' => $_POST['change_status_transaction_type'] == 'E' ? 'expenses' : 'incomes',
      ];

      $message = $this->changeStatus($userId, $transactions['transaction']);
    }

    //------------------------------------------------ Filtros ------------------------------------------------//

    // Recupera filtros da sessão caso o usuário ainda não tenha feito nenhuma escolha
    $year = $_POST['filterYear'] ? $_POST['filterYear'] : $_SESSION['user']['filters']['date']['year'];
    $month = $_POST['filterMonth'] ? $_POST['filterMonth'] : $_SESSION['user']['filters']['date']['month'];
    $type = $_POST['filterTransactions'] ? $_POST['filterTransactions'] : $_SESSION['user']['filters']['type'];

    $filterSelected = ['type' => $type, 'date' => ['year' => $year, 'month' => $month ]];

    // Guarda na sessão os filtros escolhidos
    $_SESSION['user']['filters'] = $filterSelected;

    // Prepara Filtros
    $filters = [
      'nameMonths' => [
        1 => 'Janeiro',
        2 => 'Fevereiro',
        3 => 'Março',
        4 => 'Abril',
        5 => 'Maio',
        6 => 'Junho',
        7 => 'Julho',
        8 => 'Agosto',
        9 => 'Setembro',
        10 => 'Outubro',
        11 => 'Novembro',
        12 => 'Dezembro',
      ],
      'currentDate' => [
        'year' => $filterSelected['date']['year'] ?? date('Y'),
        'month' => sprintf('%02d', $filterSelected['date']['month'] ?? date('m')),
      ],
    ];

    // Ajusta data para consultar o Banco de Dados
    $filters['selectedDate'] = implode('-', $filters['currentDate']);

    // Recupera transações
    $getTransactions = [];

    if ($filterSelected['type'] == 'I') {
      $filters['type'] = ['incomes' => 'checked'];
      $getTransactions['items'] = $this->transactionsModel->getIncomes($userId, $filters['selectedDate']);
    }
    elseif ($filterSelected['type'] == 'E') {
      $filters['type'] = ['expenses' => 'checked'];
      $getTransactions['items'] = $this->transactionsModel->getExpenses($userId, $filters['selectedDate']);
    }
    else {
      $filters['type'] = ['all' => 'checked'];
      $getTransactions['items'] = $this->transactionsModel->getTransactions($userId, $filters['selectedDate']);
    }

    // Totais
    $getTransactions['totals'] = ['inc' => 0, 'exp' => 0];
    foreach ($getTransactions['items'] as $key => $value):

      if ($value['type'] == 'E') {
        $expense_amount = $value['amount'] * -1;
        $getTransactions['totals']['exp'] += $expense_amount;
      }

      if ($value['type'] == 'I') {
        $getTransactions['totals']['inc'] += $value['amount'];
      }
    endforeach;

    $getTransactions['totals']['balance'] = $getTransactions['totals']['inc'] - $getTransactions['totals']['exp'];

    // Prepara renderização da view
    $user = $this->transactionsModel->getUser('', $userId);
    $accounts = $this->transactionsModel->getAccounts($userId);
    $categories = $this->transactionsModel->getCategories($userId);

    $renderView = [
      'panel/templates/nav' => [
        'user_id' => $userId,
        'active_tab' => 'transactions',
        'action_route' => 'transactions/' . $userId,
        'user_first_name' => $user[0]['first_name'],
        'user_last_name' => $user[0]['last_name'],
      ],
      'panel/transactions' => [
        'transactions' => $getTransactions,
        'user_id' => $userId,
        'categories' => $categories,
        'accounts' => $accounts,
        'message' => $message,
        'filters' => $filters,
      ],
    ];

    return $renderView;
  }

  // Adiciona uma nova receita
  public function createIncome($userId, $income)
  {
    $createIncome = $this->transactionsModel->createIncome($userId, $income);

    if (empty($createIncome)) {
      return ['error_transaction' => 'Erro ao cadastrar receita'];
    }

    return [];
  }

  // Edita uma receita existente
  public function editIncome($userId, $income)
  {
    $editIncome = $this->transactionsModel->editIncome($userId, $income);

    if (empty($editIncome)) {
      return ['error_transaction' => 'Erro ao editar receita'];
    }

    return [];
  }

  // Adiciona uma nova despesa
  public function createExpense($userId, $expense)
  {
    $createExpense = $this->transactionsModel->createExpense($userId, $expense);

    if (empty($createExpense)) {
      return ['error_transaction' => 'Erro ao cadastrar despesa'];
    }

    return [];
  }

  // Edita uma despesa existente
  public function editExpense($userId, $expense)
  {
    $editExpense = $this->transactionsModel->editExpense($userId, $expense);

    if (empty($editExpense)) {
      return ['error_transaction' => 'Erro ao editar despesa'];
    }

    return [];
  }

  public function deleteTransaction($userId, $transaction)
  {
    $deleteTransaction = $this->transactionsModel->deleteTransaction($userId, $transaction);

    if (empty($deleteTransaction)) {
      return ['error_transaction' => 'Erro ao apagar transação'];
    }

    return [];
  }

  public function changeStatus($userId, $transaction)
  {
    $changeStatus = $this->transactionsModel->changeStatus($userId, $transaction);;

    if (empty($changeStatus)) {
      return ['error_transaction' => 'Erro ao alterar status da transação'];
    }

    return [];
  }
}