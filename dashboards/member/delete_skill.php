<?php
require_once '../../auth/auth.php';
require_once '../../auth/authorize.php';
require_once '../../config/db.php';

authorizeRole(['member']);

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $skill_id = $_POST['skill_id'];

    // Ensure user can only delete their own skill
    $stmt = $pdo->prepare("
        DELETE FROM user_skill
        WHERE skill_id = ? AND user_id = ?
    ");

    $stmt->execute([$skill_id, $user_id]);
}

header("Location: skills.php");
exit();