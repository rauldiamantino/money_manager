<?php
require_once '../app/models/CategoriesModel.php';
require_once '../app/controllers/PanelController.php';

class CategoriesController extends PanelController
{
  public $categoriesModel;

  // Exibe todas as categorias
  public function categories($userId)
  {
    // Valida se o usuário está logado
    if (parent::checkSession($userId) or parent::checkLogout($userId)) {
      Logger::log(['method' => 'PanelController->categories', 'result' => 'Usuario Desconectado'], 'alert');
    }

    $category = [
      'id' => $_POST['category_id'] ?? 0,
      'name' => $_POST['category_name'] ?? '',
      'delete' => $_POST['delete_category_id'] ?? 0
    ];

    $message = [];
    $this->categoriesModel = new CategoriesModel();

    // Adiciona uma nova categoria para o usuário
    if ($category['name'] and empty($category['id'])) {
      $message = $this->createCategory($userId, $category);
    }

    // Edita uma categoria já existente
    if ($category['id']) {
      $message = $this->editCategory($userId, $category);
    }

    // Apaga uma categoria
    if ($category['delete']) {
      $message = $this->deleteCategory($userId, $category);
    }

    if (empty($message)) {
      Logger::log(['method' => 'PanelController->categories', 'result' => $category ]);
    }

    // Renderiza menu e conteúdo
    $user = $this->categoriesModel->getUser('', $userId);
    $categories = $this->categoriesModel->getCategories($userId);

    $renderView = [
      'panel/templates/nav' => [
        'user_id' => $userId,
        'active_tab' => 'categories',
        'action_route' => 'categories/' . $userId,
        'user_first_name' => $user[0]['first_name'],
        'user_last_name' => $user[0]['last_name'],
      ],
      'panel/categories' => [
        'categories' => $categories,
        'user_id' => $userId,
        'message' => $message,
      ],
    ];

    return $renderView;
  }

  // Cria uma nova categoria
  private function createCategory($userId, $category)
  {
    // Verifica se a categoria existe
    $categoryExists = $this->categoriesModel->categoryExists($userId, ['name' => $category['name'] ]);

    if ($categoryExists) {
      return ['error_category' => 'Conta já existe'];
    }

    // Cria a categoria
    $createCategory = $this->categoriesModel->createCategory($userId, $category['name']);

    if (empty($createCategory)) {
      return ['error_category' => 'Erro ao cadastrar categoria'];
    }

    return [];
  }

  // Edita uma categoria já existente
  private function editCategory($userId, $category)
  {
    // Verifica se a categoria existe
    $categoryExists = $this->categoriesModel->categoryExists($userId, ['id' => $category['id'] ]);

    if (empty($categoryExists)) {
      return ['error_category' => 'Conta inexistente'];
    }

    // Edita a categoria
    $editCategory = $this->categoriesModel->editCategory($userId, ['id' => $category['id'], 'name' => $category['name'] ]);

    if (empty($editCategory)) {
      return ['error_category' => 'Erro ao editar categoria'];
    }

    return [];
  }

  // Apaga uma categoria do banco de dados
  private function deleteCategory($userId, $category)
  {
    // Não apaga categoria em uso
    $categoryInUse = $this->categoriesModel->categoryInUse($userId, $category['delete']);

    if ($categoryInUse) {
      return ['error_category' => 'Conta em uso não pode ser apagada'];
    }

    // Apaga a categoria
    $deleteCategory = $this->categoriesModel->deleteCategory($userId, $category['delete']);

    if (empty($deleteCategory)) {
      return ['error_category' => 'Erro ao apagar categoria'];
    }

    return [];
  }
}