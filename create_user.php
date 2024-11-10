<?php
session_start();
include 'db_connect.php';

// Ensure only admins can access this page
if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

$alertMessage = '';
$alertType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $user_level = $_POST['user_level'];

    // Check if the username already exists
    $stmt = $conn->prepare("SELECT id FROM Users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $alertMessage = "Username already exists. Please choose a different username.";
        $alertType = "danger";
    } else {
        // Username is unique, proceed with insertion
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO Users (username, password, user_level) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $hashed_password, $user_level);

        if ($stmt->execute()) {
            $alertMessage = "User created successfully!";
            $alertType = "success";
        } else {
            $alertMessage = "Error: " . $conn->error;
            $alertType = "danger";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark">
    <div class="container mt-5">
        <h2 class="text-center mb-4 text-light">Create New User</h2>

        <!-- Display success or error messages -->
        <?php if ($alertMessage): ?>
            <div class="alert alert-<?= $alertType ?>"><?= $alertMessage ?></div>
        <?php endif; ?>

        <!-- User Creation Form -->
        <form action="create_user.php" method="post" class="border p-4 bg-white shadow-sm rounded">
            <div class="mb-3">
                <label for="username" class="form-label">Username:</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="user_level" class="form-label">User Level:</label>
                <select id="user_level" name="user_level" class="form-select">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary w-100">Create User</button>
        </form>

        <div class="mt-3">
            <a href="manage_users.php" class="btn btn-secondary">Back to User Management</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>