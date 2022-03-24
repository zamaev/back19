<?php

class Logout
{
    public function __construct()
    {
        /**
         * сделать не session_destroy а удалять активного пользователя,
         * это чтобы можно было авторизоваться нескольких пользователях и переключаться между ними.
         * А в от если выбрано выйти из всех пользователей, то тогда уже session_destroy()
         */
        session_destroy();
        header('Location: /');
    }
}