<?php
class Logger
{
  private static $log_file_path = '../app/logs/log.txt';

  public static function log($message)
  {
    // Adiciona data e hora à mensagem de log
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[$timestamp] $message\n";

    // Abre o arquivo de log em modo de escrita (append)
    if ($file_handle = fopen(self::$log_file_path, 'a')) {
      fwrite($file_handle, $log_message);
      fclose($file_handle);
    }
  }
}