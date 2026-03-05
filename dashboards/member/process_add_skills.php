<?php
require_once '../../auth/auth.php';
require_once '../../auth/authorize.php';
require_once '../../config/db.php';

authorizeRole(['member']);

$user_id = $_SESSION['user_id'];
$department_id = $_SESSION['department_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $skill_description = trim($_POST['skill_description']);

    if (!empty($skill_description)) {

        $stmt = $pdo->prepare("
            INSERT INTO user_skill 
            (user_id, department_id, skill_description)
            VALUES (?, ?, ?)
        ");

        $stmt->execute([
            $user_id,
            $department_id,
            $skill_description
        ]);
    }

    header("Location: skills.php");
    exit();
}