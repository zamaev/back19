<?php

class BlogPostDelete
{
    public function __construct($params)
    {
        $slug = $params['slug'];
        
        $model = Model::getInstance();
        $post = $model->post(['slug' => $slug]);
        if ($post) {
            $post->delete();
        }
        header('Location: /blog/');
    }
}