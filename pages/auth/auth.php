<?php
require_once 'core/auth/auth.class.php';

session_start();

if (!empty($_POST['user']) && !empty($_POST['password'])) {
    $_SESSION['user'] = $_POST['user'];
    header('Location: ./auth.php');
}

if (!isset($_SESSION['user'])) {
    ?>
        <form action="" method="post">
            User: <input name="user">
            <br>
            Password: <input type="password" name="password">
            <br>
            <input type="submit" value="Auth">
        </form>
    <?php

} else {
    ?>
        Hello <?=$_SESSION['user']?>
        <br>
        <a href="./exit.php">Exit</a>

    <?php
}

