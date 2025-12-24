<?php
session_start();
header('Content-Type: application/json');

// --- Ensure user is logged in ---
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status'=>'error','message'=>'Not logged in']);
    exit;
}

$clientId = $_SESSION['user_id'];

// --- Load DB ---
$config = include '../includes/db.php';
$dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $config['user'], $config['pass'], $options);
} catch (\PDOException $e) {
    echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
    exit;
}

// --- Get JSON from request ---
$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['modules'])) {
    echo json_encode(['status'=>'error','message'=>'Invalid data']);
    exit;
}

$projectId = $data['project_id'] ?? 0;
$isSubmit = !empty($data['submit']);

// --- Check if project exists ---
$stmt = $pdo->prepare("SELECT id FROM projects WHERE id = ? AND client_id = ?");
$stmt->execute([$projectId, $clientId]);
$existingProject = $stmt->fetchColumn();

// --- If project doesn’t exist, create it ---
if (!$existingProject) {
    $stmt = $pdo->prepare("INSERT INTO projects (client_id, title, location, status, submitted_at) VALUES (?, ?, ?, 'draft', NOW())");
    $stmt->execute([
        $clientId,
        $data['projectTitle'] ?? 'Untitled Project',
        $data['projectLocation'] ?? ''
    ]);
    $projectId = $pdo->lastInsertId();
}

// --- Save JSON state to project_data ---
$stmt = $pdo->prepare("SELECT id FROM project_data WHERE project_id = ?");
$stmt->execute([$projectId]);
$existingDataId = $stmt->fetchColumn();

$jsonData = json_encode($data['modules'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

if ($existingDataId) {
    $stmt = $pdo->prepare("UPDATE project_data SET json_data = ? WHERE project_id = ?");
    $stmt->execute([$jsonData, $projectId]);
} else {
    $stmt = $pdo->prepare("INSERT INTO project_data (project_id, json_data) VALUES (?, ?)");
    $stmt->execute([$projectId, $jsonData]);
}

// --- Optional: mark project submitted ---
if ($isSubmit) {
    $stmt = $pdo->prepare("UPDATE projects SET status='submitted', submitted_at=NOW() WHERE id=?");
    $stmt->execute([$projectId]);
}

echo json_encode([
    'status'=>'success',
    'message'=>'BQ saved successfully',
    'project_id'=>$projectId
]);