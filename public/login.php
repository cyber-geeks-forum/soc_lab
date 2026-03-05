<?php
require_once '../config/db.php';
//require_once '../auth/auth.php';
require_once '../auth/logger.php';
?>

<?php include '../includes/header.php'; ?>

<div class="card" style="width:350px;margin:100px auto;text-align:center;">

    <h2>Login</h2>

    <form action="/soc_lab/auth/login_process.php" method="POST">

        <input type="text" name="username" placeholder="Username" required><br><br>

        <input type="password" name="password" placeholder="Password" required><br><br>

        <button type="submit">Login</button>

    </form>

</div>

<?php include '../includes/footer.php'; ?>