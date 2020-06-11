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

  public static function validateCPF(string $cpf = null)
  {

    // Verifica se um número foi informado
    if (empty($cpf)) {
      return false;
    }

    // Elimina possivel mascara
    $cpf = preg_replace("/[^0-9]/", "", $cpf);
    $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);

    // Verifica se o numero de digitos informados é igual a 11 
    if (strlen($cpf) != 11) {
      return false;
    }
    // Verifica se nenhuma das sequências invalidas abaixo 
    // foi digitada. Caso afirmativo, retorna falso
    else if (
      $cpf == '00000000000' ||
      $cpf == '11111111111' ||
      $cpf == '22222222222' ||
      $cpf == '33333333333' ||
      $cpf == '44444444444' ||
      $cpf == '55555555555' ||
      $cpf == '66666666666' ||
      $cpf == '77777777777' ||
      $cpf == '88888888888' ||
      $cpf == '99999999999'
    ) {
      return false;
      // Calcula os digitos verificadores para verificar se o
      // CPF é válido
    } else {

      for ($t = 9; $t < 11; $t++) {

        for ($d = 0, $c = 0; $c < $t; $c++) {
          $d += $cpf{
            $c} * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf{
          $c} != $d) {
          return false;
        }
      }

      return true;
    }
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
    return $data->format('d/m/Y');
  }

  public static function existWordInURL(string $word)
  {
    return (strpos($_SERVER['PHP_SELF'], $word) !== false);
  }

  // public static function validateUsuario()
  // {
  // 	try {
  // 		if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != null && isset($_SESSION['login']) && isset($_SESSION['senha'])) {
  // 			if (Usuario::validateLogin($_SESSION['login'], $_SESSION['senha'])) {
  // 				return true;
  // 			} else {
  // 				throw new Exception("Invalid User");
  // 			}
  // 		} else {
  // 			throw new Exception("User session don't exist");
  // 		}
  // 	} catch (Throwable $th) {
  // 		header('Location: /login');
  // 		return false;
  // 	}
  // }

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
