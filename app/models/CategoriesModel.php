<?php
require_once '../app/models/PanelModel.php';

class CategoriesModel extends PanelModel
{

  // Cria uma categoria para o usuário
  public function createCategory($userId, $categoryName)
  {
    $databaseName = 'm_user_' . $userId;
    $sql = 'INSERT INTO categories (name) VALUES (:name);';
    $params = ['name' => $categoryName];

    $this->database->switchDatabase(($databaseName));
    $result = $this->database->insert($sql, $params);

    Logger::log(['method' => 'CategoriesModel->createCategory', 'result' => $result]);

    return $result;
  }

  // Edita uma categoria já existente
  public function editCategory($userId, $category)
  {
    $databaseName = 'm_user_' . $userId;
    $sql = 'UPDATE categories SET name = :name WHERE id = :id;';
    $params = ['id' => $category['id'], 'name' => $category['name']];

    $this->database->switchDatabase(($databaseName));
    $result = $this->database->insert($sql, $params);

    Logger::log(['method' => 'CategoriesModel->editCategory', 'result' => $result]);

    return $result;
  }

  // Apaga categoria
  public function deleteCategory($userId, $categoryId)
  {
    $databaseName = 'm_user_' . $userId;
    $sql = 'DELETE FROM categories WHERE id = :id;';
    $params = ['id' => $categoryId];

    $this->database->switchDatabase($databaseName);
    $result = $this->database->delete($sql, $params);

    Logger::log(['method' => 'CategoriesModel->deleteCategory', 'result' => $result]);

    return true;
  }

  // Verifica se a categoria está em uso em alguma transação
  public function categoryInUse($userId, $categoryId)
  {
    $databaseName = 'm_user_' . $userId;
    $sql = 'SELECT category_id, description FROM incomes WHERE category_id = :category_id
            UNION
            SELECT category_id, description FROM expenses WHERE category_id = :category_id';

    $params = ['category_id' => $categoryId];

    $this->database->switchDatabase($databaseName);
    $result = $this->database->select($sql, ['params' => $params, 'database_name' => $databaseName]);

    Logger::log(['method' => 'CategoriesModel->categoryInUse', 'result' => $result]);

    return $result;
  }

  // Verifica se a categoria já existe para o usuário
  public function categoryExists($user_id, $category)
  {
    $databaseName = 'm_user_' . $user_id;
    $paramWhere = array_key_first($category);

    $sql = 'SELECT * FROM categories WHERE ' . $paramWhere . ' = :' . $paramWhere;
    $params = [$paramWhere => reset($category)];

    $this->database->switchDatabase($databaseName);
    $result = $this->database->select($sql, ['params' => $params, 'database_name' => $databaseName]);

    Logger::log(['method' => 'CategoriesModel->categoryExists', 'result' => $result]);

    return $result;
  }
}