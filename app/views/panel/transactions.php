<section class="container mt-4">
  <h1 class="mb-4">Geral</h1>

  <div class="mb-2 d-flex gap-3">
    <a href="" class="link-success link-offset-2 link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover" data-toggle="modal" data-target="#modal_income" id="link_add_income">
      <i class="bi bi-file-earmark-plus"></i>
      Nova Receita
    </a>
    <a href="" class="link-danger link-offset-2 link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover" data-toggle="modal" data-target="#modal_expense" id="link_add_expense">
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
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>

<!-- modal nova receita -->
<div class="modal fade" id="modal_income" tabindex="-1" role="dialog" aria-labelledby="modal_income_label" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal_income_label">Nova Receita</h5>
      </div>
      <div class="modal-body">
        <form action="<?php echo BASE . '/panel/transactions/' . $data['user_id'] ?>" method="POST" id="add_income">
          <input type="hidden" name="add_income">
          <fieldset class="form-group">

            <div class="form-floating mb-3">
              <select class="form-select" id="accounts_select" name="transaction_account" aria-label="accounts_select">
                <?php foreach ($data['accounts'] as $value) : ?>
                  <option value=<?php echo $value['id'] ?>><?php echo $value['name'] ?></option>
                <?php endforeach; ?>
              </select>
              <label for="accounts_select">Selecione a conta</label>
            </div>

            <div class="input-group mb-3">
              <div class="form-floating">
                <input class="form-control" type="text" name="transaction_description" placeholder="transaction_description" id="transaction_description" required autocomplete="off">
                <label for="transaction_description" class="small">Descrição</label>
              </div>
            </div>


            <div class="form-floating mb-3">
              <select class="form-select" id="categories_select" name="transaction_category" aria-label="categories_select">
                <?php foreach ($data['categories'] as $value) : ?>
                  <option value=<?php echo $value['id'] ?>><?php echo $value['name'] ?></option>
                <?php endforeach; ?>
              </select>
              <label for="categories_select">Selecione a categoria</label>
            </div>

            <div class="input-group mb-3">
              <div class="form-floating">
                <input class="form-control" type="number" name="transaction_amount" placeholder="transaction_amount" id="transaction_amount" required autocomplete="off">
                <label for="transaction_amount" class="small">Valor</label>
              </div>
            </div>

            <div class="input-group mb-3">
              <div class="form-floating">
                <input class="form-control" type="date" name="transaction_date" placeholder="transaction_date" id="transaction_date" required autocomplete="off">
                <label for="transaction_date" class="small">Data</label>
              </div>
            </div>
          </fieldset>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success" form="add_income">Adicionar Receita</button>
      </div>
    </div>
  </div>
</div>

<!-- modal nova receita -->
<div class="modal fade" id="modal_expense" tabindex="-1" role="dialog" aria-labelledby="modal_expense_label" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal_expense_label">Nova Despesa</h5>
      </div>
      <div class="modal-body">
        <form action="#" method="POST" id="add_expense">
          <input type="hidden" name="add_expense">
          <fieldset class="form-group">

            <div class="form-floating mb-3">
              <select class="form-select" id="accounts_select" name="transaction_account" aria-label="accounts_select">
                <?php foreach ($data['accounts'] as $value) : ?>
                  <option value=<?php echo $value['id'] ?>><?php echo $value['name'] ?></option>
                <?php endforeach; ?>
              </select>
              <label for="accounts_select">Selecione a conta</label>
            </div>

            <div class="input-group mb-3">
              <div class="form-floating">
                <input class="form-control" type="text" name="transaction_description" placeholder="transaction_description" id="transaction_description" required autocomplete="off">
                <label for="transaction_description" class="small">Descrição</label>
              </div>
            </div>

            <div class="form-floating mb-3">
              <select class="form-select" id="categories_select" name="transaction_category" aria-label="categories_select">
                <?php foreach ($data['categories'] as $value) : ?>
                  <option value=<?php echo $value['id'] ?>><?php echo $value['name'] ?></option>
                <?php endforeach; ?>
              </select>
              <label for="categories_select">Selecione a categoria</label>
            </div>

            <div class="input-group mb-3">
              <div class="form-floating">
                <input class="form-control" type="number" name="transaction_amount" placeholder="transaction_amount" id="transaction_amount" required autocomplete="off">
                <label for="transaction_amount" class="small">Valor</label>
              </div>
            </div>

            <div class="input-group mb-3">
              <div class="form-floating">
                <input class="form-control" type="date" name="transaction_date" placeholder="transaction_date" id="transaction_date" required autocomplete="off">
                <label for="transaction_date" class="small">Data</label>
              </div>
            </div>
          </fieldset>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-danger" form="add_expense">Adicionar Despesa</button>
      </div>
    </div>
  </div>
</div>