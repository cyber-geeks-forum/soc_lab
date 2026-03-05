<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/auth/auth.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/auth/authorize.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/includes/logger.php";

authorizeSuperAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $user_id = $_POST['user_id'];

    if ($user_id == $_SESSION['user_id']) {
        die("You cannot delete yourself.");
    }

    // Get username for logging
    $stmt = $pdo->prepare("SELECT username FROM user WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    // Delete
    $stmt = $pdo->prepare("DELETE FROM user WHERE user_id = ?");
    $stmt->execute([$user_id]);

    logActivity(
        $pdo,
        "delete_user",
        "Deleted user: " . $user['username']
    );

    header("Location: manage_users.php");
    exit;
}