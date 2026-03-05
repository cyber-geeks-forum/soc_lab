<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/auth/auth.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/auth/authorize.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/config/db.php";

authorizeDepartmentAdmin();

$department_id = $_SESSION['department_id'];

/* ===============================
   GET DEPARTMENT NAME
=================================*/
$stmt = $pdo->prepare("SELECT department_name FROM department WHERE department_id = ?");
$stmt->execute([$department_id]);
$department = $stmt->fetch();

/* ===============================
   FETCH MEMBERS (Same Department)
=================================*/
try {
    $stmt = $pdo->prepare("
        SELECT 
            u.user_id, 
            u.username, 
            u.status,
            r.role_name
        FROM user u
        LEFT JOIN role r ON u.role_id = r.role_id
        WHERE u.department_id = :department_id
        AND r.role_name = 'member'
        ORDER BY u.username ASC
    ");

    $stmt->execute([
        ':department_id' => $department_id
    ]);

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
    $users = [];
}
?>

<?php include "../../includes/header.php"; ?>
<?php include "../../includes/navbar.php"; ?>

<h2>Manage Department Members</h2>

<p>
    Department: 
    <strong><?= htmlspecialchars($department['department_name']) ?></strong>
</p>

<hr>

<!-- ===============================
     CREATE MEMBER SECTION
=================================-->

<h3>Create New Member</h3>

<form method="POST" action="process_create_member.php">

    <label>Username</label><br>
    <input type="text" name="username" required><br><br>

    <label>Password</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Create Member</button>

</form>

<hr>

<!-- ===============================
     MEMBER LIST SECTION
=================================-->

<h3>Department Members</h3>

<table border="1" cellpadding="8">
<tr>
    <th>Username</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php foreach ($users as $u): ?>
<tr>
    <td><?= htmlspecialchars($u['username']) ?></td>

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