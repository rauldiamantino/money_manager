<?php 

class ErrorController 
{
  public function notFound()
  {
    http_response_code(404);
    require_once '../app/views/404.php';
  }
}