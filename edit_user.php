<?php
session_start();
include 'db_connect.php';

// Ensure the user is an admin
if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

// Fetch user details for editing
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $stmt = $conn->prepare("SELECT username, user_level FROM Users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        echo "<div class='alert alert-danger'>User not found.</div>";
        exit;
    }
} else {
    echo "<div class='alert alert-danger'>Invalid user ID.</div>";
    exit;
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

        <!-- Form to edit user details -->
        <form action="edit_user_process.php" method="post" class="border p-4 bg-white rounded shadow-sm">
            <!-- Hidden field for user ID -->
            <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id) ?>">

            <!-- Username field -->
            <div class="mb-3">
                <label for="username" class="form-label">Username:</label>
                <input type="text" id="username" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
            </div>

            <!-- User Level dropdown -->
            <div class="mb-3">
                <label for="user_level" class="form-label">User Level:</label>
                <select id="user_level" name="user_level" class="form-select">
                    <option value="user" <?= $user['user_level'] === 'user' ? 'selected' : '' ?>>User</option>
                    <option value="admin" <?= $user['user_level'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>

            <!-- Submit button -->
            <button type="submit" class="btn btn-primary w-100">Update User</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>