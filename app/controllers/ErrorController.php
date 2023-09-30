<?php
class ErrorController 
{
  // Renderiza página de erro
  public static function not_found()
  {
    // View e conteúdo para a página de erro
    $renderView = ['404' => []];
    return $renderView;
  }
}