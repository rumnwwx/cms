<?php
class FAQ {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function addQuestion($question, $topicId, $userId) {
        $stmt = $this->pdo->prepare("INSERT INTO faq_questions (question, topic_id, user_id) VALUES (?, ?, ?)");
        return $stmt->execute([$question, $topicId, $userId]);
    }

    public function addAnswer($questionId, $answer) {
        $stmt = $this->pdo->prepare("UPDATE faq_questions SET answer = ?, status = 'answered', updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$answer, $questionId]);
    }

    public function getQuestionsByTopic($topicId, $onlyAnswered = false) {
        $sql = "SELECT fq.*, u.username, ft.name as topic_name 
                FROM faq_questions fq
                JOIN users u ON fq.user_id = u.id
                JOIN faq_topics ft ON fq.topic_id = ft.id
                WHERE fq.topic_id = ?";

        if ($onlyAnswered) {
            $sql .= " AND fq.status = 'answered'";
        }

        $sql .= " ORDER BY fq.created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$topicId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllQuestions($onlyAnswered = false) {
        $sql = "SELECT fq.*, u.username, ft.name as topic_name 
                FROM faq_questions fq
                JOIN users u ON fq.user_id = u.id
                JOIN faq_topics ft ON fq.topic_id = ft.id";

        if ($onlyAnswered) {
            $sql .= " WHERE fq.status = 'answered'";
        }

        $sql .= " ORDER BY fq.created_at DESC";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPendingQuestions() {
        $stmt = $this->pdo->query("SELECT fq.*, u.username, ft.name as topic_name 
                                  FROM faq_questions fq
                                  JOIN users u ON fq.user_id = u.id
                                  JOIN faq_topics ft ON fq.topic_id = ft.id
                                  WHERE fq.status = 'pending'
                                  ORDER BY fq.created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTopics() {
        $stmt = $this->pdo->query("SELECT * FROM faq_topics ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}