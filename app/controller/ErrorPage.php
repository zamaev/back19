<?php

use Core\Controller;

class ErrorPage extends Controller
{
    public function run()
    {
        header("HTTP/1.1 404 Not Found");
        $this->assign('title', '404');
        $this->assign('content', 'Page not found');
    }
}