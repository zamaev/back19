<?php

class Home extends Page
{
    protected $template = 'app/view/home.html';

    public function __construct()
    {
        $this->title = 'Home';
    }
}