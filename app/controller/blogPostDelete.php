<?php

class BlogPostDelete
{
    public function __construct($slug)
    {
        $model = Model::getInstance();
        $model->post(['slug' => $slug])->delete();
        header('Location: /blog/');
    }
}