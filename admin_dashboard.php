<?php
session_start();
include 'db_connect.php';

// Ensure the user is an admin
if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] !== 'admin') {
    echo "Access denied. Admins only.";
    exit;
}

// Fetch users and items for admin overview
$users_result = $conn->query("SELECT id, username FROM Users");
$items_result = $conn->query("SELECT Items.id, Items.description, Items.photo, Users.username 
                             FROM Items JOIN Users ON Items.created_by = Users.id");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark">

    <div class="container mt-5">
        <h2 class="text-center mb-4  text-light">Admin Dashboard</h2>

        <!-- Users Management Section -->
        <h4 class="text-light">Manage Users <a href='manage_users.php'>-></a></h4>
        <table class="table table-bordered table-striped table-light">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <?php while ($user = $users_result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($user['id']) ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td>
                        <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <!-- <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteUserModal" data-user-id="<?= $user['id'] ?>">Delete</button> -->
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <!-- Items Management Section -->
        <h4 class="text-light">Manage Items <a href='manage_all_items.php'>-></a></h4>
        <table class="table table-bordered table-striped table-light">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Description</th>
                    <th>Photo</th>
                    <th>Created By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <?php while ($item = $items_result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($item['id']) ?></td>
                    <td><?= htmlspecialchars($item['description']) ?></td>
                    <td><img src="uploads/<?= htmlspecialchars($item['photo']) ?>" width="100"></td>
                    <td><?= htmlspecialchars($item['username']) ?></td>
                    <td>
                        <a href="edit_item.php?id=<?= $item['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <!-- <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteItemModal" data-item-id="<?= $item['id'] ?>">Delete</button> -->
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <!-- Delete User Modal -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteUserModalLabel">Confirm User Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this user?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteUserBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Item Modal -->
    <div class="modal fade" id="deleteItemModal" tabindex="-1" aria-labelledby="deleteItemModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteItemModalLabel">Confirm Item Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this item?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteItemBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Delete User
        const deleteUserModal = document.getElementById('deleteUserModal');
        let userIdToDelete = null;

        deleteUserModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            userIdToDelete = button.getAttribute('data-user-id');
        });

        document.getElementById('confirmDeleteUserBtn').addEventListener('click', function() {
            fetch('admin_dashboard.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    delete_user_id: userIdToDelete
                })
            }).then(() => window.location.reload());
        });

        // Delete Item
        const deleteItemModal = document.getElementById('deleteItemModal');
        let itemIdToDelete = null;

        deleteItemModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            itemIdToDelete = button.getAttribute('data-item-id');
        });

        document.getElementById('confirmDeleteItemBtn').addEventListener('click', function() {
            fetch('admin_dashboard.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    delete_item_id: itemIdToDelete
                })
            }).then(() => window.location.reload());
        });
    </script>

</body>

</html>