<?php
require_once '../../auth/auth.php';
require_once '../../auth/authorize.php';
require_once '../../config/db.php';

authorizeRole(['member']);

$user_id = $_SESSION['user_id'];
$task_id = $_POST['task_id'];

/*
Verify assignment and lock status
*/
$stmt = $pdo->prepare("
    SELECT * FROM task_assignments
    WHERE task_id = ?
    AND user_id = ?
    AND rating IS NULL
");
$stmt->execute([$task_id, $user_id]);
$assignment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$assignment) {
    die("Invalid task or already reviewed.");
}

$file = $_FILES['report'];

if ($file['error'] !== 0) die("Upload error.");
if ($file['size'] > 5 * 1024 * 1024) die("File too large.");

$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if ($ext !== 'pdf') die("Only PDF allowed.");

$new_name = "task_{$task_id}_user_{$user_id}_" . time() . ".pdf";

$uploadDir = __DIR__ . "/../../uploads/task_reports/";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$path = $uploadDir . $new_name;

if (!is_uploaded_file($file['tmp_name'])) {
    die("Invalid upload.");
}

if (!move_uploaded_file($file['tmp_name'], $path)) {
    die("Upload failed.");
}

/*
Update assignment
*/
$stmt = $pdo->prepare("
    UPDATE task_assignments
    SET report_file = ?,
        report_uploaded_at = NOW(),
        status = 'completed',
        completed_at = NOW()
    WHERE task_id = ? AND user_id = ?
");
$stmt->execute([$new_name, $task_id, $user_id]);

/*
Log activity
*/
$log = $pdo->prepare("
    INSERT INTO activity_log
    (user_id, action_type, action_description)
    VALUES (?, ?, ?)
");
$log->execute([
    $user_id,
    'upload_report',
    "Uploaded report for task ID $task_id"
]);

header("Location: tasks.php");
exit;