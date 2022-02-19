<?php

class Page
{
    protected $title;
    protected $vars = [];
    
    public function title()
    {
        return $this->title;
    }

    public function view()
    {
        $html = file_get_contents($this->template);
        foreach ($this->vars as $var => $value) {
            $html = str_replace("{{ {$var} }}", $value, $html);
        }
        preg_match_all('#{{ [0-9a-z_-]* }}#', $html, $match);
        foreach ($match as $var) {
            $html = str_replace($var, '', $html);
        }
        return $html;
    }
}