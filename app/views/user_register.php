<section class="container-fluid">
  <div class="row h-100 align-items-center px-4 col-md-8 col-lg-6 col-xl-4 mx-auto">
    <div>
      <form class="border rounded p-4 shadow-sm" action="registration" method="POST">
        <h1 class="text-center h4 mb-4">Cadastro de Usuário</h1>
        <fieldset class="form-group">
          <div class="input-group mb-3">
            <span class="input-group-text">@</span>
            <div class="form-floating">
              <input class="form-control" type="text" name="user_name" id="user_name" placeholder="username" required autocomplete="off" autofocus value=<?php echo $_POST['user_name'] ?? '' ?>>
              <label for="user_name" class="small">Nome completo</label>
            </div>
          </div>
          <?php if (isset($message['error_register'])) { ?>
            <div class="alert alert-warning text-center small" id="password-error">
              <?php echo $message['error_register'] ?>
            </div>
          <?php } ?>
          <div class="input-group mb-3">
            <span class="input-group-text">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16">
                <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z" />
              </svg>
            </span>
            <div class="form-floating">
              <input class="form-control" type="email" name="user_email" id="user_email" placeholder="email@email.com" required autocomplete="off" value=<?php echo $_POST['user_email'] ?? '' ?>>
              <label for="user_email" class="small">Endereço de email</label>
            </div>
          </div>
        </fieldset>
        <fieldset class="form-group">
          <div class="input-group mb-3">
            <div class="form-floating">
              <input class="form-control" type="password" name="user_password" placeholder="user_password" id="user_password" required autocomplete="off">
              <label for="user_password" class="small">Digite sua senha</label>
            </div>
          </div>
          <div class="input-group mb-2">
            <div class="form-floating">
              <input class="form-control mb-2" type="password" name="confirm_user_password" placeholder="confirm_user_password" id="confirm_user_password" required autocomplete="off">
              <label for="confirm_user_password" class="small">Confirme sua Senha</label>
            </div>
          </div>
        </fieldset>
        <div class="col-12 text-center">
          <button class="w-100 btn btn-primary" type="submit">Cadastrar</button>
        </div>
      </form>
      <?php if (isset($message['error_password'])) { ?>
        <div class="alert alert-danger text-center small" id="password-error">
          <?php echo $message['error_password'] ?>
        </div>
      <?php } ?>
    </div>
  </div>
</section>

<script src="../js/user_register.js"></script>