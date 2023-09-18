<?php
require_once 'controllers/ErrorController.php';
require_once '../app/helpers/ViewRenderes.php';

class Router
{
  public $uri;
  public $parts;
  public $result;

  public function __construct()
  {
    // Divide a URI e remove partes vazias ou nulas
    $this->uri = $_SERVER['REQUEST_URI'];
    $this->parts = explode('/', $this->uri);
    $this->parts = array_filter($this->parts);

    $this->handleRoutes();
  }

  // Recupera a rota e chama o controlador
  private function handleRoutes()
  {
    $params = [];
    $methodName = '';

    // Rota base
    if ($this->uri == '/') {
      $controllerName = 'HomeController';
      $methodName = 'index';
    }

    // Remove controlador e método da rota, deixando somente parâmetros
    if (count($this->parts) > 0) {
      $controllerName = ucfirst(array_shift($this->parts)) . 'Controller';
      $getMethod = $this->routes($controllerName);

      // Se houver rota definida
      $methodName = $getMethod ? $getMethod : strtolower(array_shift($this->parts));
      $params = $this->parts ?? '';
    }

    // Certifique-se de que haja parâmetros suficientes para chamar o método
    if (count($params) < 1) {
      $params = [null]; // Adicione um valor padrão se nenhum parâmetro for fornecido
    }

    // Chama o controller e se não existir chama a página de erro
    $controllerFilePath = '../app/controllers/' . $controllerName . '.php';
    $controller = '';


    if (file_exists($controllerFilePath)) {
      require_once $controllerFilePath;
      $controller = new $controllerName();
    }

    if (method_exists($controller, $methodName)) {
      $this->result = call_user_func_array([$controller, $methodName], $params);
    }
    else {
      $this->result = ErrorController::not_found();
    }

    // Renderiza views
    foreach($this->result as $view => $content):
      ViewRenderer::render($view, $content);
    endforeach;
  }

  // Recebe um controlador e retorna o método associado
  public function routes($controllerName)
  {
    $methodName = '';

    if ($controllerName == 'TransactionsController') {
      $methodName = 'transactions';
    }

    if ($controllerName == 'CategoriesController') {
      $methodName = 'categories';
    }

    if ($controllerName == 'LoginController') {
      $methodName = 'start';
    }

    if ($controllerName == 'RegisterController') {
      $methodName = 'start';
    }

    return $methodName;
  }
}
