<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);  // Temporary — hide notices
// OR better:
ini_set('display_errors', 0);

session_start();
include '../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit;
}

$_SESSION['client_id'] = $_SESSION['user_id'];
$_SESSION['client_name'] = $_SESSION['user_name'];

$client_id = $_SESSION['user_id'];
$status = 'pending'; 
$submitted_at = null;
$edit_lock = 0;      
$canEdit = true;     

// --- Project status logic ---
$sql = "SELECT status, submitted_at, edit_lock FROM projects WHERE client_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $client_id);
$stmt->execute();
$result = $stmt->get_result();

if($row = $result->fetch_assoc()){
    $status = $row['status'];
    $submitted_at = $row['submitted_at'];
    $edit_lock = (int)$row['edit_lock'];

    if($status === 'submitted') {
        if($edit_lock === 1) {
            $canEdit = false;
        } elseif($submitted_at) {
            $submittedTime = strtotime($submitted_at);
            if((time() - $submittedTime) >= 86400) {
                $canEdit = false;
            }
        }
    }
}
$stmt->close();

// --- Load or create project_data ---
$projectId = $_SESSION['project_id'] ?? 0;
$bqState = null;

// Define $defaultModules from your template
$defaultModules = [
    ["id"=>"shell","name"=>"Shell (Including Plaster & Paint)","children"=>[["id"=>"shell1","name"=>"Shell Works","rate"=>2510000,"volume"=>1]]],
    ["id"=>"roof","name"=>"Roof Structure & Cover","children"=>[["id"=>"roof1","name"=>"Roofing Works","rate"=>460000,"volume"=>1]]],
    ["id"=>"tiles","name"=>"All Tiles","children"=>[["id"=>"tiles1","name"=>"Tiles Supply & Install","rate"=>410000,"volume"=>1]]],
    ["id"=>"windows","name"=>"Windows","children"=>[["id"=>"windows1","name"=>"Window Installations","rate"=>150000,"volume"=>1]]],
    ["id"=>"kitchen","name"=>"Kitchen Fittings & Wardrobes","children"=>[["id"=>"kitchens1","name"=>"Kitchen & Wardrobes","rate"=>250000,"volume"=>1]]],
    ["id"=>"doorsInt","name"=>"Doors (Internal)","children"=>[["id"=>"doorsInt1","name"=>"Internal Doors","rate"=>105000,"volume"=>1]]],
    ["id"=>"doorsExt","name"=>"Doors (External)","children"=>[["id"=>"doorsExt1","name"=>"External Doors","rate"=>115000,"volume"=>1]]],
    ["id"=>"electrical","name"=>"Electricals","children"=>[["id"=>"elect1","name"=>"Electrical Works","rate"=>250000,"volume"=>1]]],
    ["id"=>"plumbing","name"=>"Plumbing","children"=>[["id"=>"plumb1","name"=>"Plumbing Works","rate"=>120000,"volume"=>1]]],
    ["id"=>"ceiling","name"=>"Ceiling","children"=>[["id"=>"ceil1","name"=>"Ceiling Works","rate"=>160000,"volume"=>1]]],
    ["id"=>"externalWorks","name"=>"External Works (Drainage & Biodigester)","children"=>[["id"=>"extW1","name"=>"External Works","rate"=>150000,"volume"=>1]]],
    ["id"=>"consultants","name"=>"Consultants","children"=>[["id"=>"consult1","name"=>"Consultancy","rate"=>120000,"volume"=>1]]],
    ["id"=>"approvals","name"=>"Approvals","children"=>[["id"=>"approv1","name"=>"Approval Fees","rate"=>150000,"volume"=>1]]]
];

$stmt = $conn->prepare("SELECT json_data FROM project_data WHERE project_id = ? LIMIT 1");
$stmt->bind_param("i", $projectId);
$stmt->execute();

$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $raw = json_decode($row['json_data'], true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        // Corrupted data — fallback to defaults
        $bqState = json_encode($defaultModules);
    } elseif (is_array($raw) && isset($raw[0]['id'])) {
        // New clean format: direct array of modules
        $bqState = $row['json_data'];  // already JSON string
    } elseif (isset($raw['modules']) && is_array($raw['modules'])) {
        // Old buggy format — extract modules
        $bqState = json_encode($raw['modules']);
    } else {
        // Unknown format — defaults
        $bqState = json_encode($defaultModules);
    }
} else {
    // No saved state → use defaults and insert
    $bqState = json_encode($defaultModules);
    $stmtInsert = $conn->prepare("INSERT INTO project_data (project_id, json_data) VALUES (?, ?)");
    $stmtInsert->bind_param("is", $projectId, $bqState);
    $stmtInsert->execute();
    $stmtInsert->close();
}

$stmt->close();
?>
<script>
window.APP_CONFIG = {
    canEdit: <?= json_encode($canEdit) ?>,
    status: <?= json_encode($status) ?>,
    projectId: <?= json_encode($projectId) ?>,
    bqState: <?= $bqState ?>  // ← Keep this as-is (it's already json_encode'd string)
};

</script>
