<?php
require_once 'core/tools/errors.php';
require_once 'core/tools/debug.php';
require_once 'core/tools/cache.php';

require_once 'core/patterns/singleton.php';

require_once 'core/model/entity.php';
require_once 'core/model/query.php';
require_once 'core/model/model.php';

require_once 'core/routing/page.php';


$url = $_SERVER['REQUEST_URI'];
$url = str_replace('?'.$_SERVER['QUERY_STRING'], '', $url);
$url = urldecode($url);
if (!preg_match('#/$#', $url)) {
    header('Location: '.$url.'/');
}


if (preg_match('#^/$#', $url)) {
    require('app/controller/Home.php');
    new Home();

} else if (preg_match('#/blog/$#', $url)) {
    $route = 'Blog';
    require('app/controller/'.$route.'.php');
    new $route();

} else if (preg_match('#/blog/add/#', $url)) {
    require('app/controller/BlogPostAdd.php');
    new BlogPostAdd();

} else if (preg_match('#/blog/edit/(?<slug>.+)/#', $url, $params)) {

} else if (preg_match('#/blog/delete/(?<slug>.+)/#', $url, $params)) {
    require('app/controller/BlogPostDelete.php');
    new BlogPostDelete($params['slug']);

} else if (preg_match('#/blog/(?<slug>.+)/#', $url, $params)) {
    require('app/controller/BlogPost.php');
    new BlogPost($params['slug']);

} else {
    require('app/controller/ErrorPage.php');
    new ErrorPage();
}



// require_once 'core/classes/session.php';
// require_once 'core/auth/auth.php';
