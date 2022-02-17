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
if (!preg_match('#/$#', $url)) {
    header('Location: '.$url.'/');
}

$html = file_get_contents('app/view/layout.html');


if (preg_match('#/blog/$#', $url)) {
    require('app/controller/blog.php');
    $page = new Blog();

} else if (preg_match('#/blog/([0-9a-z_-])#', $url, $params)) {
    require('app/controller/blogPost.php');
    $page = new BlogPost($params[1]);

} else {
    require('app/controller/errorPage.php');
    $page = new ErrorPage();

}


$html = str_replace('{{ title }}', $page->title(), $html);
$html = str_replace('{{ content }}', $page->view(), $html);

echo $html;

exit;

$url_arr = explode('/', $url);

$title = '';
$content = '';

switch ($url_arr[1]) {
    case 'blog': 
        var_dump(preg_match('#/blog/([0-9a-z_-]+)#', $url, $match));

        if (isset($match[1]) && $post = $model->posts(['slug' => $match[1]])->entity()) {
            $title = $post->title;
            $content = $post->content;
            $template = file_get_contents('app/view/blog.html');
            $content = str_replace('{{ post }}', $content, $template);


        } else {
            $content = file_get_contents('app/view/404.html');
            preg_match('#{{ title: "(.+)" }}#', $content, $match);
            $title = $match[1] ?? '';
            $content = preg_replace('#{{ title: "(.+)" }}#', '', $content);
        }

        break;

    default: 
        $file = 'app/view/404.html';
        if ($url == '/') {
            $file = 'app/view/index.html';
        } else if (file_exists('app/view/'.$url.'.html')) {
            $file = 'app/view/'.$url.'.html';
        } else {
            header('HTTP/1.0 404 Not Found');
        }
        
        $content = file_get_contents($file);
        preg_match('#{{ title: "(.+)" }}#', $content, $match);
        $title = $match[1] ?? '';
        $content = preg_replace('#{{ title: "(.+)" }}#', '', $content);
}



$html = str_replace('{{ title }}', $title, $html);
$html = str_replace('{{ content }}', $content, $html);



echo $html;

// require_once 'core/classes/session.php';
// require_once 'core/auth/auth.php';
