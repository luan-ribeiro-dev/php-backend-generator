<?php

namespace Controle;

use DateTime;
use Exception;
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
		return ($value != null) ? trim(preg_replace('~[\\\\*?<>|;!$%\'&]~', '', $value)) : $value;
	}

	public static function formatarValorBRL(?float $valor = 0, $isCifrao = true)
	{
		$formatter = new NumberFormatter('pt_BR',  NumberFormatter::CURRENCY);
		if ($valor != null) {
			if ($isCifrao) {
				$valorFormatado = $formatter->formatCurrency($valor, 'BRL');
				$valorFormatado = "R$" . substr($valorFormatado, 4);
				return $valorFormatado;
			} else {
				$valor = number_format($valor, 2, ',', '.');
				return $valor;
			}
		} else {
			$valorFormatado = $formatter->formatCurrency(0.0, 'BRL');
			if (!$isCifrao) return substr($valorFormatado, 4, strlen($valorFormatado));
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

	/**
	 * @param string|DateTime
	 */
	public static function formatarDataBRL($data)
	{
		if ($data instanceof DateTime)
			return $data->format('d/m/Y');
		else {
			$data = new DateTime($data);
			return $data->format('d/m/Y');
		}
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

		if ($ano > 0) return "Há " . $ano . " anos";
		else if ($mes > 0) return "Há " . $mes . " meses";
		else if ($dia > 0) return "Há " . $dia . " dias";
		else if ($hora > 0) return "Há " . $hora . " horas";
		else if ($minuto > 0) return "Há " . $minuto . " minutos";
		else if ($segundo > 0) return "Há " . $segundo . " segundos";
		else return "Não calculado";
	}

	public static function validar_cpf($cpf)
	{
		$cpf = preg_replace('/[^0-9]/is', '', $cpf);
		if (strlen($cpf) != 11) {
			return false;
		}
		if (preg_match('/(\d)\1{10}/', $cpf)) {
			return false;
		}
		for ($t = 9; $t < 11; $t++) {
			for ($d = 0, $c = 0; $c < $t; $c++) {
				$d += $cpf[$c] * (($t + 1) - $c);
			}
			$d = ((10 * $d) % 11) % 10;
			if ($cpf[$c] != $d) {
				return false;
			}
		}
		return true;
	}

	public static function validar_numero_celular($telefone)
	{
		return true;
		// return preg_match('/^(?:(?:\+|00)?(55)\s?)?(?:\(?([1-9][0-9])\)?\s?)?(?:((?:9\d|[2-9])\d{3})\-?(\d{4}))$/', $telefone);
	}

	public static function validarHora($hora)
	{
		if (strlen($hora) < 5) return false;
		else if (strpos($hora, ":") === false) return false;
		else if (intval(explode(":", $hora)[1]) > 59) return false;
		else return true;
	}

	public static function getMonthName($month_value)
	{
		if ($month_value == 1) return "Janeiro";
		else if ($month_value == 2) return "Fevereiro";
		else if ($month_value == 3) return "Março";
		else if ($month_value == 4) return "Abril";
		else if ($month_value == 5) return "Maio";
		else if ($month_value == 6) return "Junho";
		else if ($month_value == 7) return "Julho";
		else if ($month_value == 8) return "Agosto";
		else if ($month_value == 9) return "Setembro";
		else if ($month_value == 10) return "Outubro";
		else if ($month_value == 11) return "Novembro";
		else if ($month_value == 12) return "Desembro";
		else return null;
	}

	public static function getDayValue($day_name)
	{
		if ($day_name == 'seg') return 1;
		else if ($day_name == 'ter') return 2;
		else if ($day_name == 'qua') return 3;
		else if ($day_name == 'qui') return 4;
		else if ($day_name == 'sex') return 5;
		else if ($day_name == 'sab') return 6;
		else if ($day_name == 'dom') return 7;
		else return null;
	}

	public static function getFileExtension($file_name)
	{
		$arr = explode('.', $file_name);
		if (count($arr) == 0) throw new Exception("Não existe estensão do arquivo");
		else {
			return $arr[count($arr) - 1];
		}
	}

	public static function arrayToString($array)
	{
		$string = "";

		if ($array != null && count($array) > 0) {
			foreach ($array as $item) {
				$string .= $item . ", ";
			}
			if ($string != "") $string = substr($string, 0, strlen($string) - 2);
		}
		return $string;
	}

	public static function csvToJson($csv_file, $is_first_line_column = true)
	{
		$csv = array_map('str_getcsv', file($csv_file));
		if (!$is_first_line_column) return $csv;

		$arr = [];
		for ($i = 1; $i < count($csv); $i++) {
			$json = [];
			for ($j = 0; $j < count($csv[$i]); $j++) {
				$json[$csv[0][$j]] = $csv[$i][$j];
			}
			$arr[] = $json;
		}
		return $arr;
	}

	public static function arrayAvg($array)
	{
		$total = 0.0;
		$quant = 0;
		foreach ($array as $value) {
			$total += $value;
			$quant++;
		}
		// echo json_encode($array). "<br><br>";
		if($quant == 0) return 0.0;
		else return $total / $quant;
	}
}
