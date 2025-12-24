<?php

// Prevent any accidental output
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../php-error.log');

$config = [
    'host' => 'localhost',
    'dbname' => 'econ_shelters',
    'user' => 'root',
    'pass' => '',
    'charset' => 'utf8mb4'
];

// Create mysqli connection
$conn = new mysqli(
    $config['host'],
    $config['user'],
    $config['pass'],
    $config['dbname']
);

// Check connection silently
if ($conn->connect_errno) {
    error_log("DB connection failed: " . $conn->connect_error);

    // Return clean JSON error
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed. Try again later."
    ]);
    exit;
}

// Set charset
$conn->set_charset($config['charset']);
