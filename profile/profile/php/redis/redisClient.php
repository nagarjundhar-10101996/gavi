<?php
require_once  __DIR__ . '/../config.php';
class RedisClient {
    private $redis;
    public function __construct($host='127.0.0.1', $port=6379) {
        $this->redis = new Redis();
        $this->redis->connect($host, $port);
    }
    public function setToken($token, $payload, $ttl) {
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