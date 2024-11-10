<?php
session_start();
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit;
}

// Fetch all items from the database
$user_id = $_SESSION['user_id'];
$result = $conn->prepare("SELECT id, description, photo FROM Items WHERE created_by = ?");
$result->bind_param("i", $user_id);
$result->execute();
$items = $result->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Project - Home</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your custom styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">CRUD Project</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="manage_items.php">Manage Items</a>
                    </li>
                    <?php if ($_SESSION['user_level'] == 'admin') { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_dashboard.php">Admin Dashboard</a>
                        </li>
                    <?php } ?>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content Section -->
    <div class="container mt-5">
        <h1>Welcome, <?= $_SESSION['username']; ?>!</h1> <!-- Display logged-in user's name -->
        <p>Here is a list of all items:</p>

        <!-- List of Items -->
        <div class="list-group">
            <?php if ($items->num_rows > 0): ?>
                <?php while ($item = $items->fetch_assoc()): ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span><?= htmlspecialchars($item['description']); ?></span> <!-- Item Description -->
                        <a href="view_item.php?id=<?= $item['id']; ?>" class="btn btn-info btn-sm">View</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No items found.</p>
            <?php endif; ?>
        </div>

        <!-- Optionally, a button to create new item (for logged-in users) -->
        <?php if ($_SESSION['user_level'] == 'user' || $_SESSION['user_level'] == 'admin') { ?>
            <div class="mt-4">
                <a href="create_item.php" class="btn btn-primary">Create New Item</a>
            </div>
        <?php } ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>