<section class="container mt-4">
  <h1 class="mb-4">Geral</h1>

  <button type="button" class="btn btn-primary rounded-0 py-1 mb-2" data-toggle="modal" data-target="#modalExemplo">
    Adicionar transação
  </button>

  <table class="table table-dark table-striped-columns">
    <thead>
      <tr>
        <th>ID</th>
        <th>Descrição</th>
        <th>Conta</th>
        <th>Categoria</th>
        <th>Valor</th>
        <th>Data</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($transactions as $value) : ?>
        <tr>
          <td><?php echo $value['id']; ?></td>
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

<!-- Modal -->
<div class="modal fade" id="modalExemplo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Adicionar transação</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
        <button type="button" class="btn btn-primary">Salvar mudanças</button>
      </div>
    </div>
  </div>
</div>