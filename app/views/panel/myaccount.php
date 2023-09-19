<section class="container mt-4">
  <h1 class="mb-4 text-center">Minha Conta</h1>

  <form action="<?php echo 'myaccount/' . $data['user_id']?>" method="POST" class="col-md-8 col-lg-6 col-xl-4 mx-auto">
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

      <div class="form-floating mb-3">
        <input class="form-control" type="text" name="user_first_name" id="user_first_name" placeholder="username" autocomplete="off" value="<?php echo $data['myaccount']['user_first_name'] ?>">
        <label for="user_first_name" class="small">Nome</label>
      </div>
      <div class="form-floating mb-3">
        <input class="form-control" type="text" name="user_last_name" id="user_last_name" placeholder="username" autocomplete="off" value="<?php echo $data['myaccount']['user_last_name'] ?>">
        <label for="user_last_name" class="small">Sobrenome</label>
      </div>
      <div class="input-group mb-3">
        <span class="input-group-text">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16">
            <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z" />
          </svg>
        </span>
        <div class="form-floating">
          <input class="form-control" type="email" name="user_email" id="user_email" placeholder="email@email.com" autocomplete="off" value="<?php echo $data['myaccount']['user_email'] ?>">
          <label for="user_email" class="small">Endereço de email</label>
        </div>
      </div>
    </fieldset>
    <fieldset class="form-group">
      <div class="input-group mb-3">
        <div class="form-floating">
          <input class="form-control" type="password" name="password" placeholder="password" id="password" autocomplete="off">
          <label for="password" class="small">Senha</label>
        </div>
      </div>
    </fieldset>
    <div class="col-12 text-center">
      <button class="w-100 btn btn-primary" type="submit">Editar</button>
    </div>
  </form>
</section>