<?php
class AdminFAQ {
    private $pdo;
    private $faq;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->faq = new FAQ($pdo);
    }

    public function addTopic($name) {
        // Проверка на существование темы
        $stmt = $this->pdo->prepare("SELECT id FROM faq_topics WHERE name = ?");
        $stmt->execute([$name]);
        if ($stmt->fetch()) {
            throw new Exception("Тема с таким названием уже существует");
        }

        $stmt = $this->pdo->prepare("INSERT INTO faq_topics (name) VALUES (?)");
        return $stmt->execute([$name]);
    }

    public function banUser($userId) {
        // Проверяем существование пользователя
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE id = ?");
        $stmt->execute([$userId]);

        if (!$stmt->fetch()) {
            throw new Exception("Пользователь с ID $userId не найден");
        }

        $stmt = $this->pdo->prepare("UPDATE users SET banned = 1 WHERE id = ?");
        if (!$stmt->execute([$userId])) {
            throw new Exception("Ошибка при блокировке пользователя");
        }
        return true;
    }

    public function addAnswer($questionId, $answer) {
        $stmt = $this->pdo->prepare("UPDATE faq_questions SET answer = ?, status = 'answered', updated_at = NOW() WHERE id = ?");
        if (!$stmt->execute([$answer, $questionId])) {
            throw new Exception("Ошибка при добавлении ответа");
        }
        return true;
    }

    public function unbanUser($userId) {
        $stmt = $this->pdo->prepare("UPDATE users SET banned = 0 WHERE id = ?");
        return $stmt->execute([$userId]);
    }

//    public function addAnswer($questionId, $answer) {
//        return $this->faq->addAnswer($questionId, $answer);
//    }

    public function rejectQuestion($questionId) {
        $stmt = $this->pdo->prepare("UPDATE faq_questions SET status = 'rejected', updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$questionId]);
    }

    public function getPendingQuestions() {
        return $this->faq->getPendingQuestions();
    }

    public function getTopics() {
        return $this->faq->getTopics();
    }
}