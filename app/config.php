<?php

// Database
define('DB_HOST', 'localhost');
define('DB_NAME', 'estudo_mvc');
define('DB_USER', 'root');
define('DB_PASSWORD', 'root');

// URL padrão
define('BASE', dirname($_SERVER['SCRIPT_NAME']));

// Debug function
function debug($value) {
  echo '<pre>';
  print_r($value);
  echo '</pre>' ;
}