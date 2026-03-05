<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/auth/auth.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/auth/authorize.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/includes/logger.php";

authorizeDepartmentAdmin();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $department_id = $_SESSION['department_id'];

    // Get MEMBER role ID
    $stmt = $pdo->prepare("SELECT role_id FROM role WHERE role_name = 'member'");
    $stmt->execute();
    $role = $stmt->fetch();

    if (!$role) {
        die("Member role not found.");
    }

    $role_id = $role['role_id'];

    // Insert user
    $stmt = $pdo->prepare("
        INSERT INTO user (username, password_hash, role_id, department_id)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->execute([
        $username,
        $password,
        $role_id,
        $department_id
    ]);

    logActivity(
        $pdo,
        "create_member",
        "Department admin created member: $username"
    );

    header("Location: dashboard.php?success=member_created");
    exit;
}