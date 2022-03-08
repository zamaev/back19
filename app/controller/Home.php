<?php

class Home extends View
{
    public function run()
    {        
        //** раскомментируйте следующую строку для отображения отладочной консоли
        // $this->force_compile = true;
        // $this->debugging = true;
        // $this->caching = true;
        // $this->cache_lifetime = 120;

        $this->assign('title', 'Home');
        $this->assign('menu_item', 'home');

        $this->assign('text', 'Тут будет контент главной страниуцы');
    }
}