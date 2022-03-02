<?php

class Page
{
    protected $title;
    protected $vars = [];

    public function view()
    {
        $html = file_get_contents(__DIR__.'/../../app/view/'.static::class.'.html');
        foreach ($this->vars as $var => $value) {
            $html = str_replace("{{ {$var} }}", $value, $html);
        }
        preg_match_all('#{{ [0-9a-z_-]* }}#', $html, $match);
        foreach ($match as $var) {
            $html = str_replace($var, '', $html);
        }
        return $html;
    }

    public function render()
    {
        $html = file_get_contents(__DIR__.'/../../app/view/'.self::class.'.html');
        $html = str_replace('{{ title }}', $this->title, $html);
        $html = str_replace('{{ content }}', $this->view(), $html);
        echo $html;
    }

    public function __destruct()
    {
        $this->render();
    }
}