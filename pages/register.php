<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/header.php';

$pageTitle = "Регистрация";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $email = trim($_POST['email'] ?? '');

    try {
        $guest = new Guest();
        $user = $guest->register($pdo, $username, $password, $email);

        $_SESSION['user'] = [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'role' => $user->getRole()
        ];

        displaySuccess("Регистрация прошла успешно!");
        redirect('/');
    } catch (Exception $e) {
        displayError($e->getMessage());
    }
}
?>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="text-center mb-4">Регистрация</h2>
            <form method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Имя пользователя</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Пароль</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Зарегистрироваться</button>
            </form>
            <div class="mt-3 text-center">
                <p>Уже есть аккаунт? <a href="/pages/login.php">Войдите</a></p>
            </div>
        </div>
    </div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>