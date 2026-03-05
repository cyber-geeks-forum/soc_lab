<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/auth/auth.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/auth/authorize.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/includes/logger.php";

/*
|--------------------------------------------------------------------------
| Allow Super Admin OR Department Admin
|--------------------------------------------------------------------------
*/
if (!isset($_SESSION['role_name'])) {
    die("Unauthorized.");
}

$currentRole = $_SESSION['role_name'];

if ($currentRole !== 'super_admin' && $currentRole !== 'department_admin') {
    die("Access denied.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $user_id = $_POST['user_id'] ?? null;
    $action  = $_POST['action'] ?? null;

    if (!$user_id || !$action) {
        die("Invalid request.");
    }

    // Prevent self-disable
    if ($user_id == $_SESSION['user_id']) {
        die("You cannot disable yourself.");
    }

    /*
    |--------------------------------------------------------------------------
    | Fetch Target User WITH Role + Department
    |--------------------------------------------------------------------------
    */
    $stmt = $pdo->prepare("
        SELECT 
            u.username,
            u.status,
            u.department_id,
            r.role_name
        FROM user u
        LEFT JOIN role r ON u.role_id = r.role_id
        WHERE u.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        die("User not found.");
    }

    /*
    |--------------------------------------------------------------------------
    | Department Admin Restrictions
    |--------------------------------------------------------------------------
    */
    if ($currentRole === 'department_admin') {

        // Must be same department
        if ($user['department_id'] != $_SESSION['department_id']) {
            die("You cannot manage users outside your department.");
        }

        // Can only manage members
        if ($user['role_name'] !== 'member') {
            die("You can only manage members.");
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Determine New Status
    |--------------------------------------------------------------------------
    */
    if ($action === "disable") {
        $newStatus = "disabled";
        $logAction = "disable_user";
    } elseif ($action === "enable") {
        $newStatus = "active";
        $logAction = "enable_user";
    } else {
        die("Invalid action.");
    }

    /*
    |--------------------------------------------------------------------------
    | Update User
    |--------------------------------------------------------------------------
    */
    $update = $pdo->prepare("UPDATE user SET status = ? WHERE user_id = ?");
    $update->execute([$newStatus, $user_id]);

    /*
    |--------------------------------------------------------------------------
    | Log Activity
    |--------------------------------------------------------------------------
    */
    logActivity(
        $pdo,
        $logAction,
        ucfirst($newStatus) . " account: " . $user['username']
    );

    /*
    |--------------------------------------------------------------------------
    | Redirect Based On Role
    |--------------------------------------------------------------------------
    */
    if ($currentRole === 'super_admin') {
        header("Location: manage_users.php");
    } else {
        header("Location: manage_department_users.php");
    }

    exit;
}