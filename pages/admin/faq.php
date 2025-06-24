<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/header.php';
$currentUser = getCurrentUser($pdo);
$pageTitle = "Управление FAQ";
$adminFAQ = new AdminFAQ($pdo);

if (!isLoggedIn() || !$currentUser->isAdmin()) {
    redirect('/');
}

redirect('/modules/faq/admin.php');