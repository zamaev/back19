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

// эту штуку нужно отменить на файлах, чтобы не было редиректа
if (!preg_match('#/$#', $url)) {
    header('Location: '.$url.'/');
}


if (preg_match('#^/$#', $url)) {
    require('app/controller/home.php');
    $page = new Home();

} else if (preg_match('#/blog/$#', $url)) {
    require('app/controller/blog.php');
    $page = new Blog();

} else if (preg_match('#/blog/add/#', $url)) {
    require('app/controller/blogPostAdd.php');
    $page = new BlogPostAdd();

} else if (preg_match('#/blog/edit/(?<slug>.+)/#', $url, $params)) {

} else if (preg_match('#/blog/delete/(?<slug>.+)/#', $url, $params)) {
    require('app/controller/blogPostDelete.php');
    new BlogPostDelete($params['slug']);

} else if (preg_match('#/blog/(?<slug>.+)/#', $url, $params)) {
    require('app/controller/blogPost.php');
    $page = new BlogPost($params['slug']);

} else {
    require('app/controller/errorPage.php');
    $page = new ErrorPage();
}


$html = file_get_contents('app/view/layout.html');
$html = str_replace('{{ title }}', $page->title(), $html);
$html = str_replace('{{ content }}', $page->view(), $html);

echo $html;

// require_once 'core/classes/session.php';
// require_once 'core/auth/auth.php';
