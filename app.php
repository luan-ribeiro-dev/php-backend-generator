<?php
require_once 'vendor/autoload.php';
spl_autoload_register(function ($className) {
	$directoryNames = ['commands'];
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