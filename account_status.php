<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'not_logged_in']);
    exit;
}

$config = include '../includes/db.php';

$pdo = new PDO(
    "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}",
    $config['user'],
    $config['pass'],
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
);

$clientId = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT status, submitted_at
    FROM projects
    WHERE client_id = ?
    LIMIT 1
");
$stmt->execute([$clientId]);
$project = $stmt->fetch();

if (!$project) {
    echo json_encode([
        'status' => 'draft',
        'editable' => true
    ]);
    exit;
}

$editable = false;

if ($project['status'] === 'draft') {
    $editable = true;
} elseif (
    $project['status'] === 'submitted' &&
    (time() - strtotime($project['submitted_at'])) <= 86400
) {
    $editable = true;
}

echo json_encode([
    'status' => $project['status'],
    'editable' => $editable
]);
