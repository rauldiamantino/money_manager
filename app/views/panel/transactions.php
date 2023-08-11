<section class="container mt-4">
  <h1 class="mb-4">Geral</h1>

  <div class="mb-2 d-flex gap-3">
    <a href="" class="link-success link-offset-2 link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover" data-toggle="modal" data-target="#modal_receita">
      <i class="bi bi-file-earmark-plus"></i>
      Nova Receita
    </a>
    <a href="" class="link-danger link-offset-2 link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover" data-toggle="modal" data-target="#modal_despesa">
      <i class="bi bi-file-earmark-plus"></i>
      Nova Despesa
    </a>
  </div>

  <table class="table table-dark table-striped-columns">
    <thead>
      <tr>
        <th>Descrição</th>
        <th>Conta</th>
        <th>Categoria</th>
        <th>Valor</th>
        <th>Data</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($data['transactions'] as $value) : ?>
        <tr>
          <td><?php echo $value['description']; ?></td>
          <td><?php echo $value['account_name']; ?></td>
          <td><?php echo $value['category_name']; ?></td>
          <td><?php echo 'R$ ' . number_format($value['amount'], 2, ',', '.'); ?></td>
          <td><?php echo $value['date']; ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>

<!-- modal nova receita -->
<div class="modal fade" id="modal_receita" tabindex="-1" role="dialog" aria-labelledby="modal_receita_label" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal_receita_label">Nova Receita</h5>
      </div>
      <div class="modal-body">
        <form action="<?php echo BASE . '/panel/transactions/' . $data['user_id'] ?>" method="POST" id="add_income">
          <fieldset class="form-group">

            <div class="input-group mb-3">
              <div class="form-floating">
                <input class="form-control" type="text" name="transaction_account" placeholder="transaction_account" id="transaction_account" required autocomplete="off">
                <label for="transaction_account" class="small">Conta</label>
              </div>
            </div>

            <div class="input-group mb-3">
              <div class="form-floating">
                <input class="form-control" type="text" name="transaction_description" placeholder="transaction_description" id="transaction_description" required autocomplete="off">
                <label for="transaction_description" class="small">Descrição</label>
              </div>
            </div>

            <div class="input-group mb-3">
              <div class="form-floating">
                <input class="form-control" type="text" name="transaction_category" placeholder="transaction_category" id="transaction_category" required autocomplete="off">
                <label for="transaction_category" class="small">Categoria</label>
              </div>
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
<div class="modal fade" id="modal_despesa" tabindex="-1" role="dialog" aria-labelledby="modal_despesa_label" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal_despesa_label">Nova Despesa</h5>
      </div>
      <div class="modal-body">
        <form action="#" method="POST" id="add_expense">
          <fieldset class="form-group">

            <div class="input-group mb-3">
              <div class="form-floating">
                <input class="form-control" type="text" name="transaction_account" placeholder="transaction_account" id="transaction_account" required autocomplete="off">
                <label for="transaction_account" class="small">Conta</label>
              </div>
            </div>

            <div class="input-group mb-3">
              <div class="form-floating">
                <input class="form-control" type="text" name="transaction_description" placeholder="transaction_description" id="transaction_description" required autocomplete="off">
                <label for="transaction_description" class="small">Descrição</label>
              </div>
            </div>

            <div class="input-group mb-3">
              <div class="form-floating">
                <input class="form-control" type="text" name="transaction_category" placeholder="transaction_category" id="transaction_category" required autocomplete="off">
                <label for="transaction_category" class="small">Categoria</label>
              </div>
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