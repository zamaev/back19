<?php

class Router
{
    public static function route()
    {
        $url = urldecode(str_replace('?'.$_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']));
        $routing = require('config/routing.php');
        
        foreach ($routing as $path => $class) {
            if (preg_match($path, $url, $params)) {
                require_once(__DIR__.'/../../app/controller/'.$class.'.php');
                $page = new $class($params);
                if ($page->isset()) {
                    if (!preg_match('#/$#', $url)) {
                        header('Location: '.$url.'/'); // существующие страницы должны быть только по одной ссылке
                        exit;
                    }
                    return;
                }
            }
        }
        
        require_once(__DIR__.'/../../app/controller/ErrorPage.php');
        $page = new ErrorPage($params);
    }
}