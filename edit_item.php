<?php
session_start();
include 'db_connect.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to edit items.";
    exit;
}

$alertMessage = "";
$alertType = "";

if (isset($_GET['id'])) {
    $item_id = $_GET['id'];

    // Fetch the item details from the database
    $stmt = $conn->prepare("SELECT description, photo FROM Items WHERE id = ? AND created_by = ?");
    $stmt->bind_param("ii", $item_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $item = $result->fetch_assoc();
    } else {
        $alertMessage = "Item not found or you do not have permission to edit this item.";
        $alertType = "danger";
        exit;
    }
} else {
    $alertMessage = "Invalid item ID.";
    $alertType = "danger";
    exit;
}

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['item_id'])) {
    $item_id = $_POST['item_id'];
    $description = $_POST['description'];

    // Handle file upload (optional)
    $photo_filename = null;
    if (!empty($_FILES['photo']['name'])) {
        $photo = $_FILES['photo'];
        $photo_filename = time() . "_" . basename($photo['name']);
        $uploadDir = "uploads/";
        $uploadFilePath = $uploadDir . $photo_filename;

        if (move_uploaded_file($photo['tmp_name'], $uploadFilePath)) {
            // If new photo is uploaded, update it
            $stmt = $conn->prepare("UPDATE Items SET description = ?, photo = ? WHERE id = ? AND created_by = ?");
            $stmt->bind_param("ssii", $description, $photo_filename, $item_id, $_SESSION['user_id']);
        } else {
            $alertMessage = "Failed to upload new photo.";
            $alertType = "danger";
        }
    } else {
        // Update without changing the photo
        $stmt = $conn->prepare("UPDATE Items SET description = ? WHERE id = ? AND created_by = ?");
        $stmt->bind_param("sii", $description, $item_id, $_SESSION['user_id']);
    }

    // Execute the update query
    if ($stmt->execute()) {
        $alertMessage = "Item updated successfully!";
        $alertType = "success";
    } else {
        $alertMessage = "Error updating item: " . $conn->error;
        $alertType = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark">
    <div class="container mt-5">
        <h2 class="text-center mb-4">Edit Item</h2>

        <!-- Alert Message -->
        <?php if ($alertMessage): ?>
            <div class="alert alert-<?= $alertType ?> text-center">
                <?= $alertMessage ?>
                <?php if ($alertType === "success"): ?>
                    <a href="manage_items.php" class="alert-link">Go back to items list</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Edit Item Form -->
        <form action="edit_item.php?id=<?= $item_id ?>" method="post" enctype="multipart/form-data" class="border p-4 bg-white rounded shadow-sm">
            <input type="hidden" name="item_id" value="<?= $item_id ?>">

            <!-- Description Field -->
            <div class="mb-3">
                <label for="description" class="form-label">Description:</label>
                <textarea id="description" name="description" class="form-control" rows="4" required><?= htmlspecialchars($item['description']) ?></textarea>
            </div>

            <!-- Current Photo Preview -->
            <?php if ($item['photo']): ?>
                <div class="mb-3">
                    <label class="form-label">Current Photo:</label>
                    <div>
                        <img src="uploads/<?= htmlspecialchars($item['photo']) ?>" alt="Current Photo" width="150" class="border rounded">
                    </div>
                </div>
            <?php endif; ?>

            <!-- Photo Upload Field -->
            <div class="mb-3">
                <label for="photo" class="form-label">Upload New Photo (Optional):</label>
                <input type="file" id="photo" name="photo" class="form-control" accept="image/*">
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary w-100">Update Item</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>