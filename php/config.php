<?php
require_once __DIR__ . '/../vendor/autoload.php';
$env = file_get_contents(__DIR__ . '/../.env');
$lines = explode("\n",$env);
foreach ($lines as $key => $value) {
    $list=explode("=",$value);
    if (count($list)> 1) {  
        $key=trim($list[0]);
        $value=trim($list[1]);   
        $_ENV[$key] = $value;    
    }
}