<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to view items.";
    exit;
}

// Check if an item ID is provided in the URL
if (!isset($_GET['id'])) {
    echo "Invalid item ID.";
    exit;
}

$item_id = $_GET['id'];

// Retrieve item details from the database
$stmt = $conn->prepare("SELECT description, photo FROM Items WHERE id = ? AND created_by = ?");
$stmt->bind_param("ii", $item_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Item not found or you do not have permission to view this item.";
    exit;
}

$item = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark">

    <div class="container mt-5">
        <h2 class="text-center mb-4">View Item</h2>

        <div class="card shadow-sm">
            <div class="card-body">
                <!-- Display Item Photo -->
                <?php if ($item['photo']): ?>
                    <img src="uploads/<?= htmlspecialchars($item['photo']) ?>" class="img-fluid mb-3" alt="Item Photo">
                <?php endif; ?>

                <!-- Display Item Description -->
                <h5>Description</h5>
                <p><?= nl2br(htmlspecialchars($item['description'])) ?></p>


                <!-- Back to Items List -->
                <a href="index.php" class="btn btn-secondary mt-3">Back to Items List</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>