<?php
session_start();
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../includes/functions.php'; // Подключаем функции

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    try {
        $tempPdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
        $tempPdo->exec("CREATE DATABASE IF NOT EXISTS `".DB_NAME."` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");

        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        initializeDatabase($pdo);
    } catch (PDOException $ex) {
        die("Ошибка подключения к базе данных: " . $ex->getMessage());
    }
}

/**
 * Инициализация структуры БД
 */
function initializeDatabase(PDO $pdo): void
{
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

    if (empty($tables)) {
        try {
            $pdo->beginTransaction();

            $pdo->exec("CREATE TABLE `users` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `username` varchar(50) NOT NULL,
              `password` varchar(255) NOT NULL,
              `email` varchar(100) NOT NULL,
              `role` enum('guest','user','admin') NOT NULL DEFAULT 'user',
              `banned` tinyint(1) NOT NULL DEFAULT 0,
              `created_at` datetime NOT NULL DEFAULT current_timestamp(),
              PRIMARY KEY (`id`),
              UNIQUE KEY `username` (`username`),
              UNIQUE KEY `email` (`email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            $pdo->exec("CREATE TABLE `faq_topics` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `name` varchar(100) NOT NULL,
              `created_at` datetime NOT NULL DEFAULT current_timestamp(),
              PRIMARY KEY (`id`),
              UNIQUE KEY `name` (`name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            $pdo->exec("CREATE TABLE `faq_questions` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `question` text NOT NULL,
              `answer` text DEFAULT NULL,
              `topic_id` int(11) NOT NULL,
              `user_id` int(11) NOT NULL,
              `status` enum('pending','answered','rejected') NOT NULL DEFAULT 'pending',
              `created_at` datetime NOT NULL DEFAULT current_timestamp(),
              `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
              PRIMARY KEY (`id`),
              KEY `topic_id` (`topic_id`),
              KEY `user_id` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            $pdo->exec("INSERT INTO `users` (`username`, `password`, `email`, `role`) VALUES 
              ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@example.com', 'admin')");

            $pdo->exec("INSERT INTO `faq_topics` (`name`) VALUES 
              ('Общие вопросы'), ('Техническая поддержка'), ('Оплата и счета')");

            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            die("Ошибка инициализации БД: " . $e->getMessage());
        }
    }
}

spl_autoload_register(function ($class_name) {
    $file = __DIR__ . '/../classes/' . $class_name . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

function redirect($url) {
    header("Location: $url");
    exit();
}

initializeDatabase($pdo);