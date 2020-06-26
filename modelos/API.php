<?php

namespace Controle;

use Modelo\ValidationException;


class API
{
  /**
   * try catch padrão da API
   */
  public static function try($content)
  {
    try {
      $content();
    } catch (ValidationException $th) {
      \Layout\Geral::header_validation_error();
      die(json_encode([
        "message" => "Ocorreu um erro validação: \n" . $th->toString(),
        "errors" => $th->getErrors(),
        "code" => \Controle\Geral::VALIDATION_ERROR_CODE
      ]));
    } catch (\Throwable $th) {
      \Layout\Geral::header_unknown_error();
      die(json_encode([
        "message" => "Ocorreu um erro desconhecido: " . $th->getMessage(),
        "code" => \Controle\Geral::UNKNOWN_ERROR_CODE
      ]));
    }
  }
}
