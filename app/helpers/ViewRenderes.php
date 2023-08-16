<?php

class ViewRenderer
{
  public static function render($view_path, $data = [])
  {
    include('../app/views/templates/header.php');
    include('../app/views/' . $view_path . '.php');
    include('../app/views/templates/footer.php');
  }
}