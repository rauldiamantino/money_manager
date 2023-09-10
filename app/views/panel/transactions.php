<section class="container mt-4">
  <h1 class="mb-4">Geral</h1>

  <div class="mb-2 d-flex gap-3">
    <a href="" class="link-success link-offset-2 link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover link_add_transaction" data-toggle="modal" data-target="#modal_income" id="link_add_income">
      <i class="bi bi-file-earmark-plus"></i>
      Nova Receita
    </a>
    <a href="" class="link-danger link-offset-2 link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover link_add_transaction" data-toggle="modal" data-target="#modal_expense" id="link_add_expense">
      <i class="bi bi-file-earmark-plus"></i>
      Nova Despesa
    </a>
  </div>

  <?php if (isset($data['message']['error_income'])) { ?>
    <div class="d-none" id="alert_add_transaction"><?php echo $data['message']['error_income'] ?></div>
  <?php } ?>

  <?php if (isset($data['message']['success'])) { ?>
    <div class="d-none" id="alert_add_transaction"><?php echo $data['message']['success'] ?></div>
  <?php } ?>

  <table class="table table-hover">
    <thead>
      <tr>
        <th>Descrição</th>
        <th>Conta</th>
        <th>Categoria</th>
        <th>Valor</th>
        <th>Data</th>
        <th>Ação</th>
      </tr>
    </thead>
    <tbody class="table-group-divider">
      <?php foreach ($data['transactions'] as $value) : ?>
        <tr>
          <td><?php echo $value['description']; ?></td>
          <td><?php echo $value['account_name']; ?></td>
          <td><?php echo $value['category_name']; ?></td>
          <td class="<?php echo $value['amount'] <= 0 ? 'text-danger' : 'text-success'; ?>">
            <?php echo 'R$ ' . number_format($value['amount'], 2, ',', '.'); ?>
          </td>
          <td><?php echo date('d/m/Y', strtotime($value['date'])); ?></td>
          <td>
            <div class="d-flex gap-3 lh-1">

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

              <form action="<?php echo 'panel/transactions/' . $data['user_id'] ?>" method="POST" id="<?php echo 'delete-' . $value['id'] . '-' . $value['type']?>">
                <input type="hidden" name="delete_transaction">
                <input type="hidden" name="delete_transaction_id" value="<?php echo $value['id']; ?>">
                <input type="hidden" name="delete_transaction_type" value="<?php echo $value['type']; ?>">

                <button class="p-0 lh-1 border-0 bg-transparent text-danger" form="<?php echo 'delete-' . $value['id'] . '-' . $value['type']?>"><i class="bi bi-x-circle fs-5"></i></button>
              </form>

              <form action="<?php echo 'panel/transactions/' . $data['user_id'] ?>" method="POST" id="<?php echo 'edit-' . $value['id'] . '-' . $value['type']?>">
                <input type="hidden" name="edit_transaction_status">
                <input type="hidden" name="edit_transaction_id" value="<?php echo $value['id']; ?>">
                <input type="hidden" name="edit_transaction_type" value="<?php echo $value['type']; ?>">

                <?php if ($value['status'] == "0") { ?>
                  <input type="hidden" name="edit_transaction_status" value="1">
                  <button class="p-0 lh-1 border-0 bg-transparent text-secondary" form="<?php echo 'edit-' . $value['id'] . '-' . $value['type']?>"><i class="bi bi-check-circle fs-5"></i></button>
                <?php } ?>

                <?php if ($value['status'] == "1") { ?>
                  <input type="hidden" name="edit_transaction_status" value="0">
                  <button class="p-0 lh-1 border-0 bg-transparent text-success" form="<?php echo 'edit-' . $value['id'] . '-' . $value['type']?>"><i class="bi bi-check-circle-fill fs-5"></i></button>
                <?php } ?>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
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
          <input type="hidden" name="add_income" id="transaction_id_income" class="transaction_id">
          <fieldset class="form-group">

            <div class="form-floating mb-3 accounts_select">
              <select class="form-select" id="accounts_select_income" name="transaction_account" aria-label="accounts_select_income">
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
              <select class="form-select" id="categories_select_income" name="transaction_category" aria-label="categories_select_income">
                <?php foreach ($data['categories'] as $value) : ?>
                  <option value=<?php echo $value['id'] ?>><?php echo $value['name'] ?></option>
                <?php endforeach; ?>
              </select>
              <label for="categories_select_income">Selecione a categoria</label>
            </div>

            <div class="input-group mb-3">
              <div class="form-floating transaction_amount">
                <input class="form-control" type="number" name="transaction_amount" placeholder="transaction_amount" id="transaction_amount_income" required autocomplete="off">
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
          <input type="hidden" name="add_expense" id="transaction_id_expense" class="transaction_id">
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
                <input class="form-control" type="number" name="transaction_amount" placeholder="transaction_amount" id="transaction_amount_expense" required autocomplete="off">
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