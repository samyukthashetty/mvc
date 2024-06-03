<?php

require_once '../Database/Database.php';

class TokenRepository {
    private $conn;

    public function __construct(Database $database) {
        $this->conn = $database->getConnection();
    }

    public function saveToken($userId, $token) {
        $sql = "INSERT INTO tokens (user_id, token) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$userId, $token]);
    }

    public function validateToken($token) {
        $sql = "SELECT * FROM tokens WHERE token = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteToken($token) {
        $sql = "DELETE FROM tokens WHERE token = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$token]);
    }
}

?>
