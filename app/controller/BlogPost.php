<?php

use Core\Controller;

class BlogPost extends Controller
{
    public function run($params)
    {
        $post = model()->posts->categories->where(['category.slug' => $params['category'], 'post.slug' => $params['post']])->fetch();

        if ($post) {        
            $this->assign('title', $post['post.title']);
            $this->assign('menu_item', 'blog');

            $this->assign('content', $post['post.content']);
            $this->assign('thumb', $post['post.thumb']);
            $this->assign('edit', '/blog/edit/'.$post['post.slug'].'/');
            $this->assign('delete', '/blog/delete/'.$post['post.slug'].'/');
        
        } else {
            $this->isset = false;
        }
    }
}