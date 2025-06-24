<?php
require_once __DIR__ . '/../includes/functions.php';
// Затем конфигурацию
require_once __DIR__ . '/../config/config.php';
// И только потом хедер
require_once __DIR__ . '/../includes/header.php';

$pageTitle = "Вход в систему";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    try {
        $user = User::findByUsername($pdo, $username);

        if (!$user) {
            throw new Exception("Пользователь с таким именем не найден");
        }

        if ($user->isBanned()) {
            throw new Exception("Ваш аккаунт заблокирован");
        }

        if ($user->login($password)) {
            $_SESSION['user'] = [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'role' => $user->getRole()
            ];
            redirect('/');
        } else {
            throw new Exception("Неверный пароль");
        }
    } catch (Exception $e) {
        displayError($e->getMessage());
    }
}
?>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="text-center mb-4">Вход в систему</h2>
            <form method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Имя пользователя</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Пароль</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Войти</button>
            </form>
            <div class="mt-3 text-center">
                <p>Ещё нет аккаунта? <a href="/pages/register.php">Зарегистрируйтесь</a></p>
            </div>
        </div>
    </div>

