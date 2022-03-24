<?php

namespace Core;

require_once 'core/libs/smarty/Smarty.class.php';

class Controller extends \Smarty
{
    protected $isset = true;

    public function __construct($params)
    {
        parent::__construct();
        $this->template_dir = ROOT_DIR.'/app/view/';
        $this->compile_dir = ROOT_DIR.'/cache/view/';
        $this->config_dir = ROOT_DIR.'/config/';
        $this->cache_dir = ROOT_DIR.'/cache/smarty/';

        $this->run($params);
        
        if ($this->isset) {
            $this->assign('template', static::class);
            $this->assign('user', Auth\Auth::getUser());
            $this->display('Layout.html');
        }
    }

    public function isset()
    {
        return $this->isset;
    }
}