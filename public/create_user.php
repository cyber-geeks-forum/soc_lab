<?php
require '../config/db.php';

$username = "admin";
$password = "123";

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("
    INSERT INTO user (username, password_hash, role_id, department_id)
    VALUES (?, ?, 1, 1)
");

$stmt->execute([$username, $hash]);

echo "User created successfully!";