<?php
require_once '../app/models/CategoriesModel.php';
require_once '../app/controllers/PanelController.php';

class CategoriesController extends PanelController
{
  public $categoriesModel;

  // Exibe todas as categorias
  public function categories($userId)
  {
    $this->userId = $userId;
    $this->categoriesModel = new CategoriesModel();

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

    // Adiciona uma nova categoria para o usuário
    if ($category['name'] and empty($category['id'])) {
      $message = $this->createCategory($category);
    }

    // Edita uma categoria já existente
    if ($category['id']) {
      $message = $this->editCategory($category);
    }

    // Apaga uma categoria
    if ($category['delete']) {
      $message = $this->deleteCategory($category);
    }

    if (empty($message)) {
      Logger::log(['method' => 'PanelController->categories', 'result' => $category ]);
    }

    // Prepara conteúdo para a View
    $this->actionRoute = 'categories/' . $this->userId;
    $categories = $this->categoriesModel->getCategories($this->userId);
    $this->activeTab = 'categories';

    // View e conteúdo para o menu de navegação
    $navViewName = 'panel/templates/nav';
    $navViewContent = [
      'user_id' => $this->userId,
      'active_tab' => $this->activeTab,
      'action_route' => $this->actionRoute,
      'user_first_name' => $this->userFirstName,
      'user_last_name' => $this->userLastName,
    ];

    // View e conteúdo para a página de categorias
    $categoriesViewName = 'panel/categories';
    $categoriesViewContent = [
      'categories' => $categories, 
      'user_id' => $this->userId,
      'message' => $message,
    ];

    return [ $navViewName => $navViewContent, $categoriesViewName => $categoriesViewContent ];
  }

  // Cria uma nova categoria
  public function createCategory($category)
  {

    // Verifica se a categoria existe
    $categoryExists = $this->categoriesModel->categoryExists($this->userId, ['name' => $category['name'] ]);

    if ($categoryExists) {
      return ['error_category' => 'Conta já existe'];
    }

    // Cria a categoria
    $createCategory = $this->categoriesModel->createCategory($this->userId, $category['name']);

    if (empty($createCategory)) {
      return ['error_category' => 'Erro ao cadastrar categoria'];
    }

    return [];
  }

  // Edita uma categoria já existente
  public function editCategory($category)
  {

    // Verifica se a categoria existe
    $categoryExists = $this->categoriesModel->categoryExists($this->userId, ['id' => $category['id'] ]);

    if (empty($categoryExists)) {
      return ['error_category' => 'Conta inexistente'];
    }

    // Edita a categoria
    $editCategory = $this->categoriesModel->editCategory($this->userId, ['id' => $category['id'], 'name' => $category['name'] ]);

    if (empty($editCategory)) {
      return ['error_category' => 'Erro ao editar categoria'];
    }

    return [];
  }

  // Apaga uma categoria do banco de dados
  public function deleteCategory($category)
  {

    // Não apaga categoria em uso
    $categoryInUse = $this->categoriesModel->categoryInUse($this->userId, $category['delete']);

    if ($categoryInUse) {
      return ['error_category' => 'Conta em uso não pode ser apagada'];
    }

    // Apaga a categoria
    $deleteCategory = $this->categoriesModel->deleteCategory($this->userId, $category['delete']);

    if (empty($deleteCategory)) {
      return ['error_category' => 'Erro ao apagar categoria'];
    }

    return [];
  }
}