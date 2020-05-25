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
		if (!is_dir(BACKEND_MODEL)) mkdir(BACKEND_MODEL, 0777, true);
		if (!is_dir(BACKEND_CONTROLE)) mkdir(BACKEND_CONTROLE, 0777, true);
		if (!is_dir(BACKEND_DAO)) mkdir(BACKEND_DAO, 0777, true);
	}

	public static function generateAll()
	{
		Controle::checkMainDir();
		Controle::checkBackendDirs();
		
		// Controle::copyBin();
		Controle::copyApplicationModelJsonExample();
		Controle::generateModels();
	}

	public static function copyBin()
	{
		copy("../examples/generator_model.json", APPLICATION_MODELS . "/example.json");
	}

	public static function copyApplicationModelJsonExample()
	{
		copy(ROOT."/examples/generator_model.json", APPLICATION_MODELS . "/example.json");
	}

	public static function generateModels()
	{
		ControleModelo::generate();
	}
}
