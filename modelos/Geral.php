<?php

namespace Controle;

use DateTime;
use NumberFormatter;

class Geral
{
  const VALIDATION_ERROR_CODE = 1000;
  const UNKNOWN_ERROR_CODE = 1001;

  public static function getBasename()
  {
    $link = $_SERVER['PHP_SELF'];
    $link_array = explode('/', $link);
    return str_replace(".php", "", end($link_array));
  }

  public static function sanitize(?string $value)
  {
    return ($value != null) ? trim(preg_replace('~[\\\\*?"<>|;!$%¨\'&]~', '', $value)) : $value;
  }

  public static function formatarValorBRL(?float $valor = 0, $isCifrao = true)
  {
    $formatter = new NumberFormatter('pt_BR',  NumberFormatter::CURRENCY);
    if ($valor != null) {
      if ($isCifrao) {
        $valorFormatado = $formatter->formatCurrency($valor, 'BRL');
        return $valorFormatado;
      } else {
        $valor = number_format($valor, 2, ',', '.');
        return $valor;
      }
    } else {
      $valorFormatado = $formatter->formatCurrency(0.0, 'BRL');
      return $valorFormatado;
    }
  }

  public static function formatarValorFloat($valor)
  {
    if ($valor != null) {
      $valor = trim(preg_replace('~[R$ .]~', '', $valor));
      $valor = floatval(preg_replace('~[,]~', '.', $valor));
      $valor = number_format((float) $valor, 2, '.', '');
      return $valor;
    } else {
      return 0;
    }
  }

  public static function checkDate(DateTime $date)
  {
    if (!checkdate($date->format('m'), $date->format('d'), $date->format('Y')) || $date < new DateTime('1950-01-01')) {
      return false;
    } else {
      return true;
    }
  }

  public static function formatarDataBRL(DateTime $data)
  {
    return $data->format('d/m/Y');
  }

  public static function existWordInURL(string $word)
  {
    return (strpos($_SERVER['PHP_SELF'], $word) !== false);
  }

  public static function checkBool($string)
  {
    $string = strtolower($string);
    return (in_array($string, array("true", "false", "1", "0", "yes", "no"), true));
  }

  public static function boolVal($string)
  {
    $string = strtolower($string);
    if (in_array($string, ['true', '1'])) return true;
    else return false;
  }

  public static function debug($value)
  {
    echo "<pre>";
    print_r($value);
    echo "</pre>";
  }

  public static function generateRandomString($length = 16)
  {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
  }
}
