<?php

class HomeModel {

  public function getContentHome()
  {
    $result = [
      'titulo' => 'Página Inicial',
      'mensagem' => 'Money Manager',
    ];

    return $result;
  }
}