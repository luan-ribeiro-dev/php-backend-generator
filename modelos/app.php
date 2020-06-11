<?php

spl_autoload_register(function ($className) {
  if (strpos($className, "\\") !== false) {
    $className = explode("\\", $className);
    $className = $className[count($className) - 1];
  }
  $directoryNames = ['app/modelo', 'app/controle', 'app/dao'];
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

define('SALT_BEFORE', '');
define('SALT', '');
define('SALT_AFTER', '');

define('HOST', '');
define('DATABASE_NAME', '');
define('LOGIN', '');
define('PASSWORD', '');

define('VERSION', '1.0.0');