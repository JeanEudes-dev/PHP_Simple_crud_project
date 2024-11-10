<?php
session_start();
include 'db_connect.php';

// Initialize alert messages
$alertMessage = "";
$alertType = "";

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $description = $_POST['description'];
    $photo = $_FILES['photo'];

    // Handle file upload
    $photo_filename = time() . "_" . basename($photo['name']);
    $uploadDir = "uploads/";
    $uploadFilePath = $uploadDir . $photo_filename;

    if (move_uploaded_file($photo['tmp_name'], $uploadFilePath)) {
        // Insert item into the database
        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("INSERT INTO Items (description, photo, created_by) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $description, $photo_filename, $user_id);

        if ($stmt->execute()) {
            // Success alert
            $alertMessage = "Item created successfully!";
            $alertType = "success";

            // Redirect to manage items page after a short delay
            header("Refresh: 3; URL=manage_items.php");
        } else {
            // Database error
            $alertMessage = "Error: " . $conn->error;
            $alertType = "danger";
        }
    } else {
        // File upload error
        $alertMessage = "Failed to upload photo. Please try again.";
        $alertType = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <h2 class="text-center mb-4">Create New Item</h2>

        <!-- Alert Message -->
        <?php if ($alertMessage): ?>
            <div class="alert alert-<?= $alertType ?> text-center">
                <?= $alertMessage ?>
                <?php if ($alertType === "success"): ?>
                    <p>Redirecting to the items list...</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Form for creating an item -->
        <form action="create_item.php" method="post" enctype="multipart/form-data" class="border p-4 bg-white rounded shadow-sm">
            <!-- Description Field -->
            <div class="mb-3">
                <label for="description" class="form-label">Description:</label>
                <textarea id="description" name="description" class="form-control" rows="4" required></textarea>
            </div>

            <!-- Photo Upload Field -->
            <div class="mb-3">
                <label for="photo" class="form-label">Upload Photo:</label>
                <input type="file" id="photo" name="photo" class="form-control" accept="image/*" required>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary w-100">Create Item</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>