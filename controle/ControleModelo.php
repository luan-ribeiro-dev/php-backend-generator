<?php

class ControleModelo
{
	public static function generate()
	{
		$models_json_files = glob(APPLICATION_MODELS . "/*.json");
		$models_json_files = array_filter($models_json_files, function ($dir) {
			return $dir != APPLICATION_MODELS . "/example.json";
		});

		foreach ($models_json_files as $json_file) {
			$json_file = file_get_contents($json_file);
			$json = json_decode($json_file, true);
			ControleModelo::generateFile($json);
		}
	}

	private static function generateFile(array $json_object)
	{
		$class = "<?php" .
			"\n" .
			"class " . $json_object['nome'] . " extends QueryBuilder{\n";

		ControleModelo::checkAtributos($json_object, $class);
		ControleModelo::getAtributos($json_object, $class);
		ControleModelo::getConstrutor($json_object, $class);

		$class .= "\n}";
		echo $class;
	}

	private static function checkAtributos(array &$json_object)
	{
		$is_data_cadastro = false;
		foreach ($json_object['atributos'] as $atributo) {
			if ($atributo['nome'] == "data_cadastro") {
				$is_data_cadastro = true;
				break;
			}
		}

		if(!$is_data_cadastro){
			array_push($json_object['atributos'], [
				"nome" => "data_cadastro",
				"tipo" => "DateTime"
			]);
		}
	}

	private static function getAtributos(array $json_object, string &$class)
	{
		$class .= "\n";
		foreach ($json_object['atributos'] as $atributo) {
			$class .= "	/**\n";
			$class .= "	 * @var " . $atributo['tipo'] . "\n";
			$class .= "	 */\n";
			$class .= "	private $" . $atributo['nome'] . ";\n\n";
		}

		$class .= "\n";
	}

	private static function getConstrutor(array $json_object, string &$class)
	{
		$class .= "\n";
		$class .= "	public function __construct(";

		// Argumentos Start
		foreach ($json_object['atributos'] as $atributo) {
			$class .= "$" . $atributo['nome'] . " = ";
			if (isset($atributo['default']))
				$class .= $atributo['default'];
			else
				$class .= "null";
			$class .= ", ";
		}

		if (count($json_object['atributos']) > 0) {
			$class = substr($class, 0, strlen($class) - 2);
		}
		// Argumentos End

		$class .= ")\n";
		$class .= "	{\n";
		
		// Parent Constructor Start
		$class .= "		parent::__construct('" . $json_object['nome_tabela'] . "', [";

		foreach (array_filter($json_object['atributos'], function ($atributo) {
			return $atributo['nome'] != "deletado";
		})
			as
			$atributo) {
			$class.="'".$atributo['nome']."', ";
		}

		$class = substr($class, 0, strlen($class)-2);
		$class.="]);\n";
		// Parent Constructor END

		// This Reference Start
		$class .= "\n";
		foreach($json_object['atributos'] as $atributo){
			$class .= '		$this->'.$atributo['nome']." = ". '$'.$atributo['nome'].";\n";
		}
		// This Reference END

		$class .= "	}\n";


		$class .= "\n";
	}
}
