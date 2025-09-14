<?php
require_once  __DIR__ . '/../config.php';


function getMysqlPDO() {
    $config = require __DIR__ . '/../config.php';
    $host = $config['db']['host'];
    $db   = $config['db']['db'];
    $user = $config['db']['user'];
    $pass = $config['db']['pass'];
    $charset = $config['db']['charset'];
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $opts = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    return new PDO($dsn, $user, $pass, $opts);
}