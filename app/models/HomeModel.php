<?php

class HomeModel {

  // Obtém conteúdo a ser exibido na Home
  public function getContentHome()
  {
    $result = [
      'titulo' => 'Página Inicial',
      'mensagem' => 'Money Manager',
    ];

    return $result;
  }
}