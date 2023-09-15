<?php
 // echo '<script> var user_id = ' . $_SESSION['user']['user_id'] . ';</script>';

 
$total_incomes = 0;
$total_expenses = 0;

foreach ($data['transactions'] as $key => $value) :
  
  if ($value['type'] == 'E') {
    $expense_amount = $value['amount'] * -1;
    $total_expenses += $expense_amount;
  }

  if ($value['type'] == 'I') {
    $total_incomes += $value['amount'];
  }

endforeach;
?>

<link rel="stylesheet" href="/css/transactions.css">

<section class="container mt-4">
  <h1 class="mb-4 text-center">Receitas e Despesas</h1>

  <div class="mb-2 d-flex gap-3 position-relative">
    <a href="" class="link-success link-offset-2 link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover link_add_transaction" data-toggle="modal" data-target="#modal_income" id="link_add_income">
      <i class="bi bi-file-earmark-plus"></i>
      Nova Receita
    </a>
    <a href="" class="link-danger link-offset-2 link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover link_add_transaction" data-toggle="modal" data-target="#modal_expense" id="link_add_expense">
      <i class="bi bi-file-earmark-plus"></i>
      Nova Despesa
    </a>

    <?php if (isset($data['message']['error_transaction'])) { ?>
      <div class="position-absolute top-50 start-50 translate-middle col-md-8 col-lg-6 col-xl-4 mx-auto alert alert-danger text-center small p-1 rounded-0" id="alert_transaction"><?php echo $data['message']['error_transaction'] ?></div>
    <?php } ?>

    <?php if (isset($data['message']['success'])) { ?>
      <div class="d-none position-absolute top-50 start-50 translate-middle col-md-8 col-lg-6 col-xl-4 mx-auto alert alert-success text-center small p-1 rounded-0" id="alert_transaction"><?php echo $data['message']['success'] ?></div>
    <?php } ?>
  </div>

  <table class="table table-hover">
    <thead>
      <tr class="text-center">
        <th>Descrição</th>
        <th>Conta</th>
        <th>Categoria</th>
        <th>Valor</th>
        <th>Data</th>
        <th colspan="3">Ação</th>
      </tr>
    </thead>
    <tbody class="table-group-divider text-center">
      <?php foreach ($data['transactions'] as $value) : ?>
        <tr>
          <td class="text-start"><?php echo $value['description']; ?></td>
          <td><?php echo $value['account_name']; ?></td>
          <td><?php echo $value['category_name']; ?></td>
          <td class="<?php echo $value['amount'] <= 0 ? 'text-danger' : 'text-success'; ?> text-end">
            <?php echo 'R$ ' . number_format($value['amount'], 2, ',', '.'); ?>
          </td>
          <td><?php echo date('d/m/Y', strtotime($value['date'])); ?></td>
          <td class="px-0 col-10-css lh-1">
            <a href="" class="text-black link_edit_transaction"
              data-toggle="modal"
              data-target="#modal_<?php echo $value['type'] == 'I' ? 'income' : 'expense'?>"
              data-transaction_id="<?php echo $value['id']; ?>"
              data-transaction_type="<?php echo $value['type']; ?>"
              data-transaction_description="<?php echo $value['description']; ?>"
              data-transaction_account_name="<?php echo $value['account_name']; ?>"
              data-transaction_category_name="<?php echo $value['category_name']; ?>"
              data-transaction_amount="<?php echo $value['amount']; ?>" data-transaction_date="<?php echo $value['date']; ?>">

              <i class="bi bi-pencil-square fs-5"></i>
            </a>
          </td>
          <td class="px-0 col-10-css lh-1">
            <form action="<?php echo 'panel/transactions/' . $data['user_id'] ?>" method="POST" id="<?php echo 'delete-' . $value['id'] . '-' . $value['type']?>">
              <input type="hidden" name="delete_transaction_id" value="<?php echo $value['id']; ?>">
              <input type="hidden" name="delete_transaction_type" value="<?php echo $value['type']; ?>">

              <button class="p-0 lh-1 border-0 bg-transparent text-danger" form="<?php echo 'delete-' . $value['id'] . '-' . $value['type']?>"><i class="bi bi-x-circle fs-5"></i></button>
            </form>
          </td>
          <td class="px-0 col-10-css lh-1">
            <form action="<?php echo 'panel/transactions/' . $data['user_id'] ?>" method="POST" id="<?php echo 'edit-' . $value['id'] . '-' . $value['type']?>">
              <input type="hidden" name="change_status_transaction_id" value="<?php echo $value['id']; ?>">
              <input type="hidden" name="change_status_transaction_type" value="<?php echo $value['type']; ?>">

              <?php if ($value['status'] == "0") { ?>
                <input type="hidden" name="edit_transaction_status" value="1">
                <button class="p-0 lh-1 border-0 bg-transparent text-secondary" form="<?php echo 'edit-' . $value['id'] . '-' . $value['type']?>"><i class="bi bi-check-circle fs-5"></i></button>
              <?php } ?>

              <?php if ($value['status'] == "1") { ?>
                <input type="hidden" name="edit_transaction_status" value="0">
                <button class="p-0 lh-1 border-0 bg-transparent text-success" form="<?php echo 'edit-' . $value['id'] . '-' . $value['type']?>"><i class="bi bi-check-circle-fill fs-5"></i></button>
              <?php } ?>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="d-flex gap-3 justify-content-center text-center user-select-none align-items-center">
    <div class="card alert alert-success w-100 m-0 py-0">
      <div class="card-body">
        <h5 class="card-title">Receitas</h5>
        <p class="card-title fs-4 fw-lighter"><span class="fs-6">R$ </span> <?php echo number_format($total_incomes, 2, ',', '.') ?></p>
      </div>
    </div>
    <div class="card alert alert-danger w-100 m-0 py-0">
      <div class="card-body">
        <h5 class="card-title">Despesas</h5>
        <p class="card-title fs-4 fw-lighter"><span class="fs-6">R$ </span> <?php echo number_format($total_expenses, 2, ',', '.') ?></p>
      </div>
    </div>
    <div class="card alert alert-dark w-100 m-0 py-0">
      <div class="card-body">
        <h5 class="card-title">Saldo</h5>
        <p class="card-title fs-4 fw-lighter"><span class="fs-6">R$ </span> <?php echo number_format($total_incomes - $total_expenses, 2, ',', '.') ?></p>
      </div>
    </div>
  </div>

</section>

<!-- modal nova receita -->
<div class="modal fade" id="modal_income" tabindex="-1" role="dialog" aria-labelledby="modal_label_income" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal_label_income"><span class="modal_transaction_title">Nova</span> Receita</h5>
      </div>
      <div class="modal-body">
        <form action="<?php echo 'panel/transactions/' . $data['user_id'] ?>" method="POST" id="add_income">
          <input type="hidden" name="edit_income" class="transaction_id">
          <input type="hidden" name="add_income" value="1">
          <fieldset class="form-group">
            <div class="form-floating mb-3 accounts_select">
              <select class="form-select" id="accounts_select_income" name="transaction_account" aria-label="accounts_select_income" required>
                <?php foreach ($data['accounts'] as $value) : ?>
                  <option value=<?php echo $value['id'] ?>><?php echo $value['name'] ?></option>
                <?php endforeach; ?>
              </select>
              <label for="accounts_select_income">Selecione a conta</label>
            </div>

            <div class="input-group mb-3">
              <div class="form-floating transaction_description">
                <input class="form-control" type="text" name="transaction_description" placeholder="transaction_description" id="transaction_description_income" required autocomplete="off">
                <label for="transaction_description_income" class="small">Descrição</label>
              </div>
            </div>

            <div class="form-floating mb-3 categories_select">
              <select class="form-select" id="categories_select_income" name="transaction_category" aria-label="categories_select_income" required>
                <?php foreach ($data['categories'] as $value) : ?>
                  <option value=<?php echo $value['id'] ?>><?php echo $value['name'] ?></option>
                <?php endforeach; ?>
              </select>
              <label for="categories_select_income">Selecione a categoria</label>
            </div>

            <div class="input-group mb-3">
              <div class="form-floating transaction_amount">
                <input class="form-control" type="number" step="0.01" name="transaction_amount" placeholder="transaction_amount" id="transaction_amount_income" required autocomplete="off">
                <label for="transaction_amount_income" class="small">Valor</label>
              </div>
            </div>

            <div class="input-group mb-3">
              <div class="form-floating transaction_date">
                <input class="form-control" type="date" name="transaction_date" placeholder="transaction_date" id="transaction_date_income" required autocomplete="off">
                <label for="transaction_date_income" class="small">Data</label>
              </div>
            </div>
          </fieldset>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success" form="add_income" id="btn_modal_transaction_income"><span class="modal_button_ok_title">Adicionar</span></button>
      </div>
    </div>
  </div>
</div>

<!-- modal nova despesa -->
<div class="modal fade" id="modal_expense" tabindex="-1" role="dialog" aria-labelledby="modal_label_expense" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal_label_expense"><span class="modal_transaction_title">Nova</span> Despesa</h5>
      </div>
      <div class="modal-body">
        <form action="<?php echo 'panel/transactions/' . $data['user_id'] ?>" method="POST" id="add_expense">
          <input type="hidden" name="edit_expense" class="transaction_id">
          <input type="hidden" name="add_expense" value="1">
          <fieldset class="form-group">

            <div class="form-floating mb-3 accounts_select">
              <select class="form-select" id="accounts_select_expense" name="transaction_account" aria-label="accounts_select_expense">
                <?php foreach ($data['accounts'] as $value) : ?>
                  <option value=<?php echo $value['id'] ?>><?php echo $value['name'] ?></option>
                <?php endforeach; ?>
              </select>
              <label for="accounts_select_expense">Selecione a conta</label>
            </div>

            <div class="input-group mb-3">
              <div class="form-floating transaction_description">
                <input class="form-control" type="text" name="transaction_description" placeholder="transaction_description" id="transaction_description_expense" required autocomplete="off">
                <label for="transaction_description_expense" class="small">Descrição</label>
              </div>
            </div>

            <div class="form-floating mb-3 categories_select">
              <select class="form-select" id="categories_select_expense" name="transaction_category" aria-label="categories_select_expense">
                <?php foreach ($data['categories'] as $value) : ?>
                  <option value=<?php echo $value['id'] ?>><?php echo $value['name'] ?></option>
                <?php endforeach; ?>
              </select>
              <label for="categories_select_expense">Selecione a categoria</label>
            </div>

            <div class="input-group mb-3">
              <div class="form-floating transaction_amount">
                <input class="form-control" type="number" step="0.01" name="transaction_amount" placeholder="transaction_amount" id="transaction_amount_expense" required autocomplete="off">
                <label for="transaction_amount_expense" class="small">Valor</label>
              </div>
            </div>

            <div class="input-group mb-3 transaction_date">
              <div class="form-floating">
                <input class="form-control" type="date" name="transaction_date" placeholder="transaction_date" id="transaction_date_expense" required autocomplete="off">
                <label for="transaction_date_expense" class="small">Data</label>
              </div>
            </div>
          </fieldset>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-danger" form="add_expense" id="btn_modal_transaction_expense"><span class="modal_button_ok_title">Adicionar</span></button>
      </div>
    </div>
  </div>
</div>

<script src="/js/transactions.js"></script>