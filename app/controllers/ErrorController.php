<?php
require_once '../app/helpers/ViewRenderes.php';

class ErrorController 
{
  public static function not_found()
  {
    ViewRenderer::render('404');
  }
}