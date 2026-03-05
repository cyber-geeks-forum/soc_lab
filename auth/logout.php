<?php
session_start();

require_once "../config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/includes/logger.php";

// Log BEFORE destroying session
if (isset($_SESSION['user_id'])) {
    logActivity(
        $pdo,
        "logout",
        "User {$_SESSION['username']} logged out"
    );
}

session_unset();
session_destroy();

header("Location: ../public/login.php");
exit;