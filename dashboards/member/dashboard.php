<?php
require_once '../../auth/auth.php';
require_once '../../auth/authorize.php';
require_once '../../config/db.php';

authorizeRole(['member']);

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

/*
Fetch assignment statistics
*/
$stmt = $pdo->prepare("
    SELECT 
        COUNT(*) as total_assignments,
        SUM(status = 'pending') as pending,
        SUM(status = 'in_progress') as in_progress,
        SUM(status = 'completed') as completed,
        AVG(rating) as avg_rating
    FROM task_assignments
    WHERE user_id = ?
");
$stmt->execute([$user_id]);
$stats = $stmt->fetch(PDO::FETCH_ASSOC);

$total = $stats['total_assignments'] ?? 0;
$pending = $stats['pending'] ?? 0;
$in_progress = $stats['in_progress'] ?? 0;
$completed = $stats['completed'] ?? 0;
$avg_rating = $stats['avg_rating'] ? round($stats['avg_rating'], 2) : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Member Dashboard</title>
    <link rel="stylesheet" href="../../css/dashboard.css">
</head>
<body>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>

<div class="main-content">

<h2>Member Dashboard</h2>
<p>Welcome, <strong><?= htmlspecialchars($username); ?></strong></p>

<div class="card-grid">

    <div class="card">
        <h4>Total Assignments</h4>
        <p><?= $total; ?></p>
    </div>

    <div class="card">
        <h4>Pending</h4>
        <p><?= $pending; ?></p>
    </div>

    <div class="card">
        <h4>In Progress</h4>
        <p><?= $in_progress; ?></p>
    </div>

    <div class="card">
        <h4>Completed</h4>
        <p><?= $completed; ?></p>
    </div>

    <div class="card">
        <h4>Average Rating</h4>
        <p>
            <?php if ($avg_rating): ?>
                <?= $avg_rating; ?> ⭐
            <?php else: ?>
                Not Rated Yet
            <?php endif; ?>
        </p>
    </div>
<a href="tasks.php" class="card action-card">
    <h3>View My Tasks</h3>
</a>

<a href="skills.php" class="card action-card">
    <h3>Skills Acquired</h3>
</a>
 <a href="activity_logs.php" class="card action-card">
        <h3>Activity Logs</h3>
        <p>Monitor all system activities</p>
    </a>
 <a href="/soc_lab/public/profile.php" class="card action-card">
        <h3>Profile Page</h3>
        <p>Change your username and Password</p>
    </a>

</div>
</div>

<?php include '../../includes/footer.php'; ?>