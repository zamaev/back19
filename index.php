<?php

spl_autoload_register();

const ROOT_DIR = __DIR__;

require_once 'core/tools/errors.php';
require_once 'core/tools/debug.php';

function model($table = null) { 
	if ($table) {
		return Core\Model\Model::getInstance()->{$table};
	} else {
		return Core\Model\Model::getInstance(); 
	}
}

new Core\Route;
