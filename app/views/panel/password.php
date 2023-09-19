<section class="container mt-4">
  <h1 class="mb-4 text-center">Alterar Senha</h1>

  <form action="<?php echo 'password/' . $data['user_id']?>" method="POST" class="col-md-8 col-lg-6 col-xl-4 mx-auto">
    <fieldset class="form-group">
      <?php if (isset($data['message']['success_update'])) { ?>
        <div class="alert alert-success text-center small p-1 rounded-0" id="alert_update">
          <?php echo $data['message']['success_update'] ?>
        </div>
      <?php } ?>
      <?php if (isset($data['message']['error_update'])) { ?>
        <div class="alert alert-danger text-center small p-1 rounded-0" id="alert_update">
          <?php echo $data['message']['error_update'] ?>
        </div>
      <?php } ?>
    </fieldset>
    <fieldset class="form-group">
      <div class="input-group mb-3">
        <div class="form-floating">
          <input class="form-control" type="password" name="password" placeholder="password" id="password" autocomplete="off">
          <label for="password" class="small">Senha atual</label>
        </div>
      </div>
      <div class="input-group mb-3">
        <div class="form-floating">
          <input class="form-control" type="password" name="user_new_password" placeholder="user_new_password" id="user_new_password" autocomplete="off">
          <label for="user_new_password" class="small">Nova senha</label>
        </div>
      </div>
      <div class="input-group mb-2">
        <div class="form-floating">
          <input class="form-control mb-2" type="password" name="user_confirm_new_password" placeholder="user_confirm_new_password" id="user_confirm_new_password" autocomplete="off">
          <label for="user_confirm_new_password" class="small">Confirme a nova Senha</label>
        </div>
      </div>
    </fieldset>
    <div class="col-12 text-center">
      <button class="w-100 btn btn-primary" type="submit">Editar</button>
    </div>
  </form>
</section>