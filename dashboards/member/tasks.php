<?php
require_once '../../auth/auth.php';
require_once '../../auth/authorize.php';
require_once '../../config/db.php';

authorizeRole(['member']);

$user_id = $_SESSION['user_id'];

/*
Fetch tasks assigned to this member
*/
$stmt = $pdo->prepare("
    SELECT 
        t.task_id,
        t.title,
        t.description,
        ta.status,
        ta.report_file,
        ta.rating
    FROM task_assignments ta
    JOIN task t ON ta.task_id = t.task_id
    WHERE ta.user_id = ?
    ORDER BY t.created_at DESC
");
$stmt->execute([$user_id]);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>

<div class="main-content">

<h2>My Tasks</h2>

<table border="1" cellpadding="8">
<tr>
    <th>Title</th>
    <th>Description</th>
    <th>Status</th>
    <th>Report</th>
    <th>Rating</th>
    <th>Action</th>
</tr>

<?php foreach ($tasks as $task): ?>
<tr>
    <td><?= htmlspecialchars($task['title']); ?></td>
    <td><?= htmlspecialchars($task['description']); ?></td>
    <td><?= $task['status']; ?></td>

    <td>
        <?php if ($task['report_file']): ?>
            <a href="../../uploads/task_reports/<?= $task['report_file']; ?>" target="_blank">
                View Report
            </a>
        <?php else: ?>
            -
        <?php endif; ?>
    </td>

    <td>
        <?php if ($task['rating']): ?>
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <?= $i <= $task['rating'] ? "⭐" : "☆"; ?>
            <?php endfor; ?>
        <?php else: ?>
            Not Reviewed
        <?php endif; ?>
    </td>

    <td>
        <?php if (!$task['rating']): ?>
            <form action="upload_report.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="task_id" value="<?= $task['task_id']; ?>">
                <input type="file" name="report" accept=".pdf" required>
                <button type="submit">Submit / Update</button>
            </form>
        <?php else: ?>
            🔒 Locked
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>

</table>

</div>

<?php include '../../includes/footer.php'; ?>