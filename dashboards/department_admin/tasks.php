<?php
require_once '../../auth/auth.php';
require_once '../../auth/authorize.php';
require_once '../../config/db.php';

authorizeRole(['department_admin']);

$department_id = $_SESSION['department_id'];

/*
Fetch all assignments in department
*/
$stmt = $pdo->prepare("
    SELECT 
        u.username,
        t.task_id,
        t.title,
        ta.user_id,
        ta.status,
        ta.report_file,
        ta.rating
    FROM task_assignments ta
    JOIN task t ON ta.task_id = t.task_id
    JOIN user u ON ta.user_id = u.user_id
    WHERE t.department_id = ?
    ORDER BY u.username ASC, t.created_at DESC
");
$stmt->execute([$department_id]);
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Department_admin Dashboard</title>
    <link rel="stylesheet" href="../../css/dashboard.css">
</head>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>

<div class="main-content">

<h2>Department Task Assignments</h2>

<table class="styled-table">
    <thead>
        <tr>
            <th>Member</th>
            <th>Task</th>
            <th>Status</th>
            <th>Report</th>
            <th>Rating</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>

    <?php foreach ($assignments as $a): ?>
        <tr>
            <td><a href="member_skills.php?user_id=<?= $a['user_id']; ?>">
    <?= htmlspecialchars($a['username']); ?>
</a>
            <td><?= htmlspecialchars($a['title']); ?></td>
            <td>
                <span class="status <?= $a['status']; ?>">
                    <?= ucfirst($a['status']); ?>
                </span>
            </td>

            <td>
                <?php if ($a['report_file']): ?>
                    <a class="btn-link" 
                       href="../../uploads/task_reports/<?= $a['report_file']; ?>" 
                       target="_blank">View</a>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>

            <td>
                <?php if ($a['rating']): ?>
                    <span class="stars">
                        <?php for ($i=1;$i<=5;$i++): ?>
                            <?= $i <= $a['rating'] ? "⭐" : "☆"; ?>
                        <?php endfor; ?>
                    </span>
                <?php else: ?>
                    Not Rated
                <?php endif; ?>
            </td>

            <td>
                <?php if ($a['status'] === 'completed' && !$a['rating']): ?>
                    <form action="review_task.php" method="POST" class="inline-form">
                        <input type="hidden" name="task_id" value="<?= $a['task_id']; ?>">
                        <input type="hidden" name="user_id" value="<?= $a['user_id']; ?>">
                        <select name="rating" required>
                            <option value="">Rate</option>
                            <option value="1">1 ⭐</option>
                            <option value="2">2 ⭐⭐</option>
                            <option value="3">3 ⭐⭐⭐</option>
                            <option value="4">4 ⭐⭐⭐⭐</option>
                            <option value="5">5 ⭐⭐⭐⭐⭐</option>
                        </select>
                        <button type="submit" class="btn-primary">Submit</button>
                    </form>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>

    </tbody>
</table>

</div>

<?php include '../../includes/footer.php'; ?>