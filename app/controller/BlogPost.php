<?php

class BlogPost extends Page
{
    public function __construct($params)
    {
        $slug = $params['slug'];

        $model = Model::getInstance();
        $post = $model->posts(['slug' => $slug])->fetch();

        if ($post) {
            $this->title = 'Post: '.$post['title'];
        
            $this->vars['title'] = $post['title'];
            $this->vars['content'] = $post['content'];
            $this->vars['delete'] = '/blog/delete/'.$slug.'/';
        
        } else {
            $this->isset = false;
        }
    }
}