<?php
class UserRepository {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }

    public function createUser($email, $passwordHash, $name) {
        $sql = "INSERT INTO users (email, password_hash, name) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        try {
            $stmt->execute([$email, $passwordHash, $name]);
        } catch (PDOException $e) {
            
            if ($e->errorInfo[1] == 1062) 
            {
                throw new Exception("Email already registered");
            } 
            else 
            {
                throw $e; // rethrow other errors
            }
        }
        return $this->pdo->lastInsertId();
    }

    public function getUserByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function getUserById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}