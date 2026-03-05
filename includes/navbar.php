<div class="navbar">
  <div>
       Welcome, <?php echo $_SESSION['username']; ?><br>
        Role: <span class="role"><?php echo $_SESSION['role_name']; ?></span><br>
        <a href="/soc_lab/auth/logout.php">Logout</a>
    </div>
</div>

<div class="container">