<section class="container mt-4">
  <h1 class="mb-4">Categorias</h1>

  <div class="mb-2 d-flex gap-3">
    <a href="" class="link-success link-offset-2 link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover" data-toggle="modal" data-target="#modal_category">
      <i class="bi bi-file-earmark-plus"></i>
      Nova Categoria
    </a>
  </div>

  <?php if (isset($data['message']['error_category'])) { ?>
    <div class="alert alert-danger text-center small p-1 rounded-0" id="alert_category_error">
      <?php echo $data['message']['error_category'] ?>
    </div>
  <?php } ?>

  <?php if (isset($data['message']['success'])) { ?>
    <div class="alert alert-success text-center small p-1 rounded-0" id="alert_category_success">
      <?php echo $data['message']['success'] ?>
    </div>
  <?php } ?>

  <table class="table table-hover">
    <thead>
      <tr>
        <th>Name</th>
      </tr>
    </thead>
    <tbody class="table-group-divider">
      <?php foreach ($data['categories'] as $value) : ?>
        <tr>
          <td><?php echo $value['name']; ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>

<!-- modal nova categoria -->
<div class="modal fade" id="modal_category" tabindex="-1" role="dialog" aria-labelledby="modal_category_label" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal_category_label">Nova Categoria</h5>
      </div>
      <div class="modal-body">
        <form action="<?php echo BASE . '/panel/categories/' . $data['user_id'] ?>" method="POST" id="add_income">
          <fieldset class="form-group">
            <div class="input-group mb-3">
              <div class="form-floating">
                <input class="form-control" type="text" name="category" placeholder="category" id="category" required autocomplete="off">
                <label for="category" class="small">Categoria</label>
              </div>
            </div>
          </fieldset>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success" form="add_income">Criar Categoria</button>
      </div>
    </div>
  </div>
</div>