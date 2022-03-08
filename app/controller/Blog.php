<?php

class Blog extends View
{
    public function run()
    {
        $this->assign('title', 'All posts');
        $this->assign('menu_item', 'blog');

        $posts = model()->posts->categories->order('post', 'desc')->fetchAll();
        $this->assign('posts', $posts);
    }
}