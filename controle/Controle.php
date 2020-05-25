<?php
class Controle{
	public static function checkMainDir(){
		if (!is_dir(APPLICATION_DIR)) mkdir(APPLICATION_DIR, 0777, true);
	}

	public static function checkBackendDirs(){
		if (!is_dir(BACKEND_MODEL)) mkdir(APPLICATION_DIR, 0777, true);
		if (!is_dir(BACKEND_CONTROLE)) mkdir(APPLICATION_DIR, 0777, true);
		if (!is_dir(BACKEND_DAO)) mkdir(APPLICATION_DIR, 0777, true);
	}

	public static function generateAll(){
		Controle::checkMainDir();
	}
}