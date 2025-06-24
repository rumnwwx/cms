<?php
class AdminPage {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function createPage($title, $content, $slug) {
        $page = new Page([
            'title' => $title,
            'content' => $content,
            'slug' => $slug
        ]);

        return $page->save($this->pdo);
    }

    public function editPage($pageId, $title, $content, $slug) {
        $page = Page::findById($this->pdo, $pageId);
        if (!$page) return false;

        $page->setTitle($title);
        $page->setContent($content);
        $page->setSlug($slug);

        return $page->save($this->pdo);
    }

    public function deletePage($pageId) {
        $page = Page::findById($this->pdo, $pageId);
        if (!$page) return false;

        return $page->delete($this->pdo);
    }

    public function getAllPages() {
        return Page::getAll($this->pdo);
    }
}