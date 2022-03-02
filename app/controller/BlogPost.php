<?php

class BlogPost extends Page
{
    public function __construct($slug)
    {
        $model = Model::getInstance();
        $post = $model->posts(['slug' => $slug])->fetch();
        
        $this->title = 'Post: '.$post['title'];
        
        $this->vars['title'] = $post['title'];
        $this->vars['content'] = $post['content'];
        $this->vars['delete'] = '/blog/delete/'.$slug.'/';
    }
}