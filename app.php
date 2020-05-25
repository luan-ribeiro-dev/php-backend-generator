<?php
require_once 'vendor/autoload.php';
spl_autoload_register(function ($className) {
	$directoryNames = ['commands', 'controller'];
	$includedClass = false;

	foreach ($directoryNames as $directoryName) {
		$path = __DIR__ . DIRECTORY_SEPARATOR . $directoryName . DIRECTORY_SEPARATOR . $className . '.php';
		if (file_exists($path) && !is_dir($path)) {

			include_once($path);
			$includedClass = true;
		};
	}

	if (!$includedClass) {
		trigger_error("Não foi possível incluir {$className}.php", E_USER_ERROR);
		die;
	}
});

define("ROOT", dirname(__DIR__));

define("BACKEND", ROOT . "/backend");
define("BACKEND_MODEL", BACKEND . "/modelo");
define("BACKEND_CONTROLE", BACKEND . "/controle");
define("BACKEND_DAO", BACKEND . "/dao");

define("APPLICATION_DIR", BACKEND . "/app/generator");
define("GENERATOR_DATABASE_MODELS_DIR", APPLICATION_DIR . "/models");

