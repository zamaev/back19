<?php

namespace Core;

class Route
{
    public function __construct()
    {
        $base_url = $url = urldecode(str_replace('?'.$_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']));
        if (!preg_match('#^(.*)/$#', $url)) {
            $url = $url."/"; // проверка сущестовования страницы при слеше
        }

        $routing = require(ROOT_DIR.'/config/routes.php');

        foreach ($routing as $path => $class) {
            if (!preg_match('#.*/$#', $path)) {
                $path = $path.'/'; // всегда должен быть в конце слеш
            }
            $path = "#^".$path."$#u";
            
            if (preg_match($path, $url, $params)) {
                if (!file_exists(ROOT_DIR.'/app/controller/'.$class.'.php')) {
                    continue;
                }
                require_once(ROOT_DIR.'/app/controller/'.$class.'.php');
                $page = new $class($params);
                if ($page->isset()) {
                    if (!preg_match('#/$#', $base_url)) {
                        header('Location: '.$base_url.'/'); // существующие страницы должны быть только по одной ссылке
                        exit;
                    }
                    return;
                }
            }
        }
        
        require_once(ROOT_DIR.'/app/controller/ErrorPage.php');
        $page = new \ErrorPage($params);
    }
}