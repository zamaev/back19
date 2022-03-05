<?php
require_once 'core/tools/errors.php';
require_once 'core/tools/debug.php';
require_once 'core/tools/cache.php';

require_once 'core/patterns/singleton.php';

require_once 'core/model/entity.php';
require_once 'core/model/query.php';
require_once 'core/model/model.php';

require_once 'core/routing/page.php';
require_once 'core/routing/router.php';


$url = $_SERVER['REQUEST_URI'];
$url = str_replace('?'.$_SERVER['QUERY_STRING'], '', $url);
$url = urldecode($url);
if (!preg_match('#/$#', $url)) {
    header('Location: '.$url.'/');
}


$routing = require('config/routing.php');
Router::route($url, $routing);


$model = Model::getInstance();
debug($model->post(1)->category->id);



// require_once 'core/classes/session.php';
// require_once 'core/auth/auth.php';
