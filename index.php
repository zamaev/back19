<?php

spl_autoload_register();
session_start();

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

// каждый раз обращаться к базе не круто, нужно переносить в сессию нужную инфу
function user() {
	return Core\Auth\Auth::getUser();
}

new Core\Route;
