<?php
class Logger
{
  private static $log_file_path = '../app/logs/log.txt';

  public static function log($message, $level = 'alert')
  {
    // Inicializa a mensagem de log com data e hora e nível
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[$timestamp] [$level]\n";

    // Verifica se o "method" está definido e adiciona à mensagem
    if (isset($message['method'])) {
      $method = json_encode($message['method'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
      $log_message .= '{"method":"' . $method. '"';
    }

    // Verifica se o "result" está definido e formata-o em uma única linha sem barras invertidas
    if (isset($message['result'])) {
      $result = json_encode($message['result'], JSON_UNESCAPED_UNICODE);
      $result = preg_replace('/\s+/', ' ', $result);
      $log_message .= ',"result":' . $result;
    }

    // Fecha o objeto JSON
    $log_message .= "}\n\n";

    // Abre o arquivo de log em modo de escrita (append)
    if ($file_handle = fopen(self::$log_file_path, 'a')) {
      fwrite($file_handle, $log_message);
      fclose($file_handle);
    }
  }
}
