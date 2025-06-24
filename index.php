<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/header.php';

$pageTitle = "Главная страница";
?>

    <h1>Добро пожаловать в нашу CMS с модулем FAQ</h1>

<?php if (!isLoggedIn()): ?>
    <div class="alert alert-info">
        <p>Вы вошли как гость. <a href="/pages/register.php">Зарегистрируйтесь</a> или <a href="/pages/login.php">войдите</a>, чтобы получить доступ к дополнительным функциям.</p>
    </div>
<?php endif; ?>

    <div class="row mt-4">
        <div class="col-md-4">
            <h2>Последние вопросы</h2>
            <?php
            $faq = new FAQ($pdo);
            $questions = $faq->getAllQuestions(true);
            if ($questions): ?>
                <div class="list-group">
                    <?php foreach (array_slice($questions, 0, 5) as $question): ?>
                        <div class="list-group-item">
                            <h6><?= htmlspecialchars($question['topic_name']) ?></h6>
                            <p><?= htmlspecialchars($question['question']) ?></p>
                            <small class="text-muted">От: <?= htmlspecialchars($question['username']) ?></small>
                        </div>
                    <?php endforeach; ?>
                </div>
                <a href="/modules/faq/" class="btn btn-primary mt-2">Все вопросы</a>
            <?php else: ?>
                <p>Пока нет ни одного вопроса.</p>
            <?php endif; ?>
        </div>
    </div>
