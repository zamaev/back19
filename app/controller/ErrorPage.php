<?php

class ErrorPage extends Page
{
    public function __construct()
    {
        $this->title = '404';
                
        $this->vars['title'] = '404';
        $this->vars['content'] = 'Page not found';
    }

}