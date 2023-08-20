<?php
class ErrorController 
{
  // Renderiza página de erro
  public static function not_found()
  {
    // View e conteúdo para a página de erro
    $view_name = '404';
    $view_content = [];

    return [ $view_name => $view_content ];
  }
}