<?php
session_start();
include 'db_connect.php';

// Check if the user is an admin
if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Initialize variables for feedback messages
$successMessage = "";
$errorMessage = "";

// Handle form submission for updating user information
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $user_level = $_POST['user_level'];

    $stmt = $conn->prepare("UPDATE Users SET username = ?, user_level = ? WHERE id = ?");
    $stmt->bind_param("ssi", $username, $user_level, $user_id);

    if ($stmt->execute()) {
        $successMessage = "User updated successfully!";
    } else {
        $errorMessage = "Error updating user: " . $conn->error;
    }
}

// Fetch user details for display in the form
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $stmt = $conn->prepare("SELECT username, user_level FROM Users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        $errorMessage = "User not found.";
    }
} else {
    $errorMessage = "Invalid user ID.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-center mb-4">Edit User</h2>

        <!-- Display success or error messages -->
        <?php if ($successMessage): ?>
            <div class="alert alert-success"><?= $successMessage ?></div>
        <?php elseif ($errorMessage): ?>
            <div class="alert alert-danger"><?= $errorMessage ?></div>
        <?php endif; ?>

        <!-- Form to edit user details -->
        <form action="edit_user.php?id=<?= htmlspecialchars($user_id) ?>" method="post" class="border p-4 bg-white rounded shadow-sm">
            <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id) ?>">

            <!-- Username field -->
            <div class="mb-3">
                <label for="username" class="form-label">Username:</label>
                <input type="text" id="username" name="username" class="form-control" value="<?= htmlspecialchars($user['username'] ?? '') ?>" required>
            </div>

            <!-- User Level dropdown -->
            <div class="mb-3">
                <label for="user_level" class="form-label">User Level:</label>
                <select id="user_level" name="user_level" class="form-select">
                    <option value="user" <?= (isset($user['user_level']) && $user['user_level'] === 'user') ? 'selected' : '' ?>>User</option>
                    <option value="admin" <?= (isset($user['user_level']) && $user['user_level'] === 'admin') ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>

            <!-- Submit button -->
            <button type="submit" class="btn btn-primary w-100">Update User</button>
        </form>

        <!-- Back link -->
        <div class="mt-3">
            <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>