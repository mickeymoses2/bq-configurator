<?php
include '../includes/config.php'; // Ensure this contains your $conn details

$sql = "UPDATE projects 
        SET edit_lock = 1 
        WHERE status = 'submitted' 
        AND edit_lock = 0 
        AND submitted_at < NOW() - INTERVAL 1 DAY";

if ($conn->query($sql) === TRUE) {
    $affectedRows = $conn->affected_rows;
    echo "Success: " . $affectedRows . " projects have been locked.";
} else {
    echo "Error updating records: " . $conn->error;
}

$conn->close();
?>