<?php
class UserRepository {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }

    public function createUser($email, $passwordHash, $name) {
        $stmt = $this->pdo->prepare("INSERT INTO users (email, password_hash, name) VALUES (:email, :password_hash, :name)");
        try {
            $stmt->execute([
            ':email' => $email,
            ':password_hash' => $passwordHash,
            ':name' => $name
        ]);
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
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    public function getUserById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
}