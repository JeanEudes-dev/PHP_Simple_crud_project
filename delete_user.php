<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit;
}

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM Users WHERE id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'User deleted successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error deleting user.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'User ID missing.']);
}
