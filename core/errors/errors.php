<?php

$settings = require('config/settings.php');

if ($settings['debug'] || $_GET['debug'] == 1) {
    error_reporting(E_ALL);
    ini_set('display_errors', 'on');
    ini_set('display_startup_errors', 'on');

} else {
    error_reporting(0);
    ini_set('display_errors', 'off');
    ini_set('display_startup_errors', 'off');
}

// так же сделать зедсь красивый вывод сообщений, у мне где-то была функция на GitHub которая обрабатывала все ошибки вроде