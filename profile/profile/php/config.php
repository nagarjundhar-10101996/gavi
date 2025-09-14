<?php
require_once 'C:/xampp/htdocs/profile/vendor/autoload.php';
return [
    'db' => [
        'host' => '127.0.0.1',
        'db'   => 'infodb',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4'
    ],
    'redis' => [
        'host' => '127.0.0.1',
        'port' => 6379,
        'ttl'  => 3600 // 1 hour
    ],
];