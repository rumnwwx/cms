<?php
class Admin extends User {
    private $adminPages;
    private $adminFAQ;

    public function __construct($pdo, $data) {
        parent::__construct($data);
        $this->adminPages = new AdminPage($pdo);
        $this->adminFAQ = new AdminFAQ($pdo);
    }

    public function answerQuestion($questionId, $answer) {
        $result = $this->adminFAQ->addAnswer($questionId, $answer);

        if ($result) {
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