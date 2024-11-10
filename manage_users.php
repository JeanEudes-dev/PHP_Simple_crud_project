<?php
session_start();
include 'db_connect.php';

// Ensure only admins can access this page
if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

// Handle feedback for delete action
$alertMessage = '';
$alertType = '';
if (isset($_GET['delete_status'])) {
    if ($_GET['delete_status'] === 'success') {
        $alertMessage = "User deleted successfully!";
        $alertType = "success";
    } else {
        $alertMessage = "Error deleting user.";
        $alertType = "danger";
    }
}

// Fetch all users
$result = $conn->query("SELECT id, username, user_level FROM Users");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-center mb-4">Manage Users</h2>

        <!-- Display success or error messages -->
        <?php if ($alertMessage): ?>
            <div class="alert alert-<?= $alertType ?>"><?= $alertMessage ?></div>
        <?php endif; ?>

        <!-- Create New User Link -->
        <div class="mb-3">
            <a href="create_user.php" class="btn btn-success">Create New User</a>
        </div>

        <!-- Users Table -->
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>User Level</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id']) ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['user_level']) ?></td>
                        <td>
                            <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal" data-user-id="<?= $user['id'] ?>">Delete</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div class="mb-3">
            <a href="admin_dashboard.php" class="btn btn-info  float-right">Back to Dashboard</a>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this user? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmDeleteBtn" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handle delete confirmation
        document.addEventListener('show.bs.modal', function(event) {
            if (event.target.id === 'deleteModal') {
                const button = event.relatedTarget;
                const userId = button.getAttribute('data-user-id');
                const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

                confirmDeleteBtn.onclick = function() {
                    // Trigger delete action within the same page, without navigating away
                    window.location.href = `delete_user.php?id=${userId}`;
                };
            }
        });
    </script>
</body>

</html>