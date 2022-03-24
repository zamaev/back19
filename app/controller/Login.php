<?php

use Core\Controller;

class Login extends Controller
{
    public function run()
    {
        if (Core\Auth\Auth::isAuth()) {
            header('Location: /');
        }

        $this->assign('title', 'Login');
        $this->assign('menu_item', 'login');

        if (!empty($_POST)) {
            $login = $_POST['login'] ?? null;
            $password = $_POST['password'] ?? null;

            if ($login && $password) {
                Core\Auth\Auth::auth($login, $password);
            }

            $this->assign('error', true);
            $this->assign('login', $login);
            $this->assign('password', $password);
        }

    }
}