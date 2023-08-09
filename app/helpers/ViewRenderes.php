<?php

class ViewRenderer
{
  public static function render($view_path, $data = [])
  {
    include('../app/views/' . $view_path . '.php');
  }
}