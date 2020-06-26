<?php
class Controle
{
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
    $config = Controle::getConfig();

    $database = file_get_contents(ROOT_MODELS . "/ConectaBanco.php");

    $arquivo = fopen(APP_DAO . "/ConectaBanco.php", "w");
    fwrite($arquivo, $database);

    copy(ROOT_MODELS . "/QueryBuilder.php", APP_DAO . "/QueryBuilder.php");
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
    Controle::checkMainDir();
    Controle::checkBackendDirs();
  }
}
