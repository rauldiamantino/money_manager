<ul class="nav nav-tabs justify-content-end pe-2 pt-2">
  <li class="nav-item">
    <a class="nav-link py-3 text-black <?php if ($data['active_tab'] === 'overview') echo 'active'; ?>" aria-current="page" href="<?php echo BASE . '/panel/display' ?>">Visão geral</a>
  </li>
  <li class="nav-item">
    <a class="nav-link py-3 text-black <?php if ($data['active_tab'] === 'transactions') echo 'active'; ?>" aria-current="page" href="<?php echo BASE . '/panel/transactions/' . $data['user_id'] ?>">Transações</a>
  </li>
  <li class="nav-item">
    <a class="nav-link py-3 text-black <?php if ($data['active_tab'] === 'accounts') echo 'active'; ?>" aria-current="page" href="<?php echo BASE . '/panel/accounts/' . $data['user_id'] ?>">Contas</a>
  </li>
  <li class="nav-item">
    <a class="nav-link py-3 text-black <?php if ($data['active_tab'] === 'categories') echo 'active'; ?>" aria-current="page" href="<?php echo BASE . '/panel/categories/' . $data['user_id'] ?>">Categorias</a>
  </li>
  <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle py-3 text-black" data-bs-toggle="dropdown" role="button" aria-expanded="false">
      <?php echo $data['user_first_name'] . ' ' . $data['user_last_name']; ?>
    </a>
    <ul class="dropdown-menu p-0 w-100">
      <li class="text-center">
        <a href="<?php echo BASE . '/panel/myaccount/' . $data['user_id'] ?>" class="dropdown-item py-3">Minha conta</a>
      </li>
      <li class="nav-item">
        <form action="<?php echo $data['action_route']; ?>" method="POST" id="logout" class="d-none">
          <input type="hidden" name="logout" value=1>
        </form>
        <button class="dropdown-item text-center py-3" form="logout">Sair</button>
      </li>
    </ul>
  </li>
</ul>