<?php

namespace Layout;

class Geral
{
  public static function header_validation_error()
  {
    header('HTTP/1.1 400 Validation error');
    header('Content-Type: application/json; charset=UTF-8');
  }

  public static function header_unknown_error()
  {
    header('HTTP/1.1 400 Unknown error');
    header('Content-Type: application/json; charset=UTF-8');
  }

  public static function is_url_page(string $request_uri){
    $current_uri = $_SERVER['REQUEST_URI'];
    return ($request_uri == $current_uri);
  }
}
