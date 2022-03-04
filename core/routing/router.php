<?php

class Router
{
    public static function route($url, $routing)
    {
        foreach ($routing as $path => $class) {
            if (preg_match($path, $url, $params)) {
                require_once(__DIR__.'/../../app/controller/'.$class.'.php');
                $page = new $class($params);
                if ($page->isset()) {
                    break;
                }
            }
        }
    }
}