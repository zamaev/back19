<?php

class Page
{
    protected $title;
    protected $content;
    
    public function view()
    {
        $html = file_get_contents($this->template);
        $html = str_replace('{{ title }}', $this->title, $html);
        $html = str_replace('{{ content }}', $this->content, $html);
        return $html;
    }

    public function title()
    {
        return $this->title;
    }
}