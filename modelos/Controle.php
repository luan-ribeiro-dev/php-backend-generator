<?php

namespace Controle;

use DateTime;
use NumberFormatter;

class Geral
{
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

  public static function formatarValorBRL(float $valor = 0, $isCifrao = true)
  {
    if ($isCifrao) {
      $formatter = new NumberFormatter('pt_BR',  NumberFormatter::CURRENCY);
      $valorFormatado = $formatter->formatCurrency($valor, 'BRL');
      return $valorFormatado;
    } else {
      $valor = number_format($valor, 2, ',', '.');
      return $valor;
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
  // public static function validateUsuario(int $nivel_acceso)
  // {
  // 	$usuario = Usuario::find($_SESSION['user_id']);
  // 	return $usuario->getNivelAcesso() >= $nivel_acceso;
  // }

  public static function formatarDataBRL(DateTime $data)
  {
    return $data->format('d/m/Y H:i:s');
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
    echo print_r($value);
    echo "</pre>";
  }
}
