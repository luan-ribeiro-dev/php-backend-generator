<?php
require_once 'vendor/autoload.php';
spl_autoload_register(function ($className) {
	$directoryNames = ['comandos', 'controle'];
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

define("ROOT_PROJECT", dirname(__DIR__));
define("ROOT", __DIR__);

define("BACKEND", ROOT_PROJECT . "/backend");
define("BACKEND_MODEL", BACKEND . "/modelo");
define("BACKEND_CONTROLE", BACKEND . "/controle");
define("BACKEND_DAO", BACKEND . "/dao");

define("APPLICATION", BACKEND . "/app/generator");
define("APPLICATION_MODELS", APPLICATION . "/models");