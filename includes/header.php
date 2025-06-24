<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'CMS с модулем FAQ' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="/">CMS FAQ</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/">Главная</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/modules/faq/">FAQ</a>
                </li>
                <?php if (isLoggedIn() && getCurrentUser($pdo)->isAdmin()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/pages/admin/">Админ-панель</a>
                    </li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <?php if (!isLoggedIn()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/pages/login.php">Вход</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/pages/register.php">Регистрация</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <span class="nav-link"><?= htmlspecialchars($_SESSION['user']['username']) ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/pages/logout.php">Выход</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<div class="container mt-4">