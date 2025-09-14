<?php
require_once  __DIR__ . '/../config.php';
class RedisClient {
    private $redis;
    public function __construct($host = null, $port = null) {
        $host = $host ?? ($_ENV['REDIS_HOST'] ?? '127.0.0.1');
        $port = $port ?? ($_ENV['REDIS_PORT'] ?? 6379);
        $this->redis = new Redis();
        $this->redis->connect($host, $port);
    }

    public function setToken($token, $payload, $ttl=null) {
        $ttl = $ttl ?? ($_ENV['REDIS_TTL'] ?? 3600);
        $this->redis->setex($token, $ttl, json_encode($payload));
    }
    public function getToken($token) {
        $val = $this->redis->get($token);
        return $val ? json_decode($val, true) : null;
    }
    public function deleteToken($token) {
        $this->redis->del($token);
    }
}