<?php
include 'db_connect.php';

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM Users WHERE id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        echo "User deleted successfully! <a href='manage_users.php'>Go back to users list</a>";
    } else {
        echo "Error: " . $conn->error;
    }
}
