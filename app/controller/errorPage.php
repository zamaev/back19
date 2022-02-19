<?php

class ErrorPage extends Page
{   
    protected $template = 'app/view/404.html';

    public function __construct()
    {
        $this->title = '404';
                
        $this->vars['title'] = '404';
        $this->vars['content'] = 'Page not found';
    }

}