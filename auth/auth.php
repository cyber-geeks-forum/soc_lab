<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: /soc_lab/public/login.php");
    exit;
}

/* Session timeout (30 mins) */
if (isset($_SESSION["LAST_ACTIVITY"]) &&
    (time() - $_SESSION["LAST_ACTIVITY"] > 1800)) {

    session_unset();
    session_destroy();
    header("Location: /soc_lab/public/login.php?timeout=1");
    exit;
}

$_SESSION["LAST_ACTIVITY"] = time();