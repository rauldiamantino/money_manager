<?php 

class ErrorController 
{
  public static function notFound()
  {
    http_response_code(404);
    require_once '../app/views/404.php';
  }
}