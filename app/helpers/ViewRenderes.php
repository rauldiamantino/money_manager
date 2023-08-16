<?php

class ViewRenderer
{
  public static function render($view_path, $data = [])
  {
    require_once('../app/views/templates/header.php');
    require_once('../app/views/' . $view_path . '.php');
    require_once('../app/views/templates/footer.php');
  }
}