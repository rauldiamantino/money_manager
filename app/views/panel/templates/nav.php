<ul class="nav nav-tabs justify-content-end pe-2 pt-2">
  <li class="nav-item">
    <a class="nav-link py-3 text-black <?php if ($this->active_tab === 'overview') echo 'active'; ?>" aria-current="page" href="../display">Visão geral</a>
  </li>
  <li class="nav-item">
    <a class="nav-link py-3 text-black <?php if ($this->active_tab === 'transactions') echo 'active'; ?>" aria-current="page" href="transactions/<?php echo $this->user_id ?>">Transações</a>
  </li>
  <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle py-3 text-black" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">
      <?php echo $this->user_first_name . ' ' . $this->user_last_name; ?>
    </a>
    <ul class="dropdown-menu p-0 w-100">
      <li class="text-center">
        <a href="#" class="dropdown-item py-3">Minha conta</a>
      </li>
      <li class="nav-item">
        <form action="<?php echo $this->action_route; ?>" method="POST" id="logout" class="d-none">
          <input type="hidden" name="logout" value=1>
        </form>
        <button class="dropdown-item text-center py-3" form="logout">Sair</button>
      </li>
    </ul>
  </li>
</ul>