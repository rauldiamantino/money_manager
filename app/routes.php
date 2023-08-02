<?php
require_once 'config.php';
require_once 'controllers/ExpensesController.php';

// Obtém o caminho da URI da solicitação
$uri = $_SERVER['REQUEST_URI'];

// Ajuste da URL para permitir o projeto ser aberto em qualquer servidor.
$base_uri = str_replace('/public/', '', dirname($_SERVER['SCRIPT_NAME']));
$uri = str_replace($base_uri, '', $uri);

// Devide a URI em partes, e remove partes vazias ou nulas
$parts = explode('/', $uri);
$parts = array_filter($parts);

// Define as rotas e chama os controladores
if (count($parts) > 0) {

    $controllerName = ucfirst(array_shift($parts)) . 'Controller';
    $methodName = strtolower(array_shift($parts));
    
    if (class_exists($controllerName) and method_exists($controllerName, $methodName)) {
        $controller = new $controllerName();
        $controller->$methodName();
    } 
    else {
      // Caso contrário, redirecione para uma página de erro ou trate de outra forma
      // Exemplo: header("Location: /error_page");
      exit;
    }
} 
else {
    // Caso não haja partes, defina uma rota padrão, se necessário
    // Exemplo: header("Location: /default_route");
    exit;
}
