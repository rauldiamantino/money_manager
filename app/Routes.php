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
    // Prepara URI independente do local do servidor
    if (strstr($_SERVER['REQUEST_URI'], '/public')) {
      define('BASE', dirname($_SERVER['SCRIPT_NAME']));
      $this->uri = str_replace('/public', '', $_SERVER['REQUEST_URI']);
    }
    else {
      define('BASE', '');
      $this->uri = $_SERVER['REQUEST_URI'];
    }

    $this->handleRoutes();
  }

  // Recupera a rota e chama o controlador
  private function handleRoutes()
  {
    $route = $this->routes($this->uri);
    $controllerName = $route['controller'];
    $methodName = $route['method'];
    $params = $route['params'] ?? [];

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

  public function routes($uri)
  {
    // Verificação especial para a raiz
    if ($uri === '/') {
        return ['controller' => 'HomeController', 'method' => 'index', 'params' => []];
    }

    $routes = [
        '/login' => ['LoginController' => 'start'],
        '/register' => ['RegisterController' => 'start'],
        '/panel' => ['PanelController' => 'display'],
        '/accounts' => ['AccountsController' => 'accounts'],
        '/myaccount' => ['MyaccountController' => 'start'],
        '/password' => ['PasswordController' => 'start'],
        '/categories' => ['CategoriesController' => 'categories'],
        '/transactions' => ['TransactionsController' => 'transactions'],
    ];

    foreach ($routes as $key => $value):

        // Padroniza rota com barras antes de verificar
        $pattern = '/^' . preg_quote($key, '/') . '/';
        $params = [];

        if (preg_match($pattern, $uri)) {

            // Remove a parte da rota da URI
            $paramsString = substr($uri, strlen($key));

            // Divide a string de parâmetros por barras
            $params = explode('/', trim($paramsString, '/'));

            // Remove valores vazios
            $params = array_filter($params);

            return ['controller' => key($value), 'method' => current($value), 'params' => $params ];
        }
    endforeach;

    return ['controller' => 'ErrorController', 'method' => 'not_found'];
  }
}