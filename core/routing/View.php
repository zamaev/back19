<?php

class View extends Smarty
{
    protected $isset = true;

    public function __construct($params)
    {
        parent::__construct();
        $this->template_dir = APP_DIR.'/app/view/';
        $this->compile_dir = APP_DIR.'/cache/view/';
        $this->config_dir = APP_DIR.'/config/';
        $this->cache_dir = APP_DIR.'/cache/smarty/';

        $this->run($params);

        if ($this->isset) {
            $this->assign('template', static::class);
            $this->display('Layout.html');
        }
    }

    public function isset()
    {
        return $this->isset;
    }
}