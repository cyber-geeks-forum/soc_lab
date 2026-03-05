<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/auth/auth.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/auth/authorize.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/config/db.php";

authorizeDepartmentAdmin();

$department_id = $_SESSION['department_id'];

/* ===============================
   FETCH DEPARTMENT METRICS
================================ */

// Total users in this department
$stmt = $pdo->prepare("SELECT COUNT(*) FROM user WHERE department_id = ?");
$stmt->execute([$department_id]);
$totalUsers = $stmt->fetchColumn();

/*
Total tasks created in department
*/
$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM task
    WHERE department_id = ?
");
$stmt->execute([$department_id]);
$total_tasks = $stmt->fetchColumn();

/*
Total assignments in department
*/
$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM task_assignments ta
    JOIN task t ON ta.task_id = t.task_id
    WHERE t.department_id = ?
");
$stmt->execute([$department_id]);
$total_assignments = $stmt->fetchColumn();

/*
Pending assignments
*/
$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM task_assignments ta
    JOIN task t ON ta.task_id = t.task_id
    WHERE t.department_id = ?
    AND ta.status = 'pending'
");
$stmt->execute([$department_id]);
$pending = $stmt->fetchColumn();

/*
Completed assignments
*/
$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM task_assignments ta
    JOIN task t ON ta.task_id = t.task_id
    WHERE t.department_id = ?
    AND ta.status = 'completed'
");
$stmt->execute([$department_id]);
$completed = $stmt->fetchColumn();

/*
Average rating
*/
$stmt = $pdo->prepare("
    SELECT AVG(ta.rating)
    FROM task_assignments ta
    JOIN task t ON ta.task_id = t.task_id
    WHERE t.department_id = ?
    AND ta.rating IS NOT NULL
");
$stmt->execute([$department_id]);
$avg_rating = round($stmt->fetchColumn(), 2);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Department_admin Dashboard</title>
    <link rel="stylesheet" href="../../css/dashboard.css">
</head>
<body>

<?php include "../../includes/header.php"; ?>
<?php include "../../includes/navbar.php"; ?>

<div class="container">

    <main class="main-content">

        <h1>Department Admin Dashboard</h1>

            <p>Welcome <?= $_SESSION['username'] ?> </p>

<div class="card-grid">

    <div class="card">
        <h3>Department Users</h3>
        <p><?= $totalUsers ?></p>
    </div>

    <div class="card">
        <h4>Total Tasks</h4>
        <p><?= $total_tasks; ?></p>
    </div>

    <div class="card">
        <h4>Total Assignments</h4>
        <p><?= $total_assignments; ?></p>
    </div>

    <div class="card">
        <h4>Pending</h4>
        <p><?= $pending; ?></p>
    </div>

    <div class="card">
        <h4>Completed</h4>
        <p><?= $completed; ?></p>
    </div>

    <div class="card">
        <h4>Avg Rating</h4>
        <p><?= $avg_rating ?: 'N/A'; ?></p>
    </div>

    <a href="/soc_lab/public/profile.php" class="card action-card">
        <h3>Profile Page</h3>
        <p>Change your username and Password</p>
    </a>

    <a href="manage_department_users.php" class="card action-card">
        <h3>Manage Department Users</h3>
        <p>View and control members</p>
    </a>

    <a href="create_task.php" class="card action-card">
        <h3>Create Task</h3>
    </a>

    <a href="tasks.php" class="card action-card">
        <h3>Manage Tasks</h3>
    </a>

    <a href="activity_logs.php" class="card action-card">
        <h3>Activity Logs</h3>
        <p>Monitor all system activities</p>
    </a>
</div>
    </main>
</div>
</div>
<?php include "../../includes/footer.php"; ?>
