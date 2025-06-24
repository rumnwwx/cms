<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/header.php';
$currentUser = getCurrentUser($pdo);
$pageTitle = "FAQ";
$faq = new FAQ($pdo);
$topics = $faq->getTopics();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLoggedIn() && !$currentUser->isBanned()) {
    $question = trim($_POST['question'] ?? '');
    $topicId = intval($_POST['topic_id'] ?? 0);

    try {
        if (empty($question)) {
            throw new Exception("Вопрос не может быть пустым");
        }

        if ($topicId <= 0) {
            throw new Exception("Выберите тему вопроса");
        }

        $success = $faq->addQuestion($question, $topicId, $currentUser->getId());

        if ($success) {
            displaySuccess("Ваш вопрос был успешно отправлен на модерацию");
        } else {
            throw new Exception("Произошла ошибка при отправке вопроса");
        }
    } catch (Exception $e) {
        displayError($e->getMessage());
    }
}

$selectedTopicId = isset($_GET['topic']) ? intval($_GET['topic']) : 0;
$questions = $selectedTopicId > 0
    ? $faq->getQuestionsByTopic($selectedTopicId, true)
    : $faq->getAllQuestions(true);
?>

    <h1>Часто задаваемые вопросы</h1>

    <div class="row mt-4">
        <div class="col-md-3">
            <div class="list-group">
                <a href="?topic=0" class="list-group-item list-group-item-action <?= $selectedTopicId === 0 ? 'active' : '' ?>">
                    Все темы
                </a>
                <?php foreach ($topics as $topic): ?>
                    <a href="?topic=<?= $topic['id'] ?>" class="list-group-item list-group-item-action <?= $selectedTopicId === $topic['id'] ? 'active' : '' ?>">
                        <?= htmlspecialchars($topic['name']) ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <?php if (isLoggedIn() && !$currentUser->isBanned()): ?>
                <div class="mt-4">
                    <h5>Задать вопрос</h5>
                    <form method="POST">
                        <div class="mb-3">
                            <select class="form-select" name="topic_id" required>
                                <option value="">Выберите тему</option>
                                <?php foreach ($topics as $topic): ?>
                                    <option value="<?= $topic['id'] ?>"><?= htmlspecialchars($topic['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control" name="question" rows="3" placeholder="Ваш вопрос" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Отправить</button>
                    </form>
                </div>
            <?php elseif (isLoggedIn() && $currentUser->isBanned()): ?>
                <div class="alert alert-danger mt-4">
                    Вы не можете задавать вопросы, так как ваш аккаунт заблокирован.
                </div>
            <?php else: ?>
                <div class="alert alert-info mt-4">
                    <a href="/pages/login.php">Войдите</a>, чтобы задать вопрос.
                </div>
            <?php endif; ?>
        </div>
        <div class="col-md-9">
            <?php if ($questions): ?>
                <div class="accordion" id="faqAccordion">
                    <?php foreach ($questions as $i => $question): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading<?= $i ?>">
                                <button class="accordion-button <?= $i === 0 ? '' : 'collapsed' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $i ?>">
                                    <?= htmlspecialchars($question['question']) ?>
                                </button>
                            </h2>
                            <div id="collapse<?= $i ?>" class="accordion-collapse collapse <?= $i === 0 ? 'show' : '' ?>" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p><?= nl2br(htmlspecialchars($question['answer'])) ?></p>
                                    <small class="text-muted">
                                        Тема: <?= htmlspecialchars($question['topic_name']) ?><br>
                                        Автор: <?= htmlspecialchars($question['username']) ?><br>
                                        Дата: <?= date('d.m.Y H:i', strtotime($question['created_at'])) ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    В этой теме пока нет ни одного вопроса.
                </div>
            <?php endif; ?>
        </div>
    </div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
