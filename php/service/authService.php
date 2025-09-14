<?php
require_once __DIR__ .'/../db/mysql.php';
require_once __DIR__.'/../redis/redisClient.php';
require_once __DIR__.'/../utils/token.php';
require_once __DIR__ .'/../repo/userRepository.php';
class AuthService {
    private $repo;
    private $redis;

    public function __construct() {
        $pdo = getMysqlPDO();
        $this->repo = new UserRepository($pdo);
        $this->redis = new RedisClient('127.0.0.1', 6379);
    }

    public function register($email, $password, $name) {
        // Basic validation (enhance in prod)
        try {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $id = $this->repo->createUser($email, $hash, $name);
        } catch (Exception $e) {
            throw $e;
        }
        return $id;
    }

    public function login($email, $password) {
        $user = $this->repo->getUserByEmail($email);
        if (!$user) return null;
        if (!password_verify($password, $user['password_hash'])) return null;

        $token = generateToken();
        $payload = ['user_id' => $user['id'], 'email' => $user['email']];
        $this->redis->setToken($token, $payload, ); // 1h TTL
        return ['token' => $token, 'user' => $user];
    }
    
    public function validateToken($token) {
        $payload = $this->redis->getToken($token);
        return $payload;
    }

    public function logout($token) {
        $this->redis->deleteToken($token);
        return true;
    }
}