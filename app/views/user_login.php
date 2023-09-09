<section class="container-fluid">
  <div class="row h-100 align-items-center px-4 col-md-8 col-lg-6 col-xl-4 mx-auto">
    <div>
      <div class="text-center py-3">
        <a href="..">
          <svg xmlns="http://www.w3.org/2000/svg" width="70" height="70" fill="currentColor" class="bi bi-piggy-bank" viewBox="0 0 16 16">
            <path d="M5 6.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0zm1.138-1.496A6.613 6.613 0 0 1 7.964 4.5c.666 0 1.303.097 1.893.273a.5.5 0 0 0 .286-.958A7.602 7.602 0 0 0 7.964 3.5c-.734 0-1.441.103-2.102.292a.5.5 0 1 0 .276.962z" />
            <path fill-rule="evenodd" d="M7.964 1.527c-2.977 0-5.571 1.704-6.32 4.125h-.55A1 1 0 0 0 .11 6.824l.254 1.46a1.5 1.5 0 0 0 1.478 1.243h.263c.3.513.688.978 1.145 1.382l-.729 2.477a.5.5 0 0 0 .48.641h2a.5.5 0 0 0 .471-.332l.482-1.351c.635.173 1.31.267 2.011.267.707 0 1.388-.095 2.028-.272l.543 1.372a.5.5 0 0 0 .465.316h2a.5.5 0 0 0 .478-.645l-.761-2.506C13.81 9.895 14.5 8.559 14.5 7.069c0-.145-.007-.29-.02-.431.261-.11.508-.266.705-.444.315.306.815.306.815-.417 0 .223-.5.223-.461-.026a.95.95 0 0 0 .09-.255.7.7 0 0 0-.202-.645.58.58 0 0 0-.707-.098.735.735 0 0 0-.375.562c-.024.243.082.48.32.654a2.112 2.112 0 0 1-.259.153c-.534-2.664-3.284-4.595-6.442-4.595zM2.516 6.26c.455-2.066 2.667-3.733 5.448-3.733 3.146 0 5.536 2.114 5.536 4.542 0 1.254-.624 2.41-1.67 3.248a.5.5 0 0 0-.165.535l.66 2.175h-.985l-.59-1.487a.5.5 0 0 0-.629-.288c-.661.23-1.39.359-2.157.359a6.558 6.558 0 0 1-2.157-.359.5.5 0 0 0-.635.304l-.525 1.471h-.979l.633-2.15a.5.5 0 0 0-.17-.534 4.649 4.649 0 0 1-1.284-1.541.5.5 0 0 0-.446-.275h-.56a.5.5 0 0 1-.492-.414l-.254-1.46h.933a.5.5 0 0 0 .488-.393zm12.621-.857a.565.565 0 0 1-.098.21.704.704 0 0 1-.044-.025c-.146-.09-.157-.175-.152-.223a.236.236 0 0 1 .117-.173c.049-.027.08-.021.113.012a.202.202 0 0 1 .064.199z" />
          </svg>
        </a>
      </div>
      <form class="border rounded p-4 shadow-sm" action="users/login" method="POST">
        <h1 class="text-center h4 mb-4">Login</h1>
        <fieldset class="form-group">
          <?php if (isset($data['message']['error_login'])) { ?>
            <div class="alert alert-danger text-center small p-1 rounded-0" id="alert_login_error">
              <?php echo $data['message']['error_login'] ?>
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
        </fieldset>
        <div class="col-12 text-center">
          <button class="w-100 btn btn-primary" type="submit">Entrar</button>
        </div>
      </form>
      <p class="text-center"><a class="link-offset-2" href="registration">Crie sua conta Grátis!</a></p>
    </div>
  </div>
</section>

<script src="<?php echo BASE ?>/js/user.js"></script>