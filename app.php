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

define("API", ROOT_PROJECT . "/api");

define("APP", ROOT_PROJECT . "/app");
define("APP_MODEL", APP . "/modelo");
define("APP_CONTROLE", APP . "/controle");
define("APP_DAO", APP . "/dao");

define("VIEW", ROOT_PROJECT . "/view");
define("VIEW_LAYOUT", VIEW . "/layout");
define("VIEW_LAYOUT_TEMPLATE", VIEW_LAYOUT . "/template");

define("ASSETS", ROOT_PROJECT . "/assets");
define("ASSETS_JS", ASSETS . "/js");
define("ASSETS_CUSTOM_SCRIPTS", ASSETS_JS . "/custom_scripts");
define("ASSETS_CSS", ASSETS . "/css");
define("ASSETS_SCSS", ASSETS . "/scss");
define("ASSETS_CUSTOM_STYLES", ASSETS . "/custom_styles");

define("LAYOUT", ROOT_PROJECT . "/layout");

define("ROUTE", ROOT_PROJECT . "/routes");

define("APPLICATION", APP . "/generator");
define("APPLICATION_MODELS", APPLICATION . "/models");

define("ROOT", __DIR__);
define("ROOT_EXAMPLES", ROOT."/examples");
define("ROOT_MODELS", ROOT."/modelos");
define("ROOT_LAYOUT_CLASSES", ROOT_MODELS."/layout_classes");
define("ROOT_LAYOUT_VIEW", ROOT_MODELS."/layout_view");
define("ROOT_SCRIPTS", ROOT_MODELS."/custom_scripts");
define("ROOT_STYLES", ROOT_MODELS."/custom_styles");

session_start();