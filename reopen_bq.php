<?php
session_start();
require '../includes/db.php';

$pdo->prepare("
  UPDATE projects
  SET status = 'draft'
  WHERE client_id = ?
")->execute([$_SESSION['user_id']]);
