<link rel="stylesheet" href="/css/categories.css">

<section class="container mt-4">
  <h1 class="mb-4 text-center">Categorias</h1>

  <div class="col-md-8 col-lg-6 col-xl-4 mx-auto">
    <div class="mb-2 d-flex gap-3">
      <a href="" class="link_add_category link-success link-offset-2 link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover" data-toggle="modal" data-target="#modal_category">
        <i class="bi bi-file-earmark-plus"></i>
        Nova Categoria
      </a>
    </div>

    <table class="table table-hover">
      <thead>
        <tr>
          <th>Name</th>
          <th colspan="3">Ação</th>
        </tr>
      </thead>
      <tbody class="table-group-divider">
        <?php foreach ($data['categories'] as $value) : ?>
          <tr>
            <td><?php echo $value['name']; ?></td>
            <td class="px-0 col-10-css lh-1">
              <a href="" class="text-black link_edit_category"
                data-toggle="modal"
                data-target="#modal_category"
                data-category_id="<?php echo $value['id']; ?>"
                data-category_name="<?php echo $value['name']; ?>">
                <i class="bi bi-pencil-square fs-5"></i>
              </a>
            </td>
            <td class="px-0 col-10-css">
              <form action="<?php echo 'categories/' . $data['user_id'] ?>" method="POST" id="<?php echo 'delete-' . $value['id']?>">
                <input type="hidden" name="delete_category_id" value="<?php echo $value['id']; ?>">

                <button class="p-0 lh-1 border-0 bg-transparent text-danger" form="<?php echo 'delete-' . $value['id']?>"><i class="bi bi-x-circle fs-5"></i></button>
              </form>
            </td>
            <td class="px-0 col-10-css"></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <?php if (isset($data['message']['error_category'])) { ?>
      <div class="alert alert-danger text-center small p-1 rounded-0" id="alert_create_category"><?php echo $data['message']['error_category'] ?></div>
    <?php } ?>

    <?php if (isset($data['message']['success'])) { ?>
      <div class="d-none alert alert-success text-center small p-1 rounded-0" id="alert_create_category"><?php echo $data['message']['success'] ?></div>
    <?php } ?>
  </div>
</section>

<!-- modal nova categoria -->
<div class="modal fade" id="modal_category" tabindex="-1" role="dialog" aria-labelledby="modal_category_label" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal_category_label"><span class="modal_category_title">Nova </span>Categoria</h5>
      </div>
      <div class="modal-body">
        <form action="<?php echo 'categories/' . $data['user_id'] ?>" method="POST" id="add_category">
          <input type="hidden" name="category_id" class="category_id">
          <fieldset class="form-group">
            <div class="input-group mb-3">
              <div class="form-floating">
                <input class="form-control category_name" type="text" name="category_name" placeholder="category" id="category" required autocomplete="off">
                <label for="category" class="small">Categoria</label>
              </div>
            </div>
          </fieldset>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success modal_button_ok_title" form="add_category">Adicionar</button>
      </div>
    </div>
  </div>
</div>
<script src="/js/categories.js"></script>