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

    if ($this->uri == '/') {
      $controllerName = 'homeController';
      $methodName = 'index';
    }

    $params = [];

    // Remove controlador e método da rota, deixando somente parâmetros
    if (count($this->parts) > 0) {
      $controllerName = ucfirst(array_shift($this->parts)) . 'Controller';
      $methodName = strtolower(array_shift($this->parts));
      $params = $this->parts ?? '';
    }

    // Rotas pré definidas
    if ($this->uri == '/login') {
      $controllerName = 'LoginController';
      $methodName = 'start';
    }

    if ($this->uri == '/register') {
      $controllerName = 'RegisterController';
      $methodName = 'start';
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
}
