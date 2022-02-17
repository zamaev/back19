<?php

class ErrorPage extends Page
{   
    protected $template = 'app/view/404.html';

    public function __construct()
    {
        $model = Model::getInstance();
        $this->title = '404';
        $this->content = 'Page not found';
    }

}