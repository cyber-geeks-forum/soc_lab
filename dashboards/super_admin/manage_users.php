<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/auth/auth.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/auth/authorize.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/config/db.php";

authorizeSuperAdmin();

// Fetch users with role and department
try {
    $stmt = $pdo->query("
        SELECT 
            u.user_id, 
            u.username, 
            u.status,
            r.role_name,
            d.department_name
        FROM user u
        LEFT JOIN role r ON u.role_id = r.role_id
        LEFT JOIN department d ON u.department_id = d.department_id
        ORDER BY u.username ASC
    ");

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
    $users = []; // fallback so foreach doesn’t break
}
?>

<?php include "../../includes/header.php"; ?>
<?php include "../../includes/navbar.php"; ?>

<h2>Manage Users</h2>

<table border="1" cellpadding="8">
<tr>
    <th>Username</th>
    <th>Role</th>
    <th>Department</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php foreach ($users as $u): ?>
<tr>
    <td><?= htmlspecialchars($u['username']) ?></td>

    <td><?= htmlspecialchars($u['role_name'] ?? 'N/A') ?></td>

    <td><?= htmlspecialchars($u['department_name'] ?? 'N/A') ?></td>

    <td><?= htmlspecialchars($u['status']) ?></td>

    <td>

        <?php if ($u['user_id'] != $_SESSION['user_id']): ?>

            <?php if ($u['status'] === 'active'): ?>

                <form method="POST" action="toggle_user_status.php"
                      onsubmit="return confirm('Disable this user?');">
                    <input type="hidden" name="user_id" value="<?= $u['user_id'] ?>">
                    <input type="hidden" name="action" value="disable">
                    <button type="submit" style="color:red;">Disable</button>
                </form>

            <?php else: ?>

                <form method="POST" action="toggle_user_status.php"
                      onsubmit="return confirm('Enable this user?');">
                    <input type="hidden" name="user_id" value="<?= $u['user_id'] ?>">
                    <input type="hidden" name="action" value="enable">
                    <button type="submit" style="color:green;">Enable</button>
                </form>

            <?php endif; ?>

        <?php else: ?>
            (You)
        <?php endif; ?>

    </td>
</tr>
<?php endforeach; ?>

</table>

<?php include "../../includes/footer.php"; ?>