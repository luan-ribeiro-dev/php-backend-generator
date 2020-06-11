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
define("ROOT_MODELS", ROOT."/modelos");

define("APP", ROOT_PROJECT . "/app");
define("APP_MODEL", APP . "/modelo");
define("APP_CONTROLE", APP . "/controle");
define("APP_DAO", APP . "/dao");

define("APPLICATION", APP . "/generator");
define("APPLICATION_MODELS", APPLICATION . "/models");

session_start();