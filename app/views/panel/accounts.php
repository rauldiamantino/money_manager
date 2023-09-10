<link rel="stylesheet" href="/css/accounts.css">

<section class="container mt-4">
  <h1 class="mb-4">Contas</h1>

  <div class="mb-2 d-flex gap-3 position-relative">
    <a href="" class="link-success link-offset-2 link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover" data-toggle="modal" data-target="#modal_account">
      <i class="bi bi-file-earmark-plus"></i>
      Nova Conta
    </a>

    <?php if (isset($data['message']['error_account'])) { ?>
      <div class="position-absolute top-50 start-50 translate-middle col-md-8 col-lg-6 col-xl-4 mx-auto alert alert-danger text-center small p-1 rounded-0" id="alert_create_account"><?php echo $data['message']['error_account'] ?></div>
    <?php } ?>

    <?php if (isset($data['message']['success'])) { ?>
      <div class="position-absolute top-50 start-50 translate-middle col-md-8 col-lg-6 col-xl-4 mx-auto alert alert-success text-center small p-1 rounded-0" id="alert_create_account"><?php echo $data['message']['success'] ?></div>
    <?php } ?>
  </div>
  <table class="table table-hover">
    <thead>
      <tr>
        <th>Name</th>
        <th colspan="3">Ação</th>
      </tr>
    </thead>
    <tbody class="table-group-divider">
      <?php foreach ($data['accounts'] as $value) : ?>
        <tr>
          <td><?php echo $value['name']; ?></td>
          <td class="px-0 col-10-css">A</td>
          <td class="px-0 col-10-css">B</td>
          <td class="px-0 col-10-css">C</td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>

<!-- modal nova conta -->
<div class="modal fade" id="modal_account" tabindex="-1" role="dialog" aria-labelledby="modal_account_label" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal_account_label">Nova Conta</h5>
      </div>
      <div class="modal-body">
        <form action="<?php echo 'panel/accounts/' . $data['user_id'] ?>" method="POST" id="add_income">
          <fieldset class="form-group">
            <div class="input-group mb-3">
              <div class="form-floating">
                <input class="form-control" type="text" name="account" placeholder="account" id="account" required autocomplete="off">
                <label for="account" class="small">Conta</label>
              </div>
            </div>
          </fieldset>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success" form="add_income">Criar Conta</button>
      </div>
    </div>
  </div>
</div>

<script src="/js/accounts.js"></script>