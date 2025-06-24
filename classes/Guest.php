<?php
class Guest extends User {
    public function __construct() {
        parent::__construct(['role' => 'guest']);
    }

    public static function register($pdo, $username, $password, $email) {
        // Проверка на существование пользователя
        $existingUser = User::findByUsername($pdo, $username);
        if ($existingUser) {
            throw new Exception("Пользователь с таким именем уже существует");
        }

        // Хеширование пароля
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Создание пользователя
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, 'user')");
        $stmt->execute([$username, $hashedPassword, $email]);

        // Получение ID нового пользователя
        $userId = $pdo->lastInsertId();

        return User::findById($pdo, $userId);
    }

    public function viewPages($pdo) {
        $stmt = $pdo->query("SELECT * FROM pages ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}