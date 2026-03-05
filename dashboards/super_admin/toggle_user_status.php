<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/auth/auth.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/auth/authorize.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/includes/logger.php";

authorizeSuperAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $user_id = $_POST['user_id'];
    $action  = $_POST['action'];

    if ($user_id == $_SESSION['user_id']) {
        die("You cannot disable yourself.");
    }

    $stmt = $pdo->prepare("SELECT username, status FROM user WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        die("User not found.");
    }

    if ($action === "disable") {
        $newStatus = "disabled";
        $logAction = "disable_user";
    } else {
        $newStatus = "active";
        $logAction = "enable_user";
    }

    $update = $pdo->prepare("UPDATE user SET status = ? WHERE user_id = ?");
    $update->execute([$newStatus, $user_id]);

    logActivity(
        $pdo,
        $logAction,
        ucfirst($newStatus) . " account: " . $user['username']
    );

    header("Location: manage_users.php");
    exit;
}