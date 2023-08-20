<?php 
class Logger
{
  public static function log($message)
  {
    $file_path = '../app/logs/log.txt';
    $log_message = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
    file_put_contents($file_path, $log_message, FILE_APPEND);
  }
}