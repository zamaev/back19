<?php

class BlogCategory extends View
{
    public function run($params)
    {
        $this->assign('menu_item', 'blog');
        
        // $this->assign('title', $category['title']);
    }
}