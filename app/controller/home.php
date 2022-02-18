<?php

class Home extends Page
{
    protected $template = 'app/view/blog.html';

    public function __construct()
    {
        $this->title = 'Home';

        $this->vars['title'] = 'Home page';
        $this->vars['content'] = 'Home content';
    }
}