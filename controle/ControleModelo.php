<?php

class ControleModelo
{
	public static function getJsonModels()
	{
		$models_json_files = glob(APPLICATION_MODELS . "/*.json");
		$models_json_files = array_filter($models_json_files, function ($dir) {
			return $dir != APPLICATION_MODELS . "/example.json";
		});

		$json_objects = [];
		foreach ($models_json_files as $json_file) {
			$json_file = file_get_contents($json_file);
			$json = json_decode($json_file, true);
			$json_objects[] = $json;
		}
		return $json_objects;
	}

	public static function generate($override = false)
	{
		foreach (ControleModelo::getJsonModels() as $json) {
			ControleModelo::generateFile($json, $override);
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
			ControleModelo::generateFile($json, $override);
		}
	}

	private static function generateFile(array $json_object, $override)
	{
		foreach ($json_object['atributos'] as &$atributo) {
			if (isset($atributo['link'])) {
				if ($atributo['link']['tipo'] == "objeto") {
					$atributo['link']['nome_atributo'] = substr($atributo['nome'], 3);
				} else if ($atributo['link']['tipo'] == "lista") {
					$atributo['link']['nome_atributo'] = strtolower($atributo['link']['nome']);
				}
			}
		}
		unset($atributo);

		$class = "<?php\n" .
			"\n" .
			"namespace Modelo;\n" .
			"\n" .
			"use DAO\QueryBuilder;\n" .
			"use DateTime;\n" .
			"use Exception;\n" .
			"use Throwable;\n" .
			"\n" .
			"/**\n" .
			" * Classe gerada automaticamente por php-backend-generator\n" .
			" */\n" .
			"class " . Controle::getCapitalizedName($json_object['nome']) . " extends QueryBuilder{\n";

		ControleModelo::checkAtributos($json_object, $class);
		ControleModelo::getConstantes($json_object, $class);
		ControleModelo::getAtributos($json_object, $class);
		ControleModelo::getConstrutor($json_object, $class);
		ControleModelo::getSearchObjectManageament($json_object, $class);
		ControleModelo::getDatabaseOperations($json_object, $class);
		ControleModelo::getObjectManageament($json_object, $class);
		ControleModelo::getMisc($json_object, $class);
		ControleModelo::getEncapsulations($json_object, $class);
		ControleModelo::getQuerybuildReturns($json_object, $class);

		$class .= "\n}";
		$arquivo = APP_MODEL . "/" . Controle::getCapitalizedName($json_object['nome']) . ".php";

		if (!is_file($arquivo) || ($override + $json_object['replace'])) {
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

	private static function getConstantes(array $json_object, string &$class)
	{
		if (isset($json_object['constantes'])) {
			$class .= "\n";
			foreach ($json_object['constantes'] as $constante) {
				$key = array_keys($constante)[0];
				$class .= " const " . $key . " = " . $constante[$key] . ";\n";
			}
		}
	}

	private static function getAtributos(array $json_object, string &$class)
	{
		$class .= "\n";
		foreach ($json_object['atributos'] as $atributo) {
			if ($atributo['tipo'] == "objeto_assoc") {
				$class .= "	/**\n";
				$class .= "	 * @var " . $atributo['link']['nome'] . "\n";
				$class .= "	 */\n";
				$class .= "	private $" . $atributo['nome'] . ";\n\n";
			} else {
				$class .= "	/**\n";
				$class .= "	 * @var " . $atributo['tipo'] . "\n";
				$class .= "	 */\n";
				$class .= "	private $" . $atributo['nome'];

				if (isset($atributo['link']) && $atributo['link']['tipo'] == "lista")
					$class .= " = [];\n\n";
				else
					$class .= ";\n\n";

				if (isset($atributo['link'])) {
					if ($atributo['link']['tipo'] == "objeto") {
						$lowerName = $atributo['link']['nome_atributo'];

						$class .= "	/**\n";
						$class .= "	 * @var " . $atributo['link']['nome'] . "\n";
						$class .= "	 */\n";
						$class .= "	private $" . $lowerName . ";\n\n";
					}
				}
			}
		}
	}

	private static function getConstrutor(array $json_object, string &$class)
	{
		$class .= "	public function __construct(";

		// Argumentos Start
		foreach (array_filter($json_object['atributos'], function ($atributo) {
			if (isset($atributo['link'])) return $atributo['tipo'] != "objeto_assoc" && $atributo['link']['tipo'] != "lista";
			else return true;
		}) as $atributo) {
			$class .= "?" . $atributo['tipo'] . " ";
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
			if (isset($atributo['link'])) return $atributo['tipo'] != "objeto_assoc" && $atributo['link']['tipo'] != "lista" && $atributo['nome'] != "deletado";
			else return $atributo['nome'] != "deletado";
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
		foreach (array_filter($json_object['atributos'], function ($atributo) {
			if (isset($atributo['link'])) return $atributo['tipo'] != "objeto_assoc" && $atributo['link']['tipo'] != "lista";
			else return true;
		}) as $atributo) {
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

		// get
		$class .= "	/**\n";
		$class .= "	 * Busca " . $lowerName . "s de acordo com as querys de busca\n";
		$class .= "	 *\n";
		$class .= "	 * @param bool \$json Se o retorno vai ser um json|array\n";
		$class .= "	 * @param bool \$single Se o retorno vai ser apenas um registro\n";
		$class .= "	 * @param int \$limit Se a busca vai ter limite\n";
		$class .= "	 * @param int \$page Caso a busca tenha um limite, esse parametro vai trazer as proximas posições desse limite\n";
		$class .= "	 * @return " . Controle::getCapitalizedName($json_object['nome']) . "[]|array\n";
		$class .= "	 */\n";
		$class .= "	public function get(bool \$json = false, bool \$single = false, int \$page = 1)\n";
		$class .= "	{\n";
		$class .= "		\$" . $lowerName . "s = [];\n\n";
		$class .= "		\$objects = parent::getObjects(\$page);\n\n";
		$class .= "		if (\$objects != null) {\n";
		$class .= "			foreach (\$objects as \$object) {\n";
		$class .= "				if (\$json == false) {\n";
		$class .= "					\$" . $lowerName . " = " . Controle::getCapitalizedName($json_object['nome']) . "::getObject(\$object);\n";
		$class .= "					\$" . $lowerName . "\n";
		$class .= "						->childs(\$this->getChilds())\n";
		$class .= "						->columns(\$this->getColumns())\n";
		$class .= "						->config(\$this->getConfig());\n\n";
		$class .= "					if(isset(\$object['id'])) \$" . $lowerName . "->attachChilds();\n";
		$class .= "					if (\$single) return \$" . $lowerName . ";\n\n";
		$class .= "					\$" . $lowerName . "s[] = \$" . $lowerName . ";\n";
		$class .= "				} else {\n";
		$class .= "					foreach (\$object as &\$value) {\n";
		$class .= "						if(\$this->getRemoveHour()){\n";
		$class .= "						  if (DateTime::createFromFormat('Y-m-d H:i:s', \$value) !== FALSE) {\n";
		$class .= "						  	\$date = new DateTime(\$value);\n";
		$class .= "						  	\$value = \$date->format('Y-m-d');\n";
		$class .= "						  }\n";
		$class .= "						}\n";
		$class .= "					}\n";
		$class .= "					unset(\$value);\n\n";
		$class .= "					" . Controle::getCapitalizedName($json_object['nome']) . "::attachChildsJson(\$object, \$this->getChilds(), \$this->getConfig());\n";
		$class .= "					if (\$single) return \$object;\n\n";
		$class .= "					\$" . $lowerName . "s[] = \$object;\n";
		$class .= "				}\n";
		$class .= "			}\n\n";
		$class .= "			return \$" . $lowerName . "s;\n";
		$class .= "		} else {\n";
		$class .= "			return [];\n";
		$class .= "		}\n";
		$class .= "	}\n\n";
		// ----

		// attachChilds
		$class .= "	/**\n";
		$class .= "	 * Conecta aos objetos que a " . $lowerName . " dependentes da " . $lowerName . "\n";
		$class .= "	 *\n";
		$class .= "	 * @return self\n";
		$class .= "	 */\n";
		$class .= "	public function attachChilds()\n";
		$class .= "	{\n";
		$objetos_dependentes = array_filter($json_object['atributos'], function ($atributo) {
			return isset($atributo['link']);
		});
		if (count($objetos_dependentes) > 0) {
			$class .= "		if (\$this->getId() != null) {\n";
			$class .= "		  \$config = \$this->getConfig();\n";
			foreach ($objetos_dependentes as $atributo) {
				if ($atributo['tipo'] != "objeto_assoc" && $atributo['link']['tipo'] == "objeto") {
					$class .= "\n			if(count(\$this->getChilds()) > 0 && in_array('" . $atributo['link']['nome_atributo'] . "', array_values(\$this->getChilds()))){\n";
					$class .= "			  if(\$this->get" . Controle::getCapitalizedName($atributo['nome']) . "()!=null){\n";
					$class .= "				  \$childs = (isset(\$config['" . $atributo['link']['nome_atributo'] . "']) && isset(\$config['" . $atributo['link']['nome_atributo'] . "']['childs'])) ? \$config['" . $atributo['link']['nome_atributo'] . "']['childs'] : [];\n";
					$class .= "				  \$columns = (isset(\$config['" . $atributo['link']['nome_atributo'] . "']) && isset(\$config['" . $atributo['link']['nome_atributo'] . "']['columns'])) ? \$config['" . $atributo['link']['nome_atributo'] . "']['columns'] : [];\n";
					$class .= " 				  \$" . $atributo['link']['nome_atributo'] . " = " . $atributo['link']['nome'] . "::select()\n";
					$class .= " 	  			  ->columns(\$columns)\n";
					$class .= " 	  			  ->childs(\$childs)\n";
					$class .= " 	  			  ->config((isset(\$config['" . $atributo['link']['nome_atributo'] . "'])) ? \$config['" . $atributo['link']['nome_atributo'] . "'] : [])\n";
					$class .= " 	  			  ->where('id = ?', \$this->get" . Controle::getCapitalizedName($atributo['nome']) . "())\n";
					$class .= " 	  			  ->get(false, true);\n";
					$class .= "   				\$this->set" . $atributo['link']['nome'] . "(\$" . $atributo['link']['nome_atributo'] . ");\n";
					$class .= "   			}\n";
					$class .= "  		}\n";
				} else if ($atributo['tipo'] == "objeto_assoc" || $atributo['link']['tipo'] == "lista") {
					// echo var_dump(array_map(function ($element){return $element['nome'];}, ControleModelo::getJsonModels()));
					$object = array_filter(ControleModelo::getJsonModels(), function ($object) use ($atributo) {
						return $object['nome'] == $atributo['link']['nome'];
					});
					if (count($object) == 0) throw new Exception("arquivo " . $atributo['link']['nome'] . " do modelo " . $atributo['link']['tipo'] . " Não foi encontrado");
					else $object = array_values($object)[0];
					$class .= "\n			if(count(\$this->getChilds()) > 0 && in_array('" . $atributo['link']['nome_atributo'] . "', array_values(\$this->getChilds()))){\n";
					$class .= "  			\$childs = (isset(\$config['" . $atributo['link']['nome_atributo'] . "']) && isset(\$config['" . $atributo['link']['nome_atributo'] . "']['childs'])) ? \$config['" . $atributo['link']['nome_atributo'] . "']['childs'] : [];\n";
					$class .= "  			\$columns = (isset(\$config['" . $atributo['link']['nome_atributo'] . "']) && isset(\$config['" . $atributo['link']['nome_atributo'] . "']['columns'])) ? \$config['" . $atributo['link']['nome_atributo'] . "']['columns'] : [];\n";
					$class .= "  			$" . $atributo['nome'] . " = " . Controle::getCapitalizedName($atributo['link']['nome']) . "::select()\n";
					if (isset($atributo['link']['id_assoc']))
						$id = $atributo['link']['id_assoc'];
					else
						$id = "id_" . $atributo['link']['nome_atributo'];

					$class .= "  			  ->columns(\$columns)\n";
					$class .= "				  ->childs(\$childs)\n";
					$class .= "  			  ->config((isset(\$config['" . $atributo['link']['nome_atributo'] . "'])) ? \$config['" . $atributo['link']['nome_atributo'] . "'] : [])\n";
					$class .= "  				->innerJoin('" . $atributo['link']['tabela_associativa'] . "', '" . $atributo['link']['tabela_associativa'] . "." . $id . " = " . $object['nome_tabela'] . ".id')\n";
					$class .= "  				->where('" . $atributo['link']['tabela_associativa'] . ".id_" . $lowerName . " = ?', \$this->getId())\n";

					if ($atributo['tipo'] == "objeto_assoc") {
						$class .= "  				->get(false, true);\n";
						$class .= "  			if ($" . $atributo['nome'] . " != null) \$this->set" . Controle::getCapitalizedName($atributo['nome']) . "($" . $atributo['nome'] . ");\n";
					} else {
						$class .= "  		    ->get();\n";
						$class .= "  			if ($" . $atributo['nome'] . " != null && count($" . $atributo['nome'] . ") > 0) \$this->set" . Controle::getCapitalizedName($atributo['nome']) . "($" . $atributo['nome'] . ");\n";
					}
					$class .= "  		}\n";
				}
			}
			$class .= "		}\n";
		}
		$class .= "		return \$this;\n";
		$class .= "	}\n";
		// ----

		// attachChildsJson
		$class .= "	/**\n";
		$class .= "	 * Conecta aos objetos que a " . $lowerName . " dependentes da " . $lowerName . "\n";
		$class .= "	 */\n";
		$class .= "	public static function attachChildsJson(&\$" . $lowerName . ", array \$childs_json = [], array \$config = [])\n";
		$class .= "	{\n";
		$objetos_dependentes = array_filter($json_object['atributos'], function ($atributo) {
			return isset($atributo['link']);
		});
		if (count($objetos_dependentes) > 0) {
			$class .= "		if (isset(\$" . $lowerName . "['id']) && \$" . $lowerName . "['id'] != null) {\n";
			foreach ($objetos_dependentes as $atributo) {
				if ($atributo['tipo'] != "objeto_assoc" && $atributo['link']['tipo'] == "objeto") {
					$class .= "\n			if(count(\$childs_json) > 0 && in_array('" . $atributo['link']['nome_atributo'] . "', array_values(\$childs_json))){\n";
					$class .= "	  		\$childs = (isset(\$config['" . $atributo['link']['nome_atributo'] . "']) && isset(\$config['" . $atributo['link']['nome_atributo'] . "']['childs'])) ? \$config['" . $atributo['link']['nome_atributo'] . "']['childs'] : [];\n";
					$class .= "	  		\$columns = (isset(\$config['" . $atributo['link']['nome_atributo'] . "']) && isset(\$config['" . $atributo['link']['nome_atributo'] . "']['columns'])) ? \$config['" . $atributo['link']['nome_atributo'] . "']['columns'] : [];\n";
					$class .= "	  		if(isset(\$" . $lowerName . "['" . $atributo['nome'] . "']) && \$" . $lowerName . "['" . $atributo['nome'] . "'] != null){\n";
					$class .= "	  			\$" . $atributo['link']['nome_atributo'] . " = " . $atributo['link']['nome'] . "::select()\n";
					$class .= "	  			  ->columns(\$columns)\n";
					$class .= "						->childs(\$childs)\n";
					$class .= "	  			  ->config((isset(\$config['" . $atributo['link']['nome_atributo'] . "'])) ? \$config['" . $atributo['link']['nome_atributo'] . "'] : [])\n";
					$class .= "	  			  ->where('id = ?', \$" . $lowerName . "['" . $atributo['nome'] . "'])\n";
					$class .= "	  			  ->get(true, true);\n";
					$class .= "	  			\$" . $lowerName . "['" . $atributo['link']['nome_atributo'] . "'] = \$" . $atributo['link']['nome_atributo'] . ";\n";
					$class .= "	  		}\n";
					$class .= "  		}\n";
				} else if ($atributo['tipo'] == "objeto_assoc" || $atributo['link']['tipo'] == "lista") {
					$object = array_filter(ControleModelo::getJsonModels(), function ($object) use ($atributo) {
						return $object['nome'] == $atributo['link']['nome'];
					});
					if (count($object) == 0) throw new Exception("arquivo do modelo " . $atributo['link']['tipo'] . " Não foi encontrado");
					else $object = array_values($object)[0];
					$class .= "\n			if(count(\$childs_json) > 0 && in_array('" . $atributo['link']['nome_atributo'] . "', array_values(\$childs_json))){\n";
					$class .= "				\$childs = (isset(\$config['" . $atributo['link']['nome_atributo'] . "']) && isset(\$config['" . $atributo['link']['nome_atributo'] . "']['childs'])) ? \$config['" . $atributo['link']['nome_atributo'] . "']['childs'] : [];\n";
					$class .= "				\$columns = (isset(\$config['" . $atributo['link']['nome_atributo'] . "']) && isset(\$config['" . $atributo['link']['nome_atributo'] . "']['columns'])) ? \$config['" . $atributo['link']['nome_atributo'] . "']['columns'] : [];\n";
					$class .= "				$" . $atributo['nome'] . " = " . Controle::getCapitalizedName($atributo['link']['nome']) . "::select()\n";

					if (isset($atributo['link']['id_assoc']))
						$id = $atributo['link']['id_assoc'];
					else
						$id = "id_" . $atributo['link']['nome_atributo'];
					$class .= "				  ->columns(\$columns)\n";
					$class .= "				  ->childs(\$childs)\n";
					$class .= "				  ->config((isset(\$config['" . $atributo['link']['nome_atributo'] . "'])) ? \$config['" . $atributo['link']['nome_atributo'] . "'] : [])\n";
					$class .= "					->innerJoin('" . $atributo['link']['tabela_associativa'] . "', '" . $atributo['link']['tabela_associativa'] . "." . $id . " = " . $object['nome_tabela'] . ".id')\n";
					$class .= "					->where('" . $atributo['link']['tabela_associativa'] . ".id_" . $lowerName . " = ?', \$" . $lowerName . "['id'])\n";

					if ($atributo['tipo'] == "objeto_assoc")
						$class .= "					->get(true, true);\n";
					else
						$class .= "			    ->get(true, false);\n";
					$class .= "				if ($" . $atributo['nome'] . " != null && count($" . $atributo['nome'] . ") > 0) \$" . $lowerName . "['" . $atributo['nome'] . "'] = $" . $atributo['nome'] . ";\n";
					$class .= "			}\n";
				}
			}
			$class .= "		}\n";
		}
		$class .= "		return \$" . $lowerName . ";\n";
		$class .= "	}\n";
		// ----

		// generic attachs
		foreach ($objetos_dependentes as $atributo) {
			if ($atributo['link']['tipo'] == "objeto") {
				$nomeObjeto = Controle::getCapitalizedName(substr($atributo['nome'], 3));
				$nomeAtributo = Controle::getCapitalizedName($atributo['nome']);
				$class .= "	/**\n";
				$class .= "	 * Conecta ao objeto $nomeObjeto\n";
				$class .= "	 * @return $nomeObjeto\n";
				$class .= "	 */\n";
				$class .= "	public function attach$nomeObjeto(array \$columns = ['*'])\n";
				$class .= "	{\n";
				$class .= "		if(\$this->get$nomeAtributo() == null) throw new Exception('Não foi possível conectar ao objeto $nomeObjeto pois os atributo " . $atributo['nome'] . " é nulo');\n";
				$class .= "		\$" . $atributo['link']['nome_atributo'] . " = " . $atributo['link']['nome'] . "::select()\n";
				$class .= "		  ->columns(\$columns)\n";
				$class .= "		  ->where('id = ?', \$this->get$nomeAtributo())\n";
				$class .= "		  ->get(false, true);\n";
				$class .= "		\$this->set$nomeObjeto(\$" . $atributo['link']['nome_atributo'] . ");\n";
				$class .= "		return \$this->get$nomeObjeto();\n";
				$class .= "	}\n\n";
				
				//JSON
				$class .= "	/**\n";
				$class .= "	 * Conecta ao objeto $nomeObjeto\n";
				$class .= "	 * @return $nomeObjeto\n";
				$class .= "	 */\n";
				$class .= "	public static function attach".$nomeObjeto."Json(array &\$$lowerName, array \$columns = ['*'])\n";
				$class .= "	{\n";
				$class .= "		if(!isset(\$".$lowerName."['".$atributo['nome']."']) || \$".$lowerName."['".$atributo['nome']."'] == null) throw new Exception('Não foi possível conectar ao objeto $nomeObjeto pois os atributo " . $atributo['nome'] . " é nulo');\n";
				$class .= "		\$" . $atributo['link']['nome_atributo'] . " = " . $atributo['link']['nome'] . "::select()\n";
				$class .= "		  ->columns(\$columns)\n";
				$class .= "		  ->where('id = ?', \$".$lowerName."['".$atributo['nome']."'])\n";
				$class .= "		  ->get(true, true);\n";
				$class .= "		\$".$lowerName."['" . $atributo['link']['nome_atributo'] . "'] = \$" . $atributo['link']['nome_atributo'] . ";\n";
				$class .= "	}\n\n";
			} else if ($atributo['link']['tipo'] == "lista") {
				$nomeAtributo = Controle::getCapitalizedName($atributo['nome']);
				$class .= "	/**\n";
				$class .= "	 * Conecta ao objeto $nomeAtributo\n";
				$class .= "	 * @return $nomeAtributo\n";
				$class .= "	 */\n";
				$class .= "	public function attach$nomeAtributo(array \$columns = ['*'])\n";
				$class .= "	{\n";
				$class .= "		if(\$this->getId() == null) throw new Exception('Não foi possível conectar ao objeto $nomeAtributo pois os atributo id é nulo');\n";
				$class .= "		\$" . $atributo['link']['nome_atributo'] . " = " . $atributo['link']['nome'] . "::select()\n";
				$class .= "		  ->columns(\$columns)\n";
				$class .= "		  ->innerJoin('" . $atributo['link']['tabela_associativa'] . "', '" . $atributo['link']['tabela_associativa'] . "." . $id . " = " . $object['nome_tabela'] . ".id')\n";
				$class .= "		  ->where('" . $atributo['link']['tabela_associativa'] . ".id_" . $lowerName . " = ?', \$this->getId())\n";
				$class .= "		  ->get();\n";
				$class .= "		\$this->set$nomeAtributo(\$" . $atributo['link']['nome_atributo'] . ");\n";
				$class .= "		return \$this->get$nomeAtributo();\n";
				$class .= "	}\n\n";
				
				//JSON
				$class .= "	/**\n";
				$class .= "	 * Conecta ao objeto $nomeAtributo\n";
				$class .= "	 * @return $nomeAtributo\n";
				$class .= "	 */\n";
				$class .= "	public static function attach".$nomeAtributo."Json(array &\$$lowerName, array \$columns = ['*'])\n";
				$class .= "	{\n";
				$class .= "		if(!isset(\$".$lowerName."['id']) || \$".$lowerName."['id'] == null) throw new Exception('Não foi possível conectar ao objeto $nomeAtributo pois os atributo " . $atributo['nome'] . " é nulo');\n";
				$class .= "		\$" . $atributo['link']['nome_atributo'] . " = " . $atributo['link']['nome'] . "::select()\n";
				$class .= "		  ->columns(\$columns)\n";
				$class .= "		  ->innerJoin('" . $atributo['link']['tabela_associativa'] . "', '" . $atributo['link']['tabela_associativa'] . "." . $id . " = " . $object['nome_tabela'] . ".id')\n";
				$class .= "		  ->where('" . $atributo['link']['tabela_associativa'] . ".id_" . $lowerName . " = ?', \$" . $lowerName . "['id'])\n";
				$class .= "		  ->get(true);\n";
				$class .= "		\$".$lowerName."['" . $atributo['link']['nome_atributo'] . "'] = \$" . $atributo['link']['nome_atributo'] . ";\n";
				$class .= "	}\n\n";
			}
		}
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
		$class .= "	public function save()\n";
		$class .= "	{\n";
		$class .= "		\$this->sanitize();\n\n";
		$class .= "		\$result = false;\n";
		$class .= "		if (\$this->getId() == null) {\n";
		$class .= "			\$result = \\Controle\\" . Controle::getCapitalizedName($json_object['nome']) . "::create(\$this);\n";
		$class .= "			if (\$result) \$this->setId(\$this->getLastId());\n";
		$class .= "		} else {\n";
		$class .= "			\$result = \\Controle\\" . Controle::getCapitalizedName($json_object['nome']) . "::update(\$this);\n";
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
		$class .= "	public function delete(\$bool = false)\n";
		$class .= "	{\n";
		$class .= "		return \\Controle\\" . Controle::getCapitalizedName($json_object['nome']) . "::delete(\$this, \$bool);\n";
		$class .= "	}\n\n";
		// ---

		// del
		$class .= "	/**\n";
		$class .= "	 * Deleta um " . $lowerName . " definitivamente\n";
		$class .= "	 *\n";
		$class .= "	 * @return bool Se for deletado com sucesso\n";
		$class .= "	 * @throws Exception se ocorrer um erro de validacao ou com o banco de dados\n";
		$class .= "	 */\n";
		$class .= "	public function del(\$delete_without_procedure = false)\n";
		$class .= "	{\n";

		if (isset($json_object['delete_procedure']) && $json_object['delete_procedure'] != null) {
			$class .= "		if(\$delete_without_procedure) return parent::delete();\n";
			$class .= "		else return \$this->deleteWithProcedure('" . $json_object['delete_procedure'] . "');\n";
		} else
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
		$class .= "	 * Transforma um array / json em um " . Controle::getCapitalizedName($json_object['nome']) . "\n";
		$class .= "	 *\n";
		$class .= "	 * @return " . Controle::getCapitalizedName($json_object['nome']) . "\n";
		$class .= "	 */\n";
		$class .= "	public static function getObject(array \$json_data)\n";
		$class .= "	{\n";
		$class .= "		\$" . $lowerName . " = new " . Controle::getCapitalizedName($json_object['nome']) . "();\n\n";
		foreach (array_filter($json_object['atributos'], function ($atributo) {
			if (isset($atributo['link'])) return $atributo['tipo'] != "objeto_assoc" && $atributo['link']['tipo'] != "lista";
			else return true;
		}) as $atributo) {
			if ($atributo['tipo'] == "DateTime") $class .= "    if(isset(\$json_data['" . $atributo['nome'] . "'])) \$" . $lowerName . "->set" . Controle::getCapitalizedName($atributo['nome']) . "(new DateTime(\$json_data['" . $atributo['nome'] . "']));\n";
			else if ($atributo['tipo'] == 'int') $class .= "    if(isset(\$json_data['" . $atributo['nome'] . "'])) \$" . $lowerName . "->set" . Controle::getCapitalizedName($atributo['nome']) . "(intval(\$json_data['" . $atributo['nome'] . "']));\n";
			else if ($atributo['tipo'] == 'float') $class .= "    if(isset(\$json_data['" . $atributo['nome'] . "'])) \$" . $lowerName . "->set" . Controle::getCapitalizedName($atributo['nome']) . "(floatval(\$json_data['" . $atributo['nome'] . "']));\n";
			else $class .= "    if(isset(\$json_data['" . $atributo['nome'] . "'])) \$" . $lowerName . "->set" . Controle::getCapitalizedName($atributo['nome']) . "(\$json_data['" . $atributo['nome'] . "']);\n";
		}
		$class .= "\n";

		foreach (array_filter($json_object['atributos'], function ($atributo) {
			return isset($atributo['link']);
		}) as $atributo) {
			if ($atributo['tipo'] == "objeto_assoc" || $atributo['link']['tipo'] == "objeto")
				$class .= "		if(isset(\$json_data['" . $atributo['link']['nome_atributo'] . "'])) $" . $lowerName . "->set" . Controle::getCapitalizedName($atributo['link']['nome']) . "(" . Controle::getCapitalizedName($atributo['link']['nome']) . "::getObject(\$json_data['" . $atributo['link']['nome_atributo'] . "']));\n";
			else if ($atributo['link']['tipo'] == "lista") {
				$class .= "		if(isset(\$json_data['" . $atributo['nome'] . "'])){\n";
				$class .= "			$" . $atributo['nome'] . " = [];\n";
				$class .= "			foreach(\$json_data['" . $atributo['nome'] . "'] as \$object) $" . $atributo['nome'] . "[] = " . Controle::getCapitalizedName($atributo['link']['nome']) . "::getObject(\$object);\n";
				$class .= "			$" . $lowerName . "->set" . Controle::getCapitalizedName($atributo['nome']) . "($" . $atributo['nome'] . ");\n";
				$class .= "		}\n";
			}
		}
		$class .= "		return \$" . $lowerName . ";\n";

		$class .= "	}\n\n";
		// ---

		// getPostObject
		$class .= "	/**\n";
		$class .= "	 * Processa os dados de um \$_POST e retorna um " . Controle::getCapitalizedName($json_object['nome']) . ".\n";
		$class .= "	 *\n";
		$class .= "	 * @return " . Controle::getCapitalizedName($json_object['nome']) . "\n";
		$class .= "	 */\n";
		$class .= "	public static function getPostObject(array \$post_data, int \$id = null, bool \$remove_object = true)\n";
		$class .= "	{\n";
		$class .= "		/**\n";
		$class .= "		 * @var " . Controle::getCapitalizedName($json_object['nome']) . " $" . $lowerName . "\n";
		$class .= "		 */\n";
		$class .= "		if(\$id != null) $" . $lowerName . " = " . Controle::getCapitalizedName($json_object['nome']) . "::select()->where('id = ?', \$id)->get(false, true);\n";
		$class .= "		else $" . $lowerName . " = new " . Controle::getCapitalizedName($json_object['nome']) . "();\n";
		foreach ($json_object['atributos'] as $atributo) {
			if ($atributo['tipo'] == 'float') {
				$class .= "\n		try {\n";
				$class .= "			if (isset(\$post_data['" . $atributo['nome'] . "'])){\n";
				$class .= "				if(strpos(\$post_data['" . $atributo['nome'] . "'], \",\") !== false)\n";
				$class .= "					\$" . $lowerName . "->set" . Controle::getCapitalizedName($atributo['nome']) . "(brl_to_float(\$post_data['" . $atributo['nome'] . "']));\n";
				$class .= "				else\n";
				$class .= "					\$" . $lowerName . "->set" . Controle::getCapitalizedName($atributo['nome']) . "(floatval(\$post_data['" . $atributo['nome'] . "']));\n";
				$class .= "			}\n";
				$class .= "		} catch (Throwable \$th) {\n";
				$class .= "			\$" . $lowerName . "->set" . Controle::getCapitalizedName($atributo['nome']) . "(0.0);\n";
				$class .= "		}\n";
			} else if ($atributo['tipo'] == 'DateTime') {
				$class .= "\n		if (isset(\$post_data['" . $atributo['nome'] . "']) && \$post_data['" . $atributo['nome'] . "'] != null) {\n";
				$class .= "			try {\n";
				$class .= "				\$" . $lowerName . "->set" . Controle::getCapitalizedName($atributo['nome']) . "(new DateTime(\$post_data['" . $atributo['nome'] . "']));\n";
				$class .= "			} catch (Throwable \$th) {\n";
				$class .= "				\$" . $lowerName . "->set" . Controle::getCapitalizedName($atributo['nome']) . "(null);\n";
				$class .= "			}\n";
				$class .= "		}else{\n";
				$class .= "		  \$" . $lowerName . "->set" . Controle::getCapitalizedName($atributo['nome']) . "(null);\n";
				$class .= "		}\n";
			} else if ($atributo['tipo'] == 'int') {
				if (isset($atributo['link'])) {
					$class .= "\n		if(\$remove_object){\n";
					$class .= "		  if (isset(\$post_data['" . $atributo['nome'] . "']) && \$post_data['" . $atributo['nome'] . "'] != null) \$" . $lowerName . "->set" . Controle::getCapitalizedName($atributo['nome']) . "(intval(\$post_data['" . $atributo['nome'] . "']));\n";
					$class .= "		  else \$" . $lowerName . "->set" . Controle::getCapitalizedName($atributo['nome']) . "(null);\n";
					$class .= "		}";
				} else {
					$class .= "\n		if (isset(\$post_data['" . $atributo['nome'] . "'])) \$" . $lowerName . "->set" . Controle::getCapitalizedName($atributo['nome']) . "(intval(\$post_data['" . $atributo['nome'] . "']));\n";
				}
			} else if ($atributo['tipo'] == "objeto_assoc") {
				$class .= "\n		if (isset(\$post_data['" . $atributo['nome'] . "'])) \$" . $lowerName . "->set" . Controle::getCapitalizedName($atributo['nome']) . "(" . Controle::getCapitalizedName($atributo['nome']) . "::getObject(\$post_data['" . $atributo['nome'] . "']));\n";
			} else if (isset($atributo['link']) && $atributo['link']['tipo'] == "lista") {
				$class .= "\n		if(\$remove_object){\n";
				$class .= " 		  if (isset(\$post_data['" . $atributo['nome'] . "'])){\n";
				$class .= " 		  	$" . $atributo['nome'] . " = [];\n";
				$class .= " 		  	foreach(json_decode(\$post_data['" . $atributo['nome'] . "'], true) as \$object) $" . $atributo['nome'] . "[] = " . Controle::getCapitalizedName($atributo['link']['nome']) . "::getObject(\$object);\n";
				$class .= " 		  	\$" . $lowerName . "->set" . Controle::getCapitalizedName($atributo['nome']) . "(\$" . $atributo['nome'] . ");\n";
				$class .= " 		  }else{\n";
				$class .= " 		    \$" . $lowerName . "->set" . Controle::getCapitalizedName($atributo['nome']) . "([]);\n";
				$class .= " 		  }\n";
				$class .= " 	  }\n";
			} else {
				$class .= "\n		if (isset(\$post_data['" . $atributo['nome'] . "'])) \$" . $lowerName . "->set" . Controle::getCapitalizedName($atributo['nome']) . "(\$post_data['" . $atributo['nome'] . "']);\n";
			}
		}
		$class .= "		return $" . $lowerName . ";\n";
		$class .= "	}\n\n";
		// ---

		// getJson
		$class .= "	/**\n";
		$class .= "	 * Transforma um " . Controle::getCapitalizedName($json_object['nome']) . " em um array / json\n";
		$class .= "	 *\n";
		$class .= "	 * @return array\n";
		$class .= "	 */\n";
		$class .= "	public static function getJson(" . Controle::getCapitalizedName($json_object['nome']) . " \$" . $lowerName . ")\n";
		$class .= "	{\n";
		foreach (array_filter($json_object['atributos'], function ($atributo) {
			return isset($atributo['link']);
		}) as $atributo) {
			if ($atributo['tipo'] == "objeto_assoc" || $atributo['link']['tipo'] == 'objeto') {
				$class .= "		\$" . $atributo['link']['nome_atributo'] . " = null;\n";
				$class .= "		if(\$" . $lowerName . "->get" . Controle::getCapitalizedName($atributo['link']['nome']) . "()!=null)\n";
				$class .= "		\$" . $atributo['link']['nome_atributo'] . " = " . Controle::getCapitalizedName($atributo['link']['nome']) . "::getJson(\$" . $lowerName . "->get" . Controle::getCapitalizedName($atributo['link']['nome']) . "());\n\n";
			} else if ($atributo['link']['tipo'] == 'lista') {
				$class .= "		/**\n";
				$class .= "		 * @var " . Controle::getCapitalizedName($atributo['link']['nome']) . "[] \$" . $atributo['nome'] . "\n";
				$class .= "		 */\n";
				$class .= "		\$" . $atributo['nome'] . " = [];\n";
				$class .= "		if(\$" . $lowerName . "->get" . Controle::getCapitalizedName($atributo['nome']) . "()!=null && count(\$" . $lowerName . "->get" . Controle::getCapitalizedName($atributo['nome']) . "()) > 0)\n";
				$class .= "		foreach(\$" . $lowerName . "->get" . Controle::getCapitalizedName($atributo['nome']) . "() as \$json) \$" . $atributo['nome'] . "[] = " . Controle::getCapitalizedName($atributo['link']['nome']) . "::getJson(\$json);\n\n";
			}
		}
		$class .= "		\$json = [\n";
		foreach ($json_object['atributos'] as $atributo) {
			if ($atributo['tipo'] == 'DateTime') {
				$class .= "			\"" . $atributo['nome'] . "\" => (\$" . $lowerName . "->get" . Controle::getCapitalizedName($atributo['nome']) . "() != null) ? \$" . $lowerName . "->get" . Controle::getCapitalizedName($atributo['nome']) . "()->format('Y-m-d') : null,\n";
			} else if ($atributo['tipo'] == "objeto_assoc" || isset($atributo['link']) && $atributo['link']['tipo'] == "lista") {
				$class .= "			\"" . $atributo['nome'] . "\" => \$" . $atributo['nome'] . ",\n";
			} else if (isset($atributo['link']) && $atributo['link']['tipo'] == "objeto") {
				$class .= "			\"" . $atributo['nome'] . "\" => \$" . $lowerName . "->get" . Controle::getCapitalizedName($atributo['nome']) . "(),\n";
				$class .= "			\"" . $atributo['link']['nome_atributo'] . "\" => \$" . $atributo['link']['nome_atributo'] . ",\n";
			} else {
				$class .= "			\"" . $atributo['nome'] . "\" => \$" . $lowerName . "->get" . Controle::getCapitalizedName($atributo['nome']) . "(),\n";
			}
		}
		$class = substr($class, 0, strlen($class) - 2) . "\n";
		$class .= "		];\n";
		$class .= "		return \$json;\n";
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
		$class .= "	 * Remove / Limpa impurezas dos atributos do " . Controle::getCapitalizedName($json_object['nome']) . " \n";
		$class .= "	 *\n";
		$class .= "	 * @return " . Controle::getCapitalizedName($json_object['nome']) . "\n";
		$class .= "	 */\n";
		$class .= "	public function sanitize()\n";
		$class .= "	{\n";
		foreach ($json_object['atributos'] as $atributo) {
			$class .= "		if (\$this->get" . Controle::getCapitalizedName($atributo['nome']) . "() != null) {\n";
			if ($atributo['tipo'] == "DateTime") $class .= "			\$this->set" . Controle::getCapitalizedName($atributo['nome']) . "(new DateTime(sanitize(\$this->get" . Controle::getCapitalizedName($atributo['nome']) . "()->format('Y-m-d H:i:s'))));\n";
			else if ($atributo['tipo'] == "int") $class .= "			\$this->set" . Controle::getCapitalizedName($atributo['nome']) . "(intval(sanitize(\$this->get" . Controle::getCapitalizedName($atributo['nome']) . "())));\n";
			else if ($atributo['tipo'] == "objeto_assoc") $class .= "			\$this->get" . Controle::getCapitalizedName($atributo['nome']) . "()->sanitize();\n";
			else if (isset($atributo['link']) && $atributo['link']['tipo'] == "lista") $class .= "			foreach(\$this->get" . Controle::getCapitalizedName($atributo['nome']) . "() as \$object) \$object->sanitize();\n";
			else $class .= "			\$this->set" . Controle::getCapitalizedName($atributo['nome']) . "(sanitize(\$this->get" . Controle::getCapitalizedName($atributo['nome']) . "()));\n";
			$class .= "		}\n\n";
		}
		$class .= "	}\n\n";
		// ---

		// select
		$class .= "	/**\n";
		$class .= "	 * Usado para criar uma instancia do " . Controle::getCapitalizedName($json_object['nome']) . "\n";
		$class .= "	 * @return " . Controle::getCapitalizedName($json_object['nome']) . "\n";
		$class .= "	 */\n";
		$class .= "	public static function select()\n";
		$class .= "	{\n";
		$class .= "		return new " . Controle::getCapitalizedName($json_object['nome']) . "();\n";
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
			return (($atributo['tipo'] != "objeto_assoc") && ($atributo['nome'] != "deletado") && ((isset($atributo['link']) && $atributo['link']['tipo'] != "lista") || !isset($atributo['link'])));
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
			if ($atributo['tipo'] != "objeto_assoc" && (isset($atributo['link']) && $atributo['link']['tipo'] != "lista") || !isset($atributo['link'])) {
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

			if (isset($atributo['link'])) {
				if ($atributo['tipo'] == "objeto_assoc" || $atributo['link']['tipo'] == "objeto") {
					$lowerName = $atributo['link']['nome_atributo'];
					// get
					$class .= "	/**\n";
					$class .= "	 * Retorna o valor to atributo " . $atributo['link']['nome'] . "\n";
					$class .= "	 *\n";
					$class .= "	 * @return " . $atributo['link']['nome'] . "\n";
					$class .= "	 */\n";
					$class .= "	public function get" . Controle::getCapitalizedName($atributo['link']['nome']) . "()\n";
					$class .= "	{\n";
					$class .= "		return \$this->" . $lowerName . ";\n";
					$class .= "	}\n\n";
					// ---

					// set
					$class .= "	/**\n";
					$class .= "	 * Atribui um valor para o atributo " . $atributo['nome'] . "\n";
					$class .= "	 *\n";
					$class .= "	 * @return self\n";
					$class .= "	 */\n";
					$class .= "	public function set" . Controle::getCapitalizedName($atributo['link']['nome']) . "(" . $atributo['link']['nome'] . " \$$lowerName = null)\n";
					$class .= "	{\n";
					$class .= "		\$this->" . $lowerName . " = $" . $lowerName . ";\n";
					$class .= "		return \$this;\n";
					$class .= "	}\n\n";
					// ---
				} else if ($atributo['link']['tipo'] == "lista") {
					$lowerName = $atributo['link']['nome_atributo'];
					// get
					$class .= "	/**\n";
					$class .= "	 * Retorna o valor to atributo " . $atributo['link']['nome'] . "\n";
					$class .= "	 *\n";
					$class .= "	 * @return " . Controle::getCapitalizedName($atributo['link']['nome']) . "[]\n";
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
					$class .= "	public function set" . Controle::getCapitalizedName($atributo['nome']) . "(array \$" . $atributo['nome'] . " = null)\n";
					$class .= "	{\n";
					$class .= "		\$this->" . $atributo['nome'] . " = $" . $atributo['nome'] . ";\n";
					$class .= "		return \$this;\n";
					$class .= "	}\n\n";
					// ---
				}
			}
		}
		$class .= "	// Encapsulation End\n";
	}

	private static function getQuerybuildReturns(array $json_object, string &$class)
	{
		$lowerName = strtolower($json_object['nome_tabela']);
		$bigName = Controle::getCapitalizedName($json_object['nome_tabela']);
		$class .= "\n";
		$class .= "	// Query Build returns Start\n\n";
		
		$class .= "	/**\n";
		$class .= "	 * @return $bigName\n";
		$class .= "	 */\n";
		$class .= "	public function columns(array \$select_columns){\n";
		$class .= "		parent::columns(\$select_columns);\n";
		$class .= "		return \$this;\n";
		$class .= "	}\n\n";
		
		$class .= "	/**\n";
		$class .= "	 * @return $bigName\n";
		$class .= "	 */\n";
		$class .= "	public function where(\$whereCondition, \$arg = null, \$type = 'and', \$remove_arg = false){\n";
		$class .= "		parent::where(\$whereCondition, \$arg, \$type, \$remove_arg);\n";
		$class .= "		return \$this;\n";
		$class .= "	}\n\n";
		
		$class .= "	/**\n";
		$class .= "	 * @return $bigName\n";
		$class .= "	 */\n";
		$class .= "	public function notIn(\$column, \$select_not, \$type = 'and', \$arg = null){\n";
		$class .= "		parent::notIn(\$column, \$select_not, \$type, \$arg);\n";
		$class .= "		return \$this;\n";
		$class .= "	}\n\n";
		
		$class .= "	/**\n";
		$class .= "	 * @return $bigName\n";
		$class .= "	 */\n";
		$class .= "	public function whereGroup(\DAO\WhereGroup \$whereGroup, string \$type = null){\n";
		$class .= "		parent::whereGroup(\$whereGroup, \$type);\n";
		$class .= "		return \$this;\n";
		$class .= "	}\n\n";
		
		$class .= "	/**\n";
		$class .= "	 * @return $bigName\n";
		$class .= "	 */\n";
		$class .= "	public function orderBy(string \$orderColumn, string \$sortType = 'asc'){\n";
		$class .= "		parent::orderBy(\$orderColumn, \$sortType);\n";
		$class .= "		return \$this;\n";
		$class .= "	}\n\n";
		
		$class .= "	/**\n";
		$class .= "	 * @return $bigName\n";
		$class .= "	 */\n";
		$class .= "	public function limit(int \$limit = null){\n";
		$class .= "		parent::limit(\$limit);\n";
		$class .= "		return \$this;\n";
		$class .= "	}\n\n";
		
		$class .= "	/**\n";
		$class .= "	 * @return $bigName\n";
		$class .= "	 */\n";
		$class .= "	public function setRemoveHour(\$remove_hour){\n";
		$class .= "		parent::setRemoveHour(\$remove_hour);\n";
		$class .= "		return \$this;\n";
		$class .= "	}\n\n";
		
		$class .= "	/**\n";
		$class .= "	 * @return $bigName\n";
		$class .= "	 */\n";
		$class .= "	public function config(array \$config){\n";
		$class .= "		parent::config(\$config);\n";
		$class .= "		return \$this;\n";
		$class .= "	}\n\n";
		
		$class .= "	/**\n";
		$class .= "	 * @return $bigName\n";
		$class .= "	 */\n";
		$class .= "	public function childs(array \$childs){\n";
		$class .= "		parent::childs(\$childs);\n";
		$class .= "		return \$this;\n";
		$class .= "	}\n\n";

		$class .= "\n	// Query Build returns End\n";
	}
}
