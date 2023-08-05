<ul class="nav nav-tabs justify-content-end pe-2">
  <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle py-3" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">
      <?php echo $user_first_name . ' ' . $user_last_name; ?>
    </a>
    <ul class="dropdown-menu p-0">
      <li class="text-center">
        <a href="#" class="dropdown-item py-3">Minha conta</a>
      </li>
      <li class="nav-item">
        <form action="../panel/display" method="POST" id="logout" class="d-none">
          <input type="hidden" name="logout" value=1>
        </form>
        <button class="dropdown-item text-center py-3" form="logout">Sair</button>
      </li>
    </ul>
  </li>

</ul>

<section class="container mt-4">
  <h1>Painel</h1>
</section>