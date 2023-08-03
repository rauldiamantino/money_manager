<section class="container mt-4">
  <h1>Geral</h1>
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
    <?php foreach($finances as $value): ?>
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