<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $user_level = $_POST['user_level'];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO Users (username, password, user_level) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $hashed_password, $user_level);

    if ($stmt->execute()) {
        echo "User created successfully! <a href='manage_users.php'>Go back to users list</a>";
    } else {
        echo "Error: " . $conn->error;
    }
}
