<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/header.php';

if (!isLoggedIn()) {
    redirect('/pages/login.php');
}

$currentUser = getCurrentUser($pdo); // Инициализируем текущего пользователя

if (!$currentUser->isAdmin()) {
    redirect('/');
}

$pageTitle = "Административная панель";
?>

    <h1>Административная панель</h1>

    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Управление FAQ</h5>
                    <p class="card-text">Модерация вопросов, добавление тем, блокировка пользователей.</p>
                    <a href="/pages/admin/faq.php" class="btn btn-primary">Перейти</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Пользователи</h5>
                    <p class="card-text">Просмотр и управление пользователями системы.</p>
                    <a href="/pages/admin/users.php" class="btn btn-primary">Перейти</a>
                </div>
            </div>
        </div>
    </div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>