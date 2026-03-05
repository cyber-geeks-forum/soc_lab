<?php
function logActivity($pdo, $action_type, $description = null, $user_id = null) {

    $user_id = $user_id ?? ($_SESSION['user_id'] ?? null);
    $department_id = $_SESSION['department_id'] ?? null;
    $ip_address = $_SERVER['REMOTE_ADDR'];

    $stmt = $pdo->prepare("
        INSERT INTO activity_log 
        (user_id, action_type, action_description, ip_address, department_id)
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $user_id,
        $action_type,
        $description,
        $ip_address,
        $department_id
    ]);
}