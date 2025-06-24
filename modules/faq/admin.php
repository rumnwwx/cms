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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['add_topic'])) {
            $topicName = trim($_POST['topic_name'] ?? '');
            if (empty($topicName)) {
                throw new Exception("Название темы не может быть пустым");
            }

            $adminFAQ->addTopic($topicName);
            displaySuccess("Тема успешно добавлена");
        } elseif (isset($_POST['answer_question'])) {
            $questionId = intval($_POST['question_id'] ?? 0);
            $answer = trim($_POST['answer'] ?? '');

            if ($questionId <= 0) {
                throw new Exception("Неверный ID вопроса");
            }

            if (empty($answer)) {
                throw new Exception("Ответ не может быть пустым");
            }

            $adminFAQ->addAnswer($questionId, $answer);
            displaySuccess("Ответ успешно добавлен");
        } elseif (isset($_POST['reject_question'])) {
            $questionId = intval($_POST['question_id'] ?? 0);

            if ($questionId <= 0) {
                throw new Exception("Неверный ID вопроса");
            }

            $adminFAQ->rejectQuestion($questionId);
            displaySuccess("Вопрос отклонен");
        } elseif (isset($_POST['ban_user'])) {
            $userId = intval($_POST['user_id'] ?? 0);

            if ($userId <= 0) {
                throw new Exception("Неверный ID пользователя");
            }

            $adminFAQ->banUser($userId);
            displaySuccess("Пользователь заблокирован");
        } elseif (isset($_POST['unban_user'])) {
            $userId = intval($_POST['user_id'] ?? 0);

            if ($userId <= 0) {
                throw new Exception("Неверный ID пользователя");
            }

            $adminFAQ->unbanUser($userId);
            displaySuccess("Пользователь разблокирован");
        }
    } catch (Exception $e) {
        displayError($e->getMessage());
    }
}

$pendingQuestions = $adminFAQ->getPendingQuestions();
$topics = $adminFAQ->getTopics();
?>

    <h1>Управление FAQ</h1>

    <ul class="nav nav-tabs mt-4" id="faqAdminTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button">
                Вопросы на модерации (<?= count($pendingQuestions) ?>)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="topics-tab" data-bs-toggle="tab" data-bs-target="#topics" type="button">
                Управление темами
            </button>
        </li>
    </ul>

    <div class="tab-content mt-3" id="faqAdminTabsContent">
        <div class="tab-pane fade show active" id="pending" role="tabpanel">
            <?php if ($pendingQuestions): ?>
                <div class="list-group">
                    <?php foreach ($pendingQuestions as $question): ?>
                        <div class="list-group-item">
                            <h5><?= htmlspecialchars($question['question']) ?></h5>
                            <p class="mb-1">
                                <small class="text-muted">
                                    Тема: <?= htmlspecialchars($question['topic_name']) ?> |
                                    Автор: <?= htmlspecialchars($question['username']) ?> |
                                    Дата: <?= date('d.m.Y H:i', strtotime($question['created_at'])) ?>
                                </small>
                            </p>

                            <form method="POST" class="mt-2">
                                <input type="hidden" name="question_id" value="<?= $question['id'] ?>">
                                <input type="hidden" name="user_id" value="<?= $question['user_id'] ?>">
                                <div class="mb-3">
                                    <textarea class="form-control" name="answer" rows="3" placeholder="Ваш ответ" required></textarea>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" name="answer_question" class="btn btn-success">Ответить</button>
                                    <button type="submit" name="reject_question" class="btn btn-danger">Отклонить</button>
                                    <button type="submit" name="ban_user" class="btn btn-warning" value="<?= $question['user_id'] ?>">Заблокировать автора</button>
                                </div>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-success">
                    Нет вопросов, ожидающих модерации.
                </div>
            <?php endif; ?>
        </div>

        <div class="tab-pane fade" id="topics" role="tabpanel">
            <div class="row">
                <div class="col-md-6">
                    <h4>Добавить новую тему</h4>
                    <form method="POST">
                        <div class="mb-3">
                            <input type="text" class="form-control" name="topic_name" placeholder="Название темы" required>
                        </div>
                        <button type="submit" name="add_topic" class="btn btn-primary">Добавить</button>
                    </form>
                </div>
                <div class="col-md-6">
                    <h4>Список тем</h4>
                    <?php if ($topics): ?>
                        <ul class="list-group">
                            <?php foreach ($topics as $topic): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?= htmlspecialchars($topic['name']) ?>
                                    <span class="badge bg-primary rounded-pill">
                                <?php
                                $stmt = $pdo->prepare("SELECT COUNT(*) FROM faq_questions WHERE topic_id = ?");
                                $stmt->execute([$topic['id']]);
                                echo $stmt->fetchColumn();
                                ?>
                            </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="alert alert-info">
                            Нет созданных тем.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>