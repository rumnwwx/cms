<?php
class User {
    protected $id;
    protected $username;
    protected $password;
    protected $email;
    protected $role;
    protected $banned;
    protected $created_at;

    public function __construct($data = []) {
        if (isset($data['id'])) $this->id = $data['id'];
        if (isset($data['username'])) $this->username = $data['username'];
        if (isset($data['password'])) $this->password = $data['password'];
        if (isset($data['email'])) $this->email = $data['email'];
        if (isset($data['role'])) $this->role = $data['role'];
        if (isset($data['banned'])) $this->banned = $data['banned'];
        if (isset($data['created_at'])) $this->created_at = $data['created_at'];
    }

    public static function findById($pdo, $id) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            return new User($data);
        }
        return null;
    }

    public static function findByUsername($pdo, $username) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            return new User($data);
        }
        return null;
    }

    public function login($password) {
        return password_verify($password, $this->password);
    }

    public function logout() {
        session_destroy();
    }

    public function isAdmin() {
        return $this->role === 'admin';
    }

    public function isBanned() {
        return $this->banned;
    }

    public function ban() {
        $this->banned = true;
    }

    public function unban() {
        $this->banned = false;
    }

    // Геттеры
    public function getId() { return $this->id; }
    public function getUsername() { return $this->username; }
    public function getEmail() { return $this->email; }
    public function getRole() { return $this->role; }
    public function getCreatedAt() { return $this->created_at; }
}