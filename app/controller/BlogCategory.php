<?php

use Core\Controller;

class BlogCategory extends Controller
{
    public function run($params)
    {
        $this->assign('menu_item', 'blog');
        
        // $this->assign('title', $category['title']);

        $this->isset = false;
    }
}