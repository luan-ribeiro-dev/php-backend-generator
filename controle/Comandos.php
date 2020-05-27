<?php

class Comandos
{

	public static function command($callback)
	{
		Controle::checkDirs();

		if (Controle::checkConfigFile()) {
			$callback();
		} else {
			echo "O arquivo de configuração foi gerado em app/config.json, verifique os dados e execute o comando novamente";
		}
	}

	public static function generateAll()
	{
		Comandos::command(function () {
			Controle::copyApplicationModelJsonExample();
			Controle::copyDaoDefaultClasses();
			Controle::copyExceptionDefaultClasses();
			Controle::copyControllerDefaultClasses();
			Controle::copyAppDefault();
			Controle::generateModels(true);
			Controle::generateControls(true);
			// Controle::generateSQL();
		});
	}

	public static function generateConfig()
	{
		Controle::checkDirs();
		Controle::copyConfigFile();
	}

	public static function generateExampleModel()
	{
		Comandos::command(function () {
			Controle::copyApplicationModelJsonExample();
		});
	}

	public static function generateDaoDefaultClasses()
	{
		Comandos::command(function () {
			Controle::copyDaoDefaultClasses();
		});
	}

	public static function generateExceptionDefaultClasses()
	{
		Comandos::command(function () {
			Controle::copyExceptionDefaultClasses();
		});
	}

	public static function generateControllerDefaultClasses()
	{
		Comandos::command(function () {
			Controle::copyControllerDefaultClasses();
		});
	}

	public static function generateAppDefault()
	{
		Comandos::command(function () {
			Controle::copyAppDefault();
		});
	}

	public static function generateModels($override = false)
	{
		Comandos::command(function () use ($override) {
			Controle::generateModels($override);
		});
	}

	public static function generateControls($override = false)
	{
		Comandos::command(function () use ($override) {
			Controle::generateControls($override);
		});
	}

	public static function generateModel(string $nomeObjeto, $override = false)
	{
		Comandos::command(function () use ($nomeObjeto, $override) {
			Controle::generateModel($nomeObjeto, $override);
		});
	}

	public static function generateControl(string $nomeObjeto, $override = false)
	{
		Comandos::command(function () use ($nomeObjeto, $override) {
			Controle::generateControl($nomeObjeto, $override);
		});
	}

	public static function generateObjectClasses(string $nomeObjeto, $override = false)
	{
		Comandos::command(function () use ($nomeObjeto, $override) {
			Controle::generateModel($nomeObjeto, $override);
			Controle::generateControl($nomeObjeto, $override);
		});
	}
	
	public static function generateSQL()
	{
		Comandos::command(function (){
			Controle::generateSQL();
		});
	}
}
