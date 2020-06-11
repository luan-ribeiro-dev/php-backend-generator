<?php

class ControleOfControls
{
  public static function generate($override = false)
  {
    $models_json_files = glob(APPLICATION_MODELS . "/*.json");
    $models_json_files = array_filter($models_json_files, function ($dir) {
      return $dir != APPLICATION_MODELS . "/example.json";
    });

    foreach ($models_json_files as $json_file) {
      $json_file = file_get_contents($json_file);
      $json = json_decode($json_file, true);
      ControleOfControls::generateFile($json, $override);
    }
  }


  public static function single(string $nomeObjeto, $override = false)
  {
    $arquivo = APPLICATION_MODELS . "/" . $nomeObjeto . ".json";
    if (!is_file($arquivo)) {
      echo "Arquivo de modelo " . $nomeObjeto . ".json não existe.";
    } else {
      $json_file = file_get_contents($arquivo);
      $json = json_decode($json_file, true);
      ControleOfControls::generateFile($json, $override);
    }
  }

  private static function generateFile(array $json_object, $override = false)
  {
    $class = "<?php\n" .
      "\n" .
      "namespace Controle;\n" .
      "\n" .
      "use Modelo\ValidationException;\n" .
      "\n" .
      "/**\n" .
      " * Classe gerada automaticamente por php-backend-generator\n" .
      " */\n" .
      "class " . Controle::getCapitalizedName($json_object['nome']) . "\n";
    $class .= "{\n";

    ControleOfControls::checkAtributos($json_object, $class);
    ControleOfControls::getValidates($json_object, $class);
    ControleOfControls::getDatabaseChecks($json_object, $class);

    $class .= "\n}";
    $arquivo = APP_CONTROLE . "/" . Controle::getCapitalizedName($json_object['nome']) . ".php";
    if (!is_file($arquivo) || ($override * $json_object['replace'])) {
      $file = fopen($arquivo, 'w') or die('Cannot open file:  ' . $arquivo);
      fwrite($file, $class);
    }
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

    if (!$is_data_cadastro) {
      array_push($json_object['atributos'], [
        "nome" => "data_cadastro",
        "tipo" => "DateTime"
      ]);
    }
  }

  private static function getValidates(array $json_object, string &$class)
  {
    $lowerName = strtolower($json_object['nome']);
    $class .= "\n";

    $class .= "	// Validates Start\n";
    // validate
    $class .= "	/**\n";
    $class .= "	 * Valida um salvamento de uma " . $lowerName . "\n";
    $class .= "	 *\n";
    $class .= "	 * @return true Se for validado\n";
    $class .= "	 * @throws ValidationException Caso tenha algum erro de validação\n";
    $class .= "	 */\n";
    $class .= "	public static function validate(\\Modelo\\" . Controle::getCapitalizedName($json_object['nome']) . " \$" . $lowerName . ")\n";
    $class .= "	{\n";
    $class .= "		\$errors = [];\n\n";
    foreach (array_filter($json_object['atributos'], function ($atributo) {
      return isset($atributo['validacoes'][$atributo['nome']]);
    }) as $atributo) {
      $validation = $atributo['validacoes'][$atributo['nome']];
      if (in_array("not null", $validation)) {
        $class .= "		if (\$" . $lowerName . "->get" . Controle::getCapitalizedName($atributo['nome']) . "() == null || \$" . $lowerName . "->get" . Controle::getCapitalizedName($atributo['nome']) . "() == \"\") {\n";
        $class .= "			\$errors['validation']['" . $atributo['nome'] . "'][] = \"O " . Controle::getSeparateName($atributo['nome']) . " é nulo\";\n";
        $class .= "		}\n\n";
      }
    }
    $class .= "\n";
    $class .= "		if (count(\$errors) > 0) {\n";
    $class .= "			throw new ValidationException(\$errors);\n";
    $class .= "		}\n\n";
    $class .= "		return true;\n";
    $class .= "	}\n\n";
    // ----

    // validateDelete
    $class .= "	/**\n";
    $class .= "	 * Valida a exclusão de uma " . $lowerName . "\n";
    $class .= "	 *\n";
    $class .= "	 * @return true Se for validado\n";
    $class .= "	 * @throws ValidationException Caso tenha algum erro de validação\n";
    $class .= "	 */\n";
    $class .= "	public static function validateDelete(\\Modelo\\" . Controle::getCapitalizedName($json_object['nome']) . " \$" . $lowerName . ")\n";
    $class .= "	{\n";
    $class .= "		return true;\n";
    $class .= "	}\n\n";
    // ----
    $class .= "	// Validates End\n";
    $class .= "\n";
  }

  private static function getDatabaseChecks(array $json_object, string &$class)
  {
    $lowerName = strtolower($json_object['nome']);
    $class .= "\n";

    $class .= "	// Database Checks Start\n";
    // Create
    $class .= "	/**\n";
    $class .= "	 * Valida o objeto para criação e enviar uma ordem de criação caso validado\n";
    $class .= "	 *\n";
    $class .= "	 * @return bool Se for validado e cadastrado\n";
    $class .= "	 * @throws Exception Validação ou erro com o banco de dados\n";
    $class .= "	 */\n";
    $class .= "	public static function create(\\Modelo\\" . Controle::getCapitalizedName($json_object['nome']) . " \$" . $lowerName . ")\n";
    $class .= "	{\n";
    $class .= "		if (" . Controle::getCapitalizedName($json_object['nome']) . "::validate(\$" . $lowerName . ")) {\n";
    $class .= "			return \$" . $lowerName . "->create();\n";
    $class .= "		} else {\n";
    $class .= "			return false;\n";
    $class .= "		}\n";
    $class .= "	}\n\n";
    // ----

    // update
    $class .= "	/**\n";
    $class .= "	 * Valida o objeto para edicao e enviar uma ordem de edicao caso validado\n";
    $class .= "	 *\n";
    $class .= "	 * @return bool Se for validado e editado\n";
    $class .= "	 * @throws Exception Validacao ou erro com o banco de dados\n";
    $class .= "	 */\n";
    $class .= "	public static function update(\\Modelo\\" . Controle::getCapitalizedName($json_object['nome']) . " \$" . $lowerName . ")\n";
    $class .= "	{\n";
    $class .= "		if (" . Controle::getCapitalizedName($json_object['nome']) . "::validate(\$" . $lowerName . ")) {\n";
    $class .= "			\$old" . Controle::getCapitalizedName($json_object['nome']) . " = \\Modelo\\" . Controle::getCapitalizedName($json_object['nome']) . "::find(\$" . $lowerName . "->getId());\n";
    $class .= "			if (\n				";
    foreach ($json_object['atributos'] as $atributo) {
      $class .= "\$old" . Controle::getCapitalizedName($json_object['nome']) . "->get" . Controle::getCapitalizedName($atributo['nome']) . "() === \$" . $lowerName . "->get" . Controle::getCapitalizedName($atributo['nome']) . "()\n				&& ";
    }
    $class = substr($class, 0, strlen($class) - 7);
    $class .= "			) {\n";
    $class .= "				return true;\n";
    $class .= "			} else {\n";
    $class .= "				return \$" . $lowerName . "->update();\n";
    $class .= "			}\n";
    $class .= "		} else {\n";
    $class .= "			return false;\n";
    $class .= "		}\n";
    $class .= "	}\n\n";
    // ----

    // delete
    $class .= "	/**\n";
    $class .= "	 * Valida o objeto para exclusao e enviar uma ordem de exclusao caso validado\n";
    $class .= "	 *\n";
    $class .= "	 * @return bool Se for validado e deletado\n";
    $class .= "	 * @throws Exception Validacao ou erro com o banco de dados\n";
    $class .= "	 */\n";
    $class .= "	public static function delete(\\Modelo\\" . Controle::getCapitalizedName($json_object['nome']) . " \$" . $lowerName . ")\n";
    $class .= "	{\n";
    $class .= "		if (" . Controle::getCapitalizedName($json_object['nome']) . "::validateDelete(\$" . $lowerName . ")) {\n";
    $class .= "			return \$" . $lowerName . "->del();\n";
    $class .= "		} else {\n";
    $class .= "			return false;\n";
    $class .= "		}\n";
    $class .= "	}\n\n";
    // ----
    $class .= "	// Database Checks End\n";
    $class .= "\n";
  }
}
