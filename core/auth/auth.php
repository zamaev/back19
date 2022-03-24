<?php

namespace Core\Auth;

class Auth
{
    public static function auth($login, $password)
    {
        if ($user = model()->users->where(['login' => $login])->fetch()) {

            if (password_verify($password, $user['password'])) {
                $_SESSION['user'] = $user['user'];
                header('Location: /');
            }
        }
        return false;
    }

    public static function register($login, $password, $name) {
        if (!model()->user(['login' => $login])) {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            model()->users->insert(['login' => $login, 'password' => $password_hash, 'name' => $name]);
            self::auth($login, $password);
        }
        return false;
    }

    public static function isAuth()
    {
        if (self::getUser()) {
            return true;
        } else {
            return false;
        }
    }

    public static function getUser()
    {
        if (isset($_SESSION['user'])) {
            return model()->user($_SESSION['user']);
        }
        return null;
    }
}