<?php

use \Core\Controller;

class BlogPostDelete
{
    public function __construct($params)
    {
        $post = model()->post(['slug' => $params['slug']]);
        if ($post) {
            $post->delete();
        }
        header('Location: /blog/');
    }
}