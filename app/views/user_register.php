<section class="container">  

  <pre>
    <?php print_r($register) ?>
  </pre>

  <h1 class="my-5 text-center">Cadastro de Usuário</h1>
  <form class="border rounded col-lg-6 mx-auto p-4" action="registration" method="POST">
      <fieldset class="form-group mb-4">
          <legend>Informações do Usuário</legend>
          <label for="name_user">Nome:</label>
          <input class="form-control mb-2" type="text" name="name_user" id="name_user" placeholder="Nome">
          <label for="email_user">Email:</label>
          <input class="form-control" type="text" name="email_user" id="email_user" placeholder="Email">
      </fieldset>
      <fieldset class="form-group">
          <legend>Informações de Senha</legend>
          <label for="password_user">Senha</label>
          <input class="form-control mb-2" type="password" name="password_user" id="password_user" required>
          <label for="confirm_password_user">Confirme sua Senha</label>
          <input class="form-control mb-2" type="password" name="confirm_password_user" id="confirm_password_user" required>
      </fieldset>
      <div class="col-12">
          <button class="btn btn-primary mt-2" type="submit">Cadastrar</button>
      </div>
  </form>
</section>
