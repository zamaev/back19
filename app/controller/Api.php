<?php

class Api
{
    private $isset = true;

    public function __construct($params)
    {
        $path = __DIR__.'/api/'.$params['path'].'.php';

        if (file_exists($path)) {
            require_once($path);
        } else {
            $this->isset = false;
        }
    }

    public function isset()
    {
        return $this->isset;
    }
}
