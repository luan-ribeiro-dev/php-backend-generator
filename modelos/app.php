<?php

use eftec\bladeone\BladeOne;

//Importa os pacotes do composer
require_once("vendor/autoload.php");

//Importa as classes automaticamente
spl_autoload_register(function ($className) {
  if (strpos($className, "\\") !== false) {
    $className = explode("\\", $className);
    $className = $className[count($className) - 1];
  }
  $directoryNames = ['app/modelo', 'app/controle', 'app/dao', 'layout', 'layout/template'];
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
//---------------------------------

define('VERSION', '1.0.0');

//Criptografia
define('SALT_BEFORE', '');
define('SALT', '');
define('SALT_AFTER', '');
//------------

//Configuracao com o banco de dados
define('HOST', '');
define('DATABASE_NAME', '');
define('LOGIN', '');
define('PASSWORD', '');
//---------------------------------

//Blade Config
define('BLADE_VIEW', "view");
define('BLADE_CACHE', "view/cache");
define('BLADE_OPTION', BladeOne::MODE_SLOW);
//------------
