<?php

class ControleSQL
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
			ControleModelo::generateFile($json, $override);
		}
	}

	public static function single(string $nomeObjeto, $override = false)
	{
		$arquivo = APPLICATION_MODELS . "/".$nomeObjeto.".json";
		if(!is_file($arquivo)){
			echo "Arquivo de modelo ".$nomeObjeto.".json não existe.";
		}else{
			$json_file = file_get_contents($arquivo);
			$json = json_decode($json_file, true);
			ControleModelo::generateFile($json, $override);
		}
	}

	private static function generateFile(array $json_object, $override)
	{
		$class = "<?php\n\n" .
			"/**\n" .
			" * Classe gerada automaticamente por php-backend-generator\n" .
			" */\n" .
			"class " . $json_object['nome'] . " extends QueryBuilder{\n";

		ControleModelo::checkAtributos($json_object, $class);
		ControleModelo::getAtributos($json_object, $class);
		ControleModelo::getConstrutor($json_object, $class);
		ControleModelo::getSearchObjectManageament($json_object, $class);
		ControleModelo::getDatabaseOperations($json_object, $class);
		ControleModelo::getObjectManageament($json_object, $class);
		ControleModelo::getMisc($json_object, $class);
		ControleModelo::getEncapsulations($json_object, $class);

		$class .= "\n}";
		$arquivo = APP_MODEL . "/" . Controle::getCapitalizedName($json_object['nome']) . ".php";

		if (!is_file($arquivo) || $override == true) {
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

	private static function getAtributos(array $json_object, string &$class)
	{
		$class .= "\n";
		foreach ($json_object['atributos'] as $atributo) {
			$class .= "	/**\n";
			$class .= "	 * @var " . $atributo['tipo'] . "\n";
			$class .= "	 */\n";
			$class .= "	private $" . $atributo['nome'] . ";\n\n";
		}
	}

	private static function getConstrutor(array $json_object, string &$class)
	{
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
			$class .= "'" . $atributo['nome'] . "', ";
		}

		$class = substr($class, 0, strlen($class) - 2);
		$class .= "]);\n";
		// Parent Constructor END

		// This Reference Start
		$class .= "\n";
		foreach ($json_object['atributos'] as $atributo) {
			$class .= '		$this->' . $atributo['nome'] . " = " . '$' . $atributo['nome'] . ";\n";
		}
		// This Reference END

		$class .= "	}\n";


		$class .= "\n";
	}

	private static function getSearchObjectManageament(array $json_object, string &$class)
	{
		$lowerName = strtolower($json_object['nome']);
		$class .= "\n";
		$class .= "	// Search Object Manageament Start\n";

		// find
		$class .= "	/**\n";
		$class .= "	 * Busca um " . $lowerName . " pelo id\n";
		$class .= "	 *\n";
		$class .= "	 * @param int \$id ID do " . $lowerName . "\n";
		$class .= "	 * @param bool \$json Se o retorno vai ser um json|array\n";
		$class .= "	 * @return " . $json_object['nome'] . "|array\n";
		$class .= "	 */\n";
		$class .= "	public static function find(\$id, \$json = false)\n";
		$class .= "	{\n";
		$class .= "		\$json = (new " . $json_object['nome'] . "())->findObject(\$id);\n\n";
		$class .= "		if(\$json == null) throw new Exception(\"" . $json_object['nome'] . " #\".\$id.\" não encontrado\");\n\n";
		$class .= "		if (\$json) return \$json;\n";
		$class .= "		else return " . $json_object['nome'] . "::getObject(\$json);\n";
		$class .= "	}\n\n";
		// ----

		// all
		$class .= "	/**\n";
		$class .= "	 * Busca todos os " . $lowerName . "s\n";
		$class .= "	 *\n";
		$class .= "	 * @param bool \$json Se o retorno vai ser um json|array\n";
		$class .= "	 * @param int \$limit Se a busca vai ter limite\n";
		$class .= "	 * @param int \$page Caso a busca tenha um limite, esse parametro vai trazer as proximas posições desse limite\n";
		$class .= "	 * @return " . $json_object['nome'] . "|array\n";
		$class .= "	 */\n";
		$class .= "	public static function all(\$json = false, int \$limit = null, int \$page = 1)\n";
		$class .= "	{\n";
		$class .= "		return " . $json_object['nome'] . "::select()\n";
		$class .= "		->get(\$json, false, \$limit, \$page);\n";
		$class .= "	}\n\n";
		// ----

		// get
		$class .= "	/**\n";
		$class .= "	 * Busca " . $lowerName . "s de acordo com as querys de busca\n";
		$class .= "	 *\n";
		$class .= "	 * @param bool \$json Se o retorno vai ser um json|array\n";
		$class .= "	 * @param bool \$single Se o retorno vai ser apenas um registro\n";
		$class .= "	 * @param int \$limit Se a busca vai ter limite\n";
		$class .= "	 * @param int \$page Caso a busca tenha um limite, esse parametro vai trazer as proximas posições desse limite\n";
		$class .= "	 * @return " . $json_object['nome'] . "[]|array\n";
		$class .= "	 */\n";
		$class .= "	public static function get(bool \$json = false, bool \$single = false, int \$limit = null, int \$page = 1)\n";
		$class .= "	{\n";
		$class .= "		\$" . $lowerName . "s = [];\n\n";
		$class .= "		\$this->limit(\$limit);\n";
		$class .= "		\$objects = parent::get(\$page);\n\n";
		$class .= "		if (\$objects != null) {\n";
		$class .= "			foreach (\$objects as \$object) {\n";
		$class .= "				if (isset(\$object['id']) && \$json == false) {\n";
		$class .= "					\$" . $lowerName . " = " . $json_object['nome'] . "::getObject(\$object);\n\n";
		$class .= "					if (\$single) return \$" . $lowerName . ";\n\n";
		$class .= "					\$" . $lowerName . "s[] = \$" . $lowerName . ";\n";
		$class .= "				} else {\n";
		$class .= "					foreach (\$object as &\$value) {\n";
		$class .= "						if (DateTime::createFromFormat('Y-m-d H:i:s', \$value) !== FALSE) {\n";
		$class .= "							\$date = new DateTime(\$value);\n";
		$class .= "							\$value = \$date->format('Y-m-d');\n";
		$class .= "						}\n";
		$class .= "					}\n";
		$class .= "					unset(\$value);\n\n";
		$class .= "					if (\$single) return \$object;\n\n";
		$class .= "					\$" . $lowerName . "s[] = \$object;\n";
		$class .= "				}\n";
		$class .= "			}\n\n";
		$class .= "			return \$" . $lowerName . "s;\n";
		$class .= "		} else {\n";
		$class .= "			return [];\n";
		$class .= "		}\n";
		$class .= "	}\n";
		// ----

		$class .= "	// Search Object Manageament End\n";
		$class .= "\n";
	}

	private static function getDatabaseOperations(array $json_object, string &$class)
	{
		$lowerName = strtolower($json_object['nome']);
		$class .= "\n";
		$class .= "	// Database Operations Start\n";

		// save
		$class .= "	/**\n";
		$class .= "	 * Salva o " . $lowerName . " no banco de dados\n";
		$class .= "	 *\n";
		$class .= "	 * @return bool Se for salvo com sucesso\n";
		$class .= "	 * @throws Exception se ocorrer um erro de validacao ou com o banco de dados\n";
		$class .= "	 */\n";
		$class .= "	public static function save()\n";
		$class .= "	{\n";
		$class .= "		\$this->sanitize();\n\n";
		$class .= "		\$result = false;\n";
		$class .= "		if (\$this->getId() == null) {\n";
		$class .= "			\$result = Controle" . $json_object['nome'] . "::create(\$this);\n";
		$class .= "			if (\$result) \$this->setId(\$this->getLastId());\n";
		$class .= "		} else {\n";
		$class .= "			\$result = Controle" . $json_object['nome'] . "::update(\$this);\n";
		$class .= "		}\n\n";
		$class .= "		return \$result;\n";
		$class .= "	}\n\n";
		// ----

		// delete
		$class .= "	/**\n";
		$class .= "	 * Envia um pedido de exclusão do " . $lowerName . " para validação\n";
		$class .= "	 *\n";
		$class .= "	 * @return bool Se for deletado com sucesso\n";
		$class .= "	 * @throws Exception se ocorrer um erro de validacao ou com o banco de dados\n";
		$class .= "	 */\n";
		$class .= "	public static function delete()\n";
		$class .= "	{\n";
		$class .= "		return Controle" . $json_object['nome'] . "::delete(\$this);\n";
		$class .= "	}\n\n";
		// ---

		// del
		$class .= "	/**\n";
		$class .= "	 * Deleta um " . $lowerName . " definitivamente\n";
		$class .= "	 *\n";
		$class .= "	 * @return bool Se for deletado com sucesso\n";
		$class .= "	 * @throws Exception se ocorrer um erro de validacao ou com o banco de dados\n";
		$class .= "	 */\n";
		$class .= "	public static function del()\n";
		$class .= "	{\n";

		if (isset($json_object['fake_delete']) && $json_object['fake_delete'] === 1)
			$class .= "		return \$this->deleteWithProcedure('deletar_" . $lowerName . "');\n";
		else
			$class .= "		return parent::delete();\n";

		$class .= "	}\n\n";
		// ---

		$class .= "	// Database Operations End\n";
		$class .= "\n";
	}

	private static function getObjectManageament(array $json_object, string &$class)
	{
		$lowerName = strtolower($json_object['nome']);
		$class .= "\n";
		$class .= "	// Object Management Start\n";

		// getObject
		$class .= "	/**\n";
		$class .= "	 * Transforma um array / json em um " . $json_object['nome'] . "\n";
		$class .= "	 *\n";
		$class .= "	 * @return " . $json_object['nome'] . "\n";
		$class .= "	 */\n";
		$class .= "	public static function getObject(array \$json_data)\n";
		$class .= "	{\n";
		$class .= "		return new " . $json_object['nome'] . "(";
		foreach ($json_object['atributos'] as $atributo) {
			$class .= "\$json_data['" . $atributo['nome'] . "'], ";
		}
		$class = substr($class, 0, strlen($class) - 2);
		$class .= ");\n";
		$class .= "	}\n\n";
		// ---

		// getPostObject
		$class .= "	/**\n";
		$class .= "	 * Processa os dados de um \$_POST e retorna um " . $json_object['nome'] . ". É importante\n";
		$class .= "	 *\n";
		$class .= "	 * @return " . $json_object['nome'] . "\n";
		$class .= "	 */\n";
		$class .= "	public static function getPostObject(array \$post_data)\n";
		$class .= "	{\n";
		$class .= "		$" . $lowerName . " = new " . $json_object['nome'] . "();\n";
		foreach ($json_object['atributos'] as $atributo) {
			if ($atributo['tipo'] == 'float') {
				$class .= "\n		try {\n";
				$class .= "			if (isset(\$post_data['" . $atributo['nome'] . "'])){\n";
				$class .= "				if(strpos(\$post_data['" . $atributo['nome'] . "'], \",\") !== false)\n";
				$class .= "					\$" . $lowerName . "->set" . Controle::getCapitalizedName($atributo['nome']) . "(Controle::formatarValorFloat(\$post_data['" . $atributo['nome'] . "']));\n";
				$class .= "				else\n";
				$class .= "					\$" . $lowerName . "->set" . Controle::getCapitalizedName($atributo['nome']) . "(floatval(\$post_data['" . $atributo['nome'] . "']));\n";
				$class .= "			}\n";
				$class .= "		} catch (Throwable \$th) {\n";
				$class .= "			\$" . $lowerName . "->set" . Controle::getCapitalizedName($atributo['nome']) . "(0.0);\n";
				$class .= "		}\n";
			} else if ($atributo['tipo'] == 'DateTime') {
				$class .= "\n		if (isset(\$post_data['" . $atributo['nome'] . "']) && \$post_data['" . $atributo['nome'] . "']!= null) {\n";
				$class .= "			try {\n";
				$class .= "				\$" . $lowerName . "->set" . Controle::getCapitalizedName($atributo['nome']) . "(new DateTime(\$post_data['" . $atributo['nome'] . "']));\n";
				$class .= "			} catch (Throwable \$th) {\n";
				$class .= "				\$" . $lowerName . "->set" . Controle::getCapitalizedName($atributo['nome']) . "(null);\n";
				$class .= "			}\n";
				$class .= "		}\n";
			} else if ($atributo['tipo'] == 'int') {
				$class .= "\n		if (isset(\$post_data['" . $atributo['nome'] . "']) && \$post_data['" . $atributo['nome'] . "'] != null) \$" . $lowerName . "->set" . Controle::getCapitalizedName($atributo['nome']) . "(intval(\$post_data['" . $atributo['nome'] . "']));\n";
			} else {
				$class .= "\n		if (isset(\$post_data['" . $atributo['nome'] . "']) && \$post_data['" . $atributo['nome'] . "'] != null) \$" . $lowerName . "->set" . Controle::getCapitalizedName($atributo['nome']) . "(\$post_data['" . $atributo['nome'] . "']);\n";
			}
		}
		$class .= "	}\n\n";
		// ---

		// getJson
		$class .= "	/**\n";
		$class .= "	 * Transforma um " . $json_object['nome'] . " em um array / json\n";
		$class .= "	 *\n";
		$class .= "	 * @return array\n";
		$class .= "	 */\n";
		$class .= "	public static function getJson(" . $json_object['nome'] . " \$" . $lowerName . ")\n";
		$class .= "	{\n";
		$class .= "		return [\n";
		foreach ($json_object['atributos'] as $atributo) {
			if ($atributo['tipo'] == 'DateTime') {
				$class .= "			\"" . $atributo['nome'] . "\" => (\$" . $lowerName . "->get" . Controle::getCapitalizedName($atributo['nome']) . "() != null) ? \$" . $lowerName . "->get" . Controle::getCapitalizedName($atributo['nome']) . "()->format('Y-m-d H:i:s') : null,\n";
			} else {
				$class .= "			\"" . $atributo['nome'] . "\" => \$" . $lowerName . "->get" . Controle::getCapitalizedName($atributo['nome']) . "(),\n";
			}
		}
		$class = substr($class, 0, strlen($class) - 2) . "\n";
		$class .= "		];\n";
		$class .= "	}\n\n";
		// ---

		$class .= "	// Object Management End\n";
		$class .= "\n";
	}

	private static function getMisc(array $json_object, string &$class)
	{
		$lowerName = strtolower($json_object['nome']);
		$class .= "\n";
		$class .= "	// Others / Misc Start\n";

		// sanitize
		$class .= "	/**\n";
		$class .= "	 * Remove / Limpa impurezas dos atributos do " . $json_object['nome'] . " \n";
		$class .= "	 *\n";
		$class .= "	 * @return " . $json_object['nome'] . "\n";
		$class .= "	 */\n";
		$class .= "	public function sanitize()\n";
		$class .= "	{\n";
		foreach ($json_object['atributos'] as $atributo) {
			$class .= "		if (\$this->get" . Controle::getCapitalizedName($atributo['nome']) . "() != null) {\n";
			if ($atributo['tipo'] == "DateTime") $class .= "			\$this->set" . Controle::getCapitalizedName($atributo['nome']) . "(new DateTime(Controle::sanitize(\$this->get" . Controle::getCapitalizedName($atributo['nome']) . "()->format('Y-m-d H:i:s'))));\n";
			else $class .= "			\$this->setId(Controle::sanitize(\$this->get" . Controle::getCapitalizedName($atributo['nome']) . "()));\n";
			$class .= "		}\n\n";
		}
		$class .= "	}\n\n";
		// ---

		// select
		$class .= "	/**\n";
		$class .= "	 * Usado para criar uma instancia do " . $json_object['nome'] . "\n";
		$class .= "	 */\n";
		$class .= "	public static function select()\n";
		$class .= "	{\n";
		$class .= "		return new " . $json_object['nome'] . "();\n";
		$class .= "	}\n\n";
		// ---

		// getArgs
		$class .= "	/**\n";
		$class .= "	 * Usado retornar o valor de todos os argumentos do " . $lowerName . ". É usado de forma generica pelo QueryBuilder para insert / update no banco de dados\n";
		$class .= "	 *\n";
		$class .= "	 * @return array\n";
		$class .= "	 */\n";
		$class .= "	public function getArgs()\n";
		$class .= "	{\n";
		$class .= "		return [\n";
		foreach (array_filter($json_object['atributos'], function ($atributo) {
			return $atributo['nome'] != "deletado";
		}) as $atributo) {
			if ($atributo['tipo'] == "DateTime") $class .= "			(\$this->get" . Controle::getCapitalizedName($atributo['nome']) . "() != null) ? \$this->get" . Controle::getCapitalizedName($atributo['nome']) . "()->format('Y-m-d H:i:s') : null,\n";
			else $class .= "			\$this->get" . Controle::getCapitalizedName($atributo['nome']) . "(),\n";
		}
		$class = substr($class, 0, strlen($class) - 2) . "\n";
		$class .= "		];\n";
		$class .= "	}\n\n";
		// ---

		$class .= "	// Others / Misc End\n";
		$class .= "\n";
	}

	private static function getEncapsulations(array $json_object, string &$class)
	{
		$lowerName = strtolower($json_object['nome']);
		$class .= "\n";
		$class .= "	// Encapsulation\n";

		foreach ($json_object['atributos'] as $atributo) {
			// get
			$class .= "	/**\n";
			$class .= "	 * Retorna o valor to atributo " . $atributo['nome'] . "\n";
			$class .= "	 *\n";
			$class .= "	 * @return " . $atributo['tipo'] . "\n";
			$class .= "	 */\n";
			$class .= "	public function get" . Controle::getCapitalizedName($atributo['nome']) . "()\n";
			$class .= "	{\n";
			$class .= "		return \$this->" . $atributo['nome'] . ";\n";
			$class .= "	}\n\n";
			// ---

			// set
			$class .= "	/**\n";
			$class .= "	 * Atribui um valor para o atributo " . $atributo['nome'] . "\n";
			$class .= "	 *\n";
			$class .= "	 * @return self\n";
			$class .= "	 */\n";
			$class .= "	public function set" . Controle::getCapitalizedName($atributo['nome']) . "(" . $atributo['tipo'] . " \$" . $atributo['nome'] . " = null)\n";
			$class .= "	{\n";
			$class .= "		\$this->" . $atributo['nome'] . " = $" . $atributo['nome'] . ";\n";
			$class .= "		return \$this;\n";
			$class .= "	}\n\n";
			// ---
		}
		$class .= "	// Encapsulation End\n";
	}
}
