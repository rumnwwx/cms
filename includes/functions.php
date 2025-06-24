<?php
function isLoggedIn() {
    return isset($_SESSION['user']);
}

function getCurrentUser($pdo) {
    if (isLoggedIn()) {
        return User::findById($pdo, $_SESSION['user']['id']);
    }
    return new Guest();
}

function displayError($message) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($message) . '</div>';
}

function displaySuccess($message) {
    echo '<div class="alert alert-success">' . htmlspecialchars($message) . '</div>';
}

function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);

    if (empty($text)) {
        return 'n-a';
    }

    return $text;
}