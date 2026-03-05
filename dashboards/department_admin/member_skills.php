<?php
require_once '../../auth/auth.php';
require_once '../../auth/authorize.php';
require_once '../../config/db.php';

authorizeRole(['department_admin']);

$department_id = $_SESSION['department_id'];

if (!isset($_GET['user_id'])) {
    die("User not specified.");
}

$user_id = (int) $_GET['user_id'];

/*
Verify user belongs to same department
and is NOT super_admin
*/
$stmt = $pdo->prepare("
    SELECT u.username
    FROM user u
    JOIN role r ON u.role_id = r.role_id
    WHERE u.user_id = ?
    AND u.department_id = ?
    AND r.role_name = 'member'
");
$stmt->execute([$user_id, $department_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Access denied.");
}

/*
Fetch skills
*/
$stmt = $pdo->prepare("
    SELECT skill_description, created_at
    FROM user_skill
    WHERE user_id = ?
    ORDER BY created_at DESC
");
$stmt->execute([$user_id]);
$skills = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>

<div class="main-content">

<h2>Skills - <?= htmlspecialchars($user['username']); ?></h2>

<table class="styled-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Skill Description</th>
            <th>Date Added</th>
        </tr>
    </thead>
    <tbody>

    <?php if (count($skills) > 0): ?>
        <?php foreach ($skills as $index => $skill): ?>
            <tr>
                <td><?= $index + 1; ?></td>
                <td><?= htmlspecialchars($skill['skill_description']); ?></td>
                <td><?= $skill['created_at']; ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="3">No skills documented yet.</td>
        </tr>
    <?php endif; ?>

    </tbody>
</table>

<div style="margin-top:20px;">
    <a href="tasks.php" class="btn-primary">Back to Tasks</a>
</div>

</div>

<?php include '../../includes/footer.php'; ?>