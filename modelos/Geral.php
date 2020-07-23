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

  public static function isJson($string)
  {
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
  }

  public static function sumTime(string $time_one, $time_two)
  {
    if ($time_one != null) {
      if (strlen($time_one) > 5) $time_one = substr($time_one, 0, strlen($time_one) - 3);
      if (strlen($time_two) > 5) $time_two = substr($time_two, 0, strlen($time_two) - 3);

      $time_one = explode(":", $time_one);
      $time_two = explode(":", $time_two);
      $hour = intval($time_one[0]) + intval($time_two[0]);
      $min = intval($time_one[1]) + intval($time_two[1]);
      $min += $hour * 60;

      $time = $min / 60.0;

      $min = intval(ceil(($time - intval($time)) * 60));
      $hour = intval($time);
      if ($hour >= 24) $hour -= 24;


      if ($hour < 10) $hour = "0" . $hour;
      if ($min < 10) $min = "0" . $min;

      return $hour . ":" . $min;
    }
  }

  public static function getIP()
  {
    $ipaddress = null;
    if (isset($_SERVER['HTTP_CLIENT_IP'])) $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_X_FORWARDED'])) $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_FORWARDED'])) $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if (isset($_SERVER['REMOTE_ADDR'])) $ipaddress = $_SERVER['REMOTE_ADDR'];
    return $ipaddress;
  }

  public static function getAgoTime($date)
  {
    $date = new DateTime($date);
    $current_date = new DateTime();
    $diff_date = $date->diff($current_date);
    
    $ano = $diff_date->y;
    $mes = $diff_date->m;
    $dia = $diff_date->d;
    
    $hora = $diff_date->h;
    $minuto = $diff_date->i;
    $segundo = $diff_date->s;
    
    // echo json_encode($date->format("H:i:s"));
    // echo json_encode($current_date->format("H:i:s"));
    // echo json_encode($diff_date);
    // echo json_encode([$ano, $mes, $dia, $hora, $minuto, $segundo]);

    if($ano > 0) return "Há ".$ano." anos";
    else if($mes > 0) return "Há ".$mes." meses";
    else if($dia > 0) return "Há ".$dia." dias";
    else if($hora > 0) return "Há ".$hora." horas";
    else if($minuto > 0) return "Há ".$minuto." minutos";
    else if($segundo > 0) return "Há ".$segundo." segundo";
    else return "Não calculado";
  }
}
