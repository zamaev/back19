<?php

use Core\Controller;

class Register extends Controller
{
    public function run()
    {
        $this->assign('title', 'Register');
        $this->assign('menu_item', 'register');

        if (!empty($_POST)) {
            $name = $_POST['name'] ?? null;
            $login = $_POST['login'] ?? null;
            $password = $_POST['password'] ?? null;
            $confirm = $_POST['confirm'] ?? null;
            
            if ($name && $login && $password && $confirm) {
                if ($password !== $confirm) {
                    $this->assign('error', 'Passwords do not match');

                } else if (!Core\Auth\Auth::register($login, $password, $name)) {
                    $this->assign('error', 'User is exist');
                }
            } else {
                $this->assign('error', 'Empty fields');
            }

            $this->assign('name', $name);
            $this->assign('login', $login);
        }

    }
}