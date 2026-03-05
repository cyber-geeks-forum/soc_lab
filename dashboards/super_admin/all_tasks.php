<?php
require_once '../../auth/auth.php';
require_once '../../auth/authorize.php';
require_once '../../config/db.php';

authorizeRole(['super_admin']);

/*
Fetch ALL task assignments system-wide
*/
$stmt = $pdo->query("
    SELECT 
        d.department_name,
        t.title,
        u.username,
        ta.status,
        ta.rating,
        t.created_at,
        ta.completed_at
    FROM task_assignments ta
    JOIN task t ON ta.task_id = t.task_id
    JOIN user u ON ta.user_id = u.user_id
    JOIN department d ON t.department_id = d.department_id
    ORDER BY d.department_name ASC, t.created_at DESC
");

$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>

<div class="main-content">

<h2>System-Wide Task Overview</h2>

<table class="styled-table">
    <thead>
        <tr>
            <th>Department</th>
            <th>Task</th>
            <th>Member</th>
            <th>Status</th>
            <th>Rating</th>
            <th>Created</th>
            <th>Completed</th>
        </tr>
    </thead>
    <tbody>

    <?php if (count($assignments) > 0): ?>
        <?php foreach ($assignments as $a): ?>
            <tr>
                <td><?= htmlspecialchars($a['department_name']); ?></td>
                <td><?= htmlspecialchars($a['title']); ?></td>
                <td><?= htmlspecialchars($a['username']); ?></td>

                <td>
                    <span class="status <?= $a['status']; ?>">
                        <?= ucfirst($a['status']); ?>
                    </span>
                </td>

                <td>
                    <?php if ($a['rating']): ?>
                        <?php for ($i=1;$i<=5;$i++): ?>
                            <?= $i <= $a['rating'] ? "⭐" : "☆"; ?>
                        <?php endfor; ?>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>

                <td><?= $a['created_at']; ?></td>
                <td><?= $a['completed_at'] ?? '-'; ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="7">No task activity found.</td>
        </tr>
    <?php endif; ?>

    </tbody>
</table>

</div>

<?php include '../../includes/footer.php'; ?> 