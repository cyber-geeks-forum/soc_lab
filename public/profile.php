<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/auth/auth.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/config/db.php";

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT username FROM user WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

<h2>My Profile</h2>

<form method="POST" action="process_profile_update.php">

    <label>Username</label>
    <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
    <br><br>

    <hr>

    <h3>Change Password</h3>

    <label>Current Password</label>
    <input type="password" name="current_password">
    <br><br>

    <label>New Password</label>
    <input type="password" name="new_password">
    <br><br>

    <button type="submit">Update Profile</button>

</form>