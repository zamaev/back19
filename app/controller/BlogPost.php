<?php

class BlogPost extends Page
{
    public function __construct($params)
    {
        $category = $params['category'];
        $post = $params['post'];

        $model = Model::getInstance();
        $post = $model->posts->categories->where(['category.slug' => $category, 'post.slug' => $post])->fetch();

        if ($post) {
            $this->title = $post['post.title'];
        
            $this->vars['title'] = $post['post.title'];
            $this->vars['content'] = $post['post.content'];
            $this->vars['delete'] = '/blog/delete/'.$post['post.slug'].'/';
        
        } else {
            $this->isset = false;
        }
    }
}