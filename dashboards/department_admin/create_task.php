<?php
require_once '../../auth/auth.php';
require_once '../../auth/authorize.php';
require_once '../../config/db.php';

authorizeRole(['department_admin']);

$department_id = $_SESSION['department_id'];

/*
Fetch members in this department
*/
$stmt = $pdo->prepare("
    SELECT user_id, username
    FROM user
    WHERE department_id = ?
    AND role_id = 3
    AND status = 'active'
");
$stmt->execute([$department_id]);
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>

<div class="main-content">

<h2>Create Task</h2>

<form action="process_create_task.php" method="POST">

    <label>Title</label><br>
    <input type="text" name="title" required><br><br>

    <label>Description</label><br>
    <textarea name="description" required></textarea><br><br>

    <label>Assign To (Hold CTRL to select multiple)</label><br>
    <select name="assigned_users[]" multiple required style="height:150px; width:300px;">
        <?php foreach ($members as $member): ?>
            <option value="<?= $member['user_id']; ?>">
                <?= htmlspecialchars($member['username']); ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <button type="submit" class="btn-primary">Create Task</button>

</form>

</div>

<?php include '../../includes/footer.php'; ?>