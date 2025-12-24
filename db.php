<?php
// config.php

$config = [
    'host' => 'localhost',
    'dbname' => 'econ_shelters', 
    'user' => 'root',
    'pass' => '', 
    'charset' => 'utf8mb4'
];

// Optional: test connection immediately
try {
    $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['user'], $config['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

return $config;
