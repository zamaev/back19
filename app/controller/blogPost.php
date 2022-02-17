<?php

class BlogPost extends Page
{
    protected $template = 'app/view/blogPost.html';

    public function __construct($param)
    {
        $model = Model::getInstance();
        list('title' => $this->title, 'content' => $this->content) = $model->posts($param)->fetch();
    }
}