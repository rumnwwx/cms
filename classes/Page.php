<?php
class Page {
    private $id;
    private $title;
    private $content;
    private $slug;
    private $created_at;
    private $updated_at;

    public function __construct($data = []) {
        if (isset($data['id'])) $this->id = $data['id'];
        if (isset($data['title'])) $this->title = $data['title'];
        if (isset($data['content'])) $this->content = $data['content'];
        if (isset($data['slug'])) $this->slug = $data['slug'];
        if (isset($data['created_at'])) $this->created_at = $data['created_at'];
        if (isset($data['updated_at'])) $this->updated_at = $data['updated_at'];
    }

    public static function findById($pdo, $id) {
        $stmt = $pdo->prepare("SELECT * FROM pages WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            return new Page($data);
        }
        return null;
    }

    public static function findBySlug($pdo, $slug) {
        $stmt = $pdo->prepare("SELECT * FROM pages WHERE slug = ?");
        $stmt->execute([$slug]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            return new Page($data);
        }
        return null;
    }

    public static function getAll($pdo) {
        $stmt = $pdo->query("SELECT * FROM pages ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function save($pdo) {
        if ($this->id) {
            // Обновление существующей страницы
            $stmt = $pdo->prepare("UPDATE pages SET title = ?, content = ?, slug = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$this->title, $this->content, $this->slug, $this->id]);
        } else {
            // Создание новой страницы
            $stmt = $pdo->prepare("INSERT INTO pages (title, content, slug) VALUES (?, ?, ?)");
            $stmt->execute([$this->title, $this->content, $this->slug]);
            $this->id = $pdo->lastInsertId();
        }
        return true;
    }

    public function delete($pdo) {
        if (!$this->id) return false;

        $stmt = $pdo->prepare("DELETE FROM pages WHERE id = ?");
        return $stmt->execute([$this->id]);
    }

    // Геттеры
    public function getId() { return $this->id; }
    public function getTitle() { return $this->title; }
    public function getContent() { return $this->content; }
    public function getSlug() { return $this->slug; }
    public function getCreatedAt() { return $this->created_at; }
    public function getUpdatedAt() { return $this->updated_at; }

    // Сеттеры
    public function setTitle($title) { $this->title = $title; }
    public function setContent($content) { $this->content = $content; }
    public function setSlug($slug) { $this->slug = $slug; }
}