<?php
require_once 'controllers/ErrorController.php';

class Router
{
  private $base_uri;
  private $uri;
  private $parts;

  public function __construct()
  {
    // Ajuste da URL para permitir o projeto ser aberto em qualquer servidor
    $this->base_uri = str_replace('/public/', '', dirname($_SERVER['SCRIPT_NAME']));
    $this->uri = str_replace($this->base_uri, '', $_SERVER['REQUEST_URI']);

    // Divide a URI e remove partes vazias ou nulas
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

    if (count($this->parts) > 0) {

      // remove controlador e método da rota, deixando somente parâmetros
      $controllerName = ucfirst(array_shift($this->parts)) . 'Controller';
      $methodName = strtolower(array_shift($this->parts));
      $params = $this->parts ?? '';
    }

    $controllerFilePath = '../app/controllers/' . $controllerName . '.php';
    $controller = '';

    if (file_exists($controllerFilePath)) {
      require_once $controllerFilePath;
      $controller = new $controllerName();
    }

    if (method_exists($controller, $methodName)) {
      call_user_func_array([$controller, $methodName], $params);
    }
    else {
      ErrorController::not_found();
    }
  }
}
