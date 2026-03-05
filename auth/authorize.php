<?php

// Prevent direct access if auth not loaded
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//Ensure user has one of the allowed roles

function authorizeRole($allowedRoles = []) {

    if (!isset($_SESSION['role_name'])) {
        header("Location: /soc_lab/public/login.php");
        exit;
    }

    if (!in_array($_SESSION['role_name'], $allowedRoles)) {

        // Optional: log this later to activity_log for SOC use-case

       header($_SERVER['DOCUMENT_ROOT'] . "/soc_lab/public/unauthorized.php");
        exit;
    }
}

//Ensure user belongs to a specific department

function authorizeDepartment($departmentId) {

    if (!isset($_SESSION['department_id'])) {
        header($_SERVER['DOCUMENT_ROOT'] . "/soc_lab/public/unauthorized.php");
        exit;
    }

    if ($_SESSION['department_id'] != $departmentId) {

        header($_SERVER['DOCUMENT_ROOT'] . "/soc_lab/public/unauthorized.php");
        exit;
    }
}

//Super Admin shortcut
function authorizeSuperAdmin() {

    authorizeRole(['super_admin']);
}

//Department Admin shortcut
function authorizeDepartmentAdmin() {

    authorizeRole(['department_admin']);
}

// Member shortcut
function authorizeMember() {

    authorizeRole(['member']);
}