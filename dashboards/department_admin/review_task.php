<?php
require_once '../../auth/auth.php';
require_once '../../auth/authorize.php';
require_once '../../config/db.php';

authorizeRole(['department_admin']);

$department_id = $_SESSION['department_id'];
$user_id = $_SESSION['user_id'];

$task_id = $_POST['task_id'];
$assigned_user = $_POST['user_id'];
$rating = $_POST['rating'];

$stmt = $pdo->prepare("
    SELECT ta.*
    FROM task_assignments ta
    JOIN task t ON ta.task_id = t.task_id
    WHERE ta.task_id = ?
    AND ta.user_id = ?
    AND t.department_id = ?
    AND ta.status = 'completed'
    AND ta.rating IS NULL
");
$stmt->execute([$task_id, $assigned_user, $department_id]);

if (!$stmt->fetch()) {
    die("Invalid review attempt.");
}

$stmt = $pdo->prepare("
    UPDATE task_assignments
    SET rating = ?, reviewed_by = ?, reviewed_at = NOW()
    WHERE task_id = ? AND user_id = ?
");
$stmt->execute([$rating, $user_id, $task_id, $assigned_user]);

header("Location: tasks.php");
exit;