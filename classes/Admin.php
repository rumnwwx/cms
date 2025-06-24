<?php
class Admin extends User {
    private $adminPages;
    private $adminFAQ;

    public function __construct($pdo, $data) {
        parent::__construct($data);
        $this->adminPages = new AdminPage($pdo);
        $this->adminFAQ = new AdminFAQ($pdo);
    }

    public function createPage($title, $content, $slug) {
        return $this->adminPages->createPage($title, $content, $slug);
    }

    public function answerQuestion($questionId, $answer) {
        $result = $this->adminFAQ->addAnswer($questionId, $answer);

        if ($result) {
            // Здесь должна быть логика отправки уведомления пользователю
            // Например, через email или внутреннюю систему уведомлений
            return true;
        }

        return false;
    }

    public function banUser($userId) {
        return $this->adminFAQ->banUser($userId);
    }

    public function unbanUser($userId) {
        return $this->adminFAQ->unbanUser($userId);
    }

    public function addTopic($topicName) {
        return $this->adminFAQ->addTopic($topicName);
    }
}