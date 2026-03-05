<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/auth/auth.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/auth/authorize.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/config/db.php";

authorizeSuperAdmin();

/* ========= FILTERS ========= */

$action_type = $_GET['action_type'] ?? '';
$username    = $_GET['username'] ?? '';

$query = "
    SELECT activity_log.*, user.username 
    FROM activity_log
    LEFT JOIN user ON activity_log.user_id = user.user_id
    WHERE 1
";

$params = [];

if (!empty($action_type)) {
    $query .= " AND action_type = ?";
    $params[] = $action_type;
}

if (!empty($username)) {
    $query .= " AND user.username LIKE ?";
    $params[] = "%$username%";
}

$query .= " ORDER BY created_at DESC LIMIT 100";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$logs = $stmt->fetchAll();
?>
<?php include "../../includes/header.php"; ?>
 <?php include "../../includes/navbar.php"; ?>
<!DOCTYPE html>

<h2>Activity Logs</h2>

<form method="GET">
    <input type="text" name="username" placeholder="Search by username">
    
    <select name="action_type">
        <option value="">All Actions</option>
        <option value="login">Login</option>
        <option value="failed_login">Failed Login</option>
        <option value="create_user">Create User</option>
        <option value="delete_user">Delete User</option>
    </select>

    <button type="submit">Filter</button>
</form>

<table border="1" cellpadding="8">
    <tr>
        <th>Time</th>
        <th>User</th>
        <th>Action</th>
        <th>Description</th>
        <th>IP</th>
    </tr>

    <?php foreach ($logs as $log): ?>
        <tr>
            <td><?= $log['created_at'] ?></td>
            <td><?= $log['username'] ?? 'System' ?></td>
            <td><?= $log['action_type'] ?></td>
            <td><?= $log['action_description'] ?></td>
            <td><?= $log['ip_address'] ?></td>
        </tr>
    <?php endforeach; ?>

</table>
<?php include "../../includes/footer.php"; ?>


</body>
</html>