<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/auth/auth.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/auth/authorize.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/config/db.php";

authorizeDepartmentAdmin();

$department_id = $_SESSION['department_id'];

/* ========= FILTERS ========= */

$action_type = $_GET['action_type'] ?? '';
$username    = $_GET['username'] ?? '';

$query = "
    SELECT activity_log.*, user.username 
    FROM activity_log
    LEFT JOIN user ON activity_log.user_id = user.user_id
    LEFT JOIN role r ON user.role_id = r.role_id
    WHERE user.department_id = ?
AND (r.role_name = 'member' OR user.user_id = ?)
";

$params = [$department_id, $_SESSION['user_id']];

if (!empty($action_type)) {
    $query .= " AND activity_log.action_type = ?";
    $params[] = $action_type;
}

if (!empty($username)) {
    $query .= " AND user.username LIKE ?";
    $params[] = "%$username%";
}

$query .= " ORDER BY activity_log.created_at DESC LIMIT 100";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$logs = $stmt->fetchAll();
?>

<?php include "../../includes/header.php"; ?>
<?php include "../../includes/navbar.php"; ?>

<h2>Department Activity Logs</h2>

<form method="GET">
    <input type="text" name="username" placeholder="Search by username">

    <select name="action_type">
        <option value="">All Actions</option>
        <option value="login">Login</option>
        <option value="failed_login">Failed Login</option>
        <option value="create_user">Create User</option>
        <option value="disable_user">Disable User</option>
        <option value="enable_user">Enable User</option>
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
            <td><?= htmlspecialchars($log['created_at']) ?></td>
            <td><?= htmlspecialchars($log['username'] ?? 'System') ?></td>
            <td><?= htmlspecialchars($log['action_type']) ?></td>
            <td><?= htmlspecialchars($log['action_description']) ?></td>
            <td><?= htmlspecialchars($log['ip_address']) ?></td>
        </tr>
    <?php endforeach; ?>

</table>

<?php include "../../includes/footer.php"; ?>