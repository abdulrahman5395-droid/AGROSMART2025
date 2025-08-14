<?php
session_start();

// Ensure the user is logged in as a buyer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'buyer') {
    header("Location: ../auth/login.php");
    exit();
}

// Include the database connection
require '../db_connect.php';

// Handle search and filter
$search = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? '');

$conditions = ["i.stock > 0"];
$params = [];
$types = '';

if (!empty($search)) {
    $conditions[] = "i.name LIKE ?";
    $params[] = "%$search%";
    $types .= 's';
}

if (!empty($category)) {
    $conditions[] = "i.category = ?";
    $params[] = $category;
    $types .= 's';
}

$whereClause = implode(' AND ', $conditions);

$sql = "SELECT i.items_id, i.name, i.description, i.price, i.stock, i.category, i.image_url, u.firstname AS farmer_name 
        FROM items i 
        JOIN users u ON i.farmer_id = u.users_id 
        WHERE $whereClause
        ORDER BY i.name ASC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketplace - AGRO-SMART</title>
    <link rel="stylesheet" href="../styles.css"> <!-- Link to centralized CSS -->
    <style>
        /* Inline styles for quick demonstration (should be in external CSS ideally) */
        body {
            background: url('../images/pexels-timmossholder-974314.jpg') center/cover no-repeat;
            color: white;
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        header.topnav {
            background-color: rgba(0, 0, 0, 0.7);
            padding: 10px;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            text-align: center;
        }
        .topnav a {
            color: white;
            margin: 0 10px;
            text-decoration: none;
        }
        main.marketplace-page {
            flex: 1;
            padding: 80px 20px 80px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .marketplace-box {
            background: rgba(0, 0, 0, 0.8);
            padding: 20px;
            border-radius: 10px;
            max-width: 1200px;
            width: 100%;
            text-align: center;
        }
        .search-filter {
            margin-bottom: 20px;
        }
        .search-filter input,
        .search-filter select,
        .search-filter button {
            padding: 10px;
            margin: 5px;
            border-radius: 5px;
            border: none;
        }
        .search-filter button {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }
        .search-filter button:hover {
            background-color: #45a049;
        }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .product-card {
            background: rgba(255, 255, 255, 0.9);
            color: #000;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
        }
        .product-card img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
        }
        .product-card h3 {
            margin: 10px 0;
            font-size: 1.2em;
        }
        .product-card p {
            margin: 5px 0;
        }
        .btn {
            display: inline-block;
            margin-top: 10px;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #45a049;
        }
        footer {
            background: rgba(0, 0, 0, 0.8);
            padding: 15px;
            text-align: center;
            color: #fff;
            font-size: 0.9em;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
        footer a {
            color: #4CAF50;
            text-decoration: none;
        }
        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<header class="topnav">
    <a href="../index.php">Home</a>
    <a href="buyer.php">Buyer Dashboard</a>
    <a href="cart.php">Cart</a>
    <a href="../auth/logout.php">Logout</a>
</header>

<main class="marketplace-page">
    <div class="marketplace-box">
        <h2>Marketplace</h2>

        <!-- Search and Filter Form -->
        <form class="search-filter" method="GET" action="">
            <input type="text" name="search" placeholder="Search products..." value="<?= htmlspecialchars($search) ?>">
            <select name="category">
                <option value="">All Categories</option>
                <option value="fruits" <?= $category === 'fruits' ? 'selected' : '' ?>>Fruits</option>
                <option value="grains" <?= $category === 'grains' ? 'selected' : '' ?>>Grains</option>
                <option value="vegetables" <?= $category === 'vegetables' ? 'selected' : '' ?>>Vegetables</option>
            </select>
            <button type="submit">Search</button>
        </form>

        <!-- Products Grid -->
        <?php if ($result->num_rows > 0): ?>
            <div class="product-grid">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="product-card">
                        <img src="<?= '../uploads/' . htmlspecialchars($row['image_url'] ?? 'images/default.jpg') ?>" alt="<?= htmlspecialchars($row['name'] ?? 'Product') ?>">
                        <h3><?= htmlspecialchars($row['name']) ?></h3>
                        <p><?= htmlspecialchars($row['description']) ?></p>
                        <p><strong>Price:</strong> ₦<?= number_format($row['price'], 2) ?></p>
                        <p><strong>Stock:</strong> <?= htmlspecialchars($row['stock']) ?></p>
                        <p><strong>Seller:</strong> <?= htmlspecialchars($row['farmer_name']) ?></p>
                        <a href="add_to_cart.php?id=<?= urlencode($row['items_id']) ?>" class="btn">Add to Cart</a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>No products found.</p>
        <?php endif; ?>
    </div>
</main>

<footer>
    <p>&copy; 2025 AGRO-SMART. All rights reserved. |
        <a href="#">Privacy Policy</a> |
        <a href="#">Terms of Service</a>
    </p>
</footer>

</body>
</html>
