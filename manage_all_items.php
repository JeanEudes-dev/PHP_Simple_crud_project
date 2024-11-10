<?php
session_start();
include 'db_connect.php';

// Ensure only admins can access this page
if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] !== 'admin') {
    header('Location: dashboard.php');
    exit;
}

// Retrieve all items
$result = $conn->query("SELECT id, description, photo, created_by FROM Items");

$alertMessage = '';
$alertType = '';

// Handle deletion requests
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    $stmt = $conn->prepare("DELETE FROM Items WHERE id = ?");
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Item deleted successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error deleting item.']);
    }
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage All Items</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark">
    <div class="container my-5">
        <h2 class="text-center mb-4 text-light">Manage All Items</h2>

        <?php if ($alertMessage): ?>
            <div class="alert alert-<?= $alertType ?> text-center">
                <?= $alertMessage ?>
            </div>
        <?php endif; ?>

        <div class="mb-3">
            <a href="admin_dashboard.php" class="btn btn-info  float-right">Back to Dashboard</a>
        </div><br /><br />
        <?php if ($_SESSION['user_level'] == 'user' || $_SESSION['user_level'] == 'admin') { ?>
            <div class="mt-4">
                <a href="create_item.php" class="btn btn-primary">Create New Item</a>
            </div><br />
        <?php } ?>
        <!-- Check if there are any items -->
        <?php if ($result->num_rows > 0): ?>
            <table class="table table-bordered table-striped table-hover table-light">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Description</th>
                        <th>Photo</th>
                        <th>Created By (User ID)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['id']) ?></td>
                            <td><?= htmlspecialchars($item['description']) ?></td>
                            <td><img src="uploads/<?= htmlspecialchars($item['photo']) ?>" width="100" alt="Item Photo"></td>
                            <td><?= htmlspecialchars($item['created_by']) ?></td>
                            <td>
                                <a href="edit_item.php?id=<?= $item['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal" data-item-id="<?= $item['id'] ?>">Delete</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info" role="alert">
                There are no items to manage.
            </div>
        <?php endif; ?>
    </div>

    <!-- Delete confirmation modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this item?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Delete</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handle delete confirmation with AJAX
        const deleteModal = document.getElementById('deleteModal');
        let itemIdToDelete = null;

        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            itemIdToDelete = button.getAttribute('data-item-id');
        });

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            fetch('manage_all_items.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        delete_id: itemIdToDelete
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        window.location.href = 'manage_all_items.php?delete_status=success';
                    } else {
                        window.location.href = 'manage_all_items.php?delete_status=error';
                    }
                })
                .catch(() => {
                    window.location.href = 'manage_all_items.php?delete_status=error';
                });
        });
    </script>
</body>

</html>