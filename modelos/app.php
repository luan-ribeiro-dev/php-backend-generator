<?php

use eftec\bladeone\BladeOne;

date_default_timezone_set('America/Sao_Paulo');

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

define("VERSION", "1.0.0");
//CORE_VERSION: 1.0
define("DEFAULT_PASSWORD", "truckcontrol123");
 
//Criptografia
  define('SALT_BEFORE', 'J"Sq?NY!h+:}33^/');
  define('SALT', 'nTbD"$crna(M/9zH'); 
  define('SALT_AFTER', '(P43SWQ?y/s7H,e4');
//------------

//Configuracao com o banco de dados
  define('HOST', "localhost");
  define('DATABASE_NAME', "");
  define('LOGIN', "");
  define('PASSWORD',"");
  // define('LOGIN', "root");
  // define('PASSWORD', "");
//---------------------------------

//Blade Config
  define('BLADE_VIEW', "view");
  define('BLADE_CACHE', "view/cache");
  define('BLADE_OPTION', BladeOne::MODE_SLOW);
//------------

// $include_path = "";
// if(strpos(get_include_path(), "../") !== false) $include_path = get_include_path();
// if(!is_dir($include_path."assets/blah")) mkdir($include_path."assets/blah/");

//Email Config
//------------

//Debug Config
  define('DEBUG', false);
//------------