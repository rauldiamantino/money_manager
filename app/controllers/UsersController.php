<?php
require_once '../app/models/UsersModel.php';

class UsersController
{
  public $usersModel;

  public function __construct()
  {
    $this->usersModel = new UsersModel();
  }
}