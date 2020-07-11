<?php
class Controle
{

  public static function copy_dir($src, $dst)
  {
    $dir = opendir($src);
    @mkdir($dst);
    while (false !== ($file = readdir($dir))) {
      if (($file != '.') && ($file != '..')) {
        if (is_dir($src . '/' . $file)) {
          Controle::copy_dir($src . '/' . $file, $dst . '/' . $file);
        } else {
          copy($src . '/' . $file, $dst . '/' . $file);
        }
      }
    }
    closedir($dir);
  }

  public static function checkAPIDir()
  {
    if (!is_dir(API)) mkdir(API, 0777, true);
  }

  public static function checkMainDir()
  {
    if (!is_dir(APPLICATION)) mkdir(APPLICATION, 0777, true);
    if (!is_dir(APPLICATION_MODELS)) mkdir(APPLICATION_MODELS, 0777, true);
  }

  public static function checkBackendDirs()
  {
    if (!is_dir(APP_MODEL)) mkdir(APP_MODEL, 0777, true);
    if (!is_dir(APP_CONTROLE)) mkdir(APP_CONTROLE, 0777, true);
    if (!is_dir(APP_DAO)) mkdir(APP_DAO, 0777, true);
  }

  public static function checkAssetsDir()
  {
    if (!is_dir(ASSETS)) mkdir(ASSETS, 0777, true);

    if (!is_dir(ASSETS_JS)) mkdir(ASSETS_JS, 0777, true);
    if (!is_dir(ASSETS_CUSTOM_SCRIPTS)) mkdir(ASSETS_CUSTOM_SCRIPTS, 0777, true);

    if (!is_dir(ASSETS_CSS)) mkdir(ASSETS_CSS, 0777, true);
    if (!is_dir(ASSETS_SCSS)) mkdir(ASSETS_SCSS, 0777, true);
  }

  public static function checkLayoutDir()
  {
    if (!is_dir(LAYOUT)) mkdir(LAYOUT, 0777, true);
  }

  public static function checkRouteDir()
  {
    if (!is_dir(ROUTE)) mkdir(ROUTE, 0777, true);
  }

  public static function checkViewDir()
  {
    if (!is_dir(VIEW)) mkdir(VIEW, 0777, true);
    if (!is_dir(VIEW_LAYOUT)) mkdir(VIEW_LAYOUT, 0777, true);
    if (!is_dir(VIEW_LAYOUT_TEMPLATE)) mkdir(VIEW_LAYOUT_TEMPLATE, 0777, true);
  }

  public static function checkConfigFile()
  {
    if (!is_file(APP . "/config.json")) {
      Controle::copyConfigFile();
      return false;
    }
    return true;
  }

  public static function copyConfigFile()
  {
    copy(ROOT . "/modelos/config.json", APP . "/config.json");
  }

  public static function copyBin()
  {
    copy("../examples/generator_model.json", APPLICATION_MODELS . "/example.json");
  }

  public static function copyApplicationModelJsonExample()
  {
    copy(ROOT . "/examples/generator_model.json", APPLICATION_MODELS . "/example.json");
  }

  public static function copyDaoDefaultClasses()
  {
    copy(ROOT_MODELS . "/ConectaBanco.php", APP_DAO . "/ConectaBanco.php");
    copy(ROOT_MODELS . "/QueryBuilder.php", APP_DAO . "/QueryBuilder.php");
  }

  public static function copyAssetsDefault()
  {
    Controle::copy_dir(ROOT_SCRIPTS, ASSETS_CUSTOM_SCRIPTS);
    Controle::copy_dir(ROOT_STYLES, ASSETS_SCSS . "/custom_styles");
  }

  public static function copyLayoutDefaultClasses()
  {
    Controle::copy_dir(ROOT_MODELS . "/layout_classes", LAYOUT);
  }

  public static function copyRouteExample()
  {
    // copy(ROOT_EXAMPLES . "/route_example.php", ROUTE . "/example.php");
  }

  public static function copyViewDefault()
  {
    copy(ROOT_EXAMPLES."/blade_example.blade.php", VIEW."/example.blade.php");
    Controle::copy_dir(ROOT_LAYOUT_VIEW, VIEW_LAYOUT);
  }

  public static function copyExceptionDefaultClasses()
  {
    copy(ROOT . "/modelos/ValidationException.php", APP_MODEL . "/ValidationException.php");
  }

  public static function copyControllerDefaultClasses()
  {
    copy(ROOT . "/modelos/Geral.php", APP_CONTROLE . "/Geral.php");
    copy(ROOT . "/modelos/Constantes.php", APP_CONTROLE . "/Constantes.php");
    copy(ROOT . "/modelos/API.php", APP_CONTROLE . "/API.php");
  }

  public static function copyAppDefault()
  {
    $config = Controle::getConfig();

    $app = file_get_contents(ROOT_MODELS . "/app.php");
    $app = str_replace("define('VERSION', '')", "define('VERSION', '" . $config['version'] . "')", $app);
    $app = str_replace("define('SALT_BEFORE', '')", "define('SALT_BEFORE', '" . $config['salt_before'] . "')", $app);
    $app = str_replace("define('SALT', '')", "define('SALT', '" . $config['salt'] . "')", $app);
    $app = str_replace("define('SALT_AFTER', '')", "define('SALT_AFTER', '" . $config['salt_after'] . "')", $app);

    $app = str_replace("define('HOST', '');", "define('HOST', '" . $config['database']['caminho'] . "');", $app);
    $app = str_replace("define('DATABASE_NAME', '');", "define('DATABASE_NAME', '" . $config['database']['nome'] . "');", $app);
    $app = str_replace("define('LOGIN', '');", "define('LOGIN', '" . $config['database']['login'] . "');", $app);
    $app = str_replace("define('PASSWORD', '');", "define('PASSWORD', '" . $config['database']['senha'] . "');", $app);

    $arquivo = fopen(ROOT_PROJECT . "/app.php", "w");
    fwrite($arquivo, $app);
  }

  public static function generateModels($override = false)
  {
    ControleModelo::generate($override);
  }

  public static function generateModel(string $nomeObjeto, $override = false)
  {
    ControleModelo::single($nomeObjeto, $override);
  }

  public static function generateControls($override = false)
  {
    ControleOfControls::generate($override);
  }

  public static function generateControl(string $nomeObjeto, $override = false)
  {
    ControleOfControls::single($nomeObjeto, $override);
  }


  public static function generateSQL()
  {
    ControleSQL::generate();
  }

  public static function getCapitalizedName($value)
  {
    return str_replace(" ", "", ucwords(str_replace("_", " ", $value)));
  }

  public static function getSeparateName($value)
  {
    return str_replace("_", " ", $value);
  }

  public static function getConfig()
  {
    $config = file_get_contents(APP . "/config.json");
    $json = json_decode($config, true);
    return $json;
  }

  public static function checkDirs()
  {
    Controle::checkAPIDir();
    Controle::checkMainDir();
    Controle::checkBackendDirs();
    Controle::checkAssetsDir();
    Controle::checkLayoutDir();
    // Controle::checkRouteDir();
    Controle::checkViewDir();
  }
}
