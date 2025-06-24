<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/header.php';

$currentUser = getCurrentUser($pdo);
$pageTitle = "Управление пользователями";

// Проверка прав доступа
if (!isLoggedIn() || !$currentUser->isAdmin()) {
    redirect('/');
}

// Обработка действий
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $userId = intval($_POST['user_id'] ?? 0);
        $action = $_POST['action'] ?? '';

        if ($userId <= 0) {
            throw new Exception("Неверный ID пользователя");
        }

        $adminFAQ = new AdminFAQ($pdo);

        if ($action === 'ban') {
            $adminFAQ->banUser($userId);
            displaySuccess("Пользователь успешно заблокирован");
        } elseif ($action === 'unban') {
            $adminFAQ->unbanUser($userId);
            displaySuccess("Пользователь успешно разблокирован");
        }
    } catch (Exception $e) {
        displayError($e->getMessage());
    }
}

// Получение списка пользователей
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

    <h1>Управление пользователями</h1>

    <div class="table-responsive mt-4">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Имя пользователя</th>
                <th>Email</th>
                <th>Роль</th>
                <th>Статус</th>
                <th>Дата регистрации</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= $user['role'] ?></td>
                    <td><?= $user['banned'] ? '<span class="badge bg-danger">Заблокирован</span>' : '<span class="badge bg-success">Активен</span>' ?></td>
                    <td><?= date('d.m.Y H:i', strtotime($user['created_at'])) ?></td>
                    <td>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                            <?php if ($user['banned']): ?>
                                <input type="hidden" name="action" value="unban">
                                <button type="submit" class="btn btn-success btn-sm">Разблокировать</button>
                            <?php else: ?>
                                <input type="hidden" name="action" value="ban">
                                <button type="submit" class="btn btn-warning btn-sm">Заблокировать</button>
                            <?php endif; ?>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

