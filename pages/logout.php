<?php
require_once __DIR__ . '/../config/config.php';

if (isset($_SESSION['user'])) {
    $user = new User($_SESSION['user']);
    $user->logout();
}

redirect('/');