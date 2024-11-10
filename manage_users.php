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

<body class="bg-dark">
    <div class="container mt-5">
        <h2 class="text-center mb-4 text-light">Manage Users</h2>

        <!-- Alert Message -->
        <div id="alertMessage" class="alert d-none text-center" role="alert"></div>

        <!-- Create New User Link -->
        <div class="mb-3">
            <a href="create_user.php" class="btn btn-success">Create New User</a>
        </div>

        <!-- Users Table -->
        <table class="table table-bordered table-striped table-light">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>User Level</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $result->fetch_assoc()): ?>
                    <tr id="user-<?= $user['id'] ?>">
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
            <a href="admin_dashboard.php" class="btn btn-info float-right">Back to Dashboard</a>
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

    <!-- Bootstrap JS and Custom JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let userIdToDelete = null;
            const alertMessage = document.getElementById('alertMessage');

            document.getElementById('deleteModal').addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                userIdToDelete = button.getAttribute('data-user-id');
            });

            document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
                if (userIdToDelete) {
                    fetch(`delete_user.php?id=${userIdToDelete}`)
                        .then(response => response.json())
                        .then(data => {
                            // Close modal
                            const deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                            deleteModal.hide();

                            // Show alert message
                            alertMessage.textContent = data.message;
                            alertMessage.className = `alert alert-${data.status === 'success' ? 'success' : 'danger'} d-block`;

                            // Remove user row if deletion was successful
                            if (data.status === 'success') {
                                const userRow = document.getElementById(`user-${userIdToDelete}`);
                                if (userRow) userRow.remove();
                            }
                        })
                        .catch(() => {
                            alertMessage.textContent = 'An error occurred while processing your request.';
                            alertMessage.className = 'alert alert-danger d-block';
                        });
                }
            });
        });
    </script>
</body>

</html>