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
			"class ".$json_object['nome']." extends QueryBuilder{\n";
		
		ControleModelo::getAtributos($json_object, $class);

		$class.="\n}";
		echo $class;
	}

	private static function getAtributos(array $json_object, string &$class)
	{
		
	}
}
