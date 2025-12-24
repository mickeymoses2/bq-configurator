<?php
session_start();
header('Content-Type: application/json');

$config = include '../includes/db.php';
$pdo = new PDO(
  "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}",
  $config['user'],
  $config['pass']
);

$clientId = $_SESSION['user_id'];

$stmt = $pdo->prepare("
  SELECT submitted_at
  FROM projects
  WHERE client_id = ?
");
$stmt->execute([$clientId]);
$submittedAt = $stmt->fetchColumn();

if (!$submittedAt || (time() - strtotime($submittedAt)) > 86400) {
  echo json_encode(['status' => 'error']);
  exit;
}

$stmt = $pdo->prepare("
  UPDATE projects SET status = 'draft'
  WHERE client_id = ?
");
$stmt->execute([$clientId]);

echo json_encode(['status' => 'ok']);
