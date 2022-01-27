<?php

session_start();

if (!isset($_SESSION['date'])) {
    
}

print_r($_SESSION['date']);

// unset($_SESSION['date']);