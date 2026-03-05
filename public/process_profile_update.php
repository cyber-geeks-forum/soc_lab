<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/auth/auth.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/includes/logger.php";

$user_id = $_SESSION['user_id'];

$username = $_POST['username'];
$current_password = $_POST['current_password'] ?? null;
$new_password = $_POST['new_password'] ?? null;

/* =============================
   GET CURRENT USER DATA
============================= */

$stmt = $pdo->prepare("SELECT password_hash FROM user WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

/* =============================
   UPDATE USERNAME
============================= */

$updateUsername = $pdo->prepare("UPDATE user SET username = ? WHERE user_id = ?");
$updateUsername->execute([$username, $user_id]);

/* =============================
   UPDATE PASSWORD (IF PROVIDED)
============================= */

if (!empty($current_password) && !empty($new_password)) {

    if (!password_verify($current_password, $user['password_hash'])) {
        die("Current password is incorrect.");
    }

    $newHash = password_hash($new_password, PASSWORD_DEFAULT);

    $updatePassword = $pdo->prepare("UPDATE user SET password_hash = ? WHERE user_id = ?");
    $updatePassword->execute([$newHash, $user_id]);

    logActivity($pdo, "password_change", "User changed their password");
}

/* =============================
   UPDATE SESSION USERNAME
============================= */

$_SESSION['username'] = $username;

logActivity($pdo, "profile_update", "User updated profile");

header("Location: profile.php?success=1");
exit;