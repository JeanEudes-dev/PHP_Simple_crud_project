<?php
session_start();
include 'db_connect.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Get all items created by the logged-in user
$result = $conn->prepare("SELECT id, description, photo FROM Items WHERE created_by = ?");
$result->bind_param("i", $user_id);
$result->execute();
$items = $result->get_result();

$alertMessage = "";
$alertType = "";

// Check for deletion status in URL parameters
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    $stmt = $conn->prepare("DELETE FROM Items WHERE id = ? AND created_by = ?");
    $stmt->bind_param("ii", $delete_id, $user_id);

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
    <title>Manage My Items</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css"> <!-- Custom CSS for additional styles -->
</head>

<body class="bg-dark text-light">

    <!-- Container for content -->
    <div class="container my-5">
        <h2 class="text-center mb-4 text-light">Manage My Items</h2>

        <?php if ($alertMessage): ?>
            <div class="alert alert-<?= $alertType ?> text-center">
                <?= $alertMessage ?>
            </div>
        <?php endif; ?>

        <!-- Check if there are no items -->
        <?php if ($items->num_rows > 0): ?>
            <a href="create_item.php" class="btn btn-primary mb-3">Create New Item</a>

            <table class="table table-bordered table-light">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Description</th>
                        <th>Photo</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = $items->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['id']); ?></td>
                            <td><?php echo htmlspecialchars($item['description']); ?></td>
                            <td><img src="uploads/<?php echo htmlspecialchars($item['photo']); ?>" width="100" alt="Item Photo"></td>
                            <td>
                                <a href="edit_item.php?id=<?php echo $item['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal" data-item-id="<?= $item['id'] ?>">Delete</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

        <?php else: ?>
            <div class="alert alert-info" role="alert">
                You have no items to manage. <a href="create_item.php" class="alert-link">Create a new item</a> now.
            </div>
        <?php endif; ?>
        <div class="mb-3">
            <a href="index.php" class="btn btn-info  float-right">Home</a>
        </div
            </div>

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
                fetch('manage_items.php', {
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
                            window.location.href = 'manage_items.php?delete_status=success';
                        } else {
                            window.location.href = 'manage_items.php?delete_status=error';
                        }
                    })
                    .catch(() => {
                        window.location.href = 'manage_items.php?delete_status=error';
                    });
            });
        </script>
</body>

</html>