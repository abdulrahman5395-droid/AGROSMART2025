<?php
session_start();

// Include the database connection
require 'db_connect.php';

// Fetch all products from the database
$sql_products = "SELECT items.*, users.firstname, users.lastname 
                 FROM items 
                 JOIN users ON items.farmer_id = users.users_id 
                 ORDER BY items.name ASC";
$stmt_products = $conn->prepare($sql_products);
$stmt_products->execute();
$result_products = $stmt_products->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Items - AGRO-SMART</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to centralized CSS -->
    <style>
        /* General Styles */
        body {
            background-image: url('images/pexels-timmossholder-974314.jpg'); /* Replace with the path to your image */
            background-size: cover;
            background-position: center;
            color: white;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        h1, h2 {
            color: #fff;
        }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .product-card {
            background-color: rgba(0, 0, 0, 0.7);
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
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
            font-size: 0.9em;
        }
        footer {
            background-color: rgba(0, 0, 0, 0.8); /* Slightly lighter background */
            text-align: center;
            padding: 15px;
            color: #ffffff; /* Light text for better contrast */
            font-size: 0.9em;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
        footer a {
            color: #4CAF50; /* Green links for better visibility */
            text-decoration: none;
        }
        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header class="topnav">
        <a href="index.php">Home</a>
        <a href="item.php">All Items</a>
        <div class="topnav-right">
            <a href="auth/signup.php">Sign Up</a>
            <a href="admin/admin.php">Admin</a>
            <a href="auth/login.php">Login</a>
        </div>
    </header>

    <main class="dashboard-container">
        <h1>All Items</h1>
        <p>Browse through all the products available on AGRO-SMART.</p>

        <?php if ($result_products->num_rows > 0): ?>
            <div class="product-grid">
                <?php while ($row = $result_products->fetch_assoc()): ?>
                    <div class="product-card">
                        <img src="<?php echo htmlspecialchars($row['img']); ?>" alt="Product Image">
                        <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                        <p><strong>Brand:</strong> <?php echo htmlspecialchars($row['brand']); ?></p>
                        <p><strong>Price:</strong> ₦<?php echo number_format($row['price'], 2); ?></p>
                        <p><strong>Farmer:</strong> <?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?></p>
                        <p><strong>Stock:</strong> <?php echo htmlspecialchars($row['stock']); ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>No products found. Check back later!</p>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2025 AGRO-SMART. All rights reserved. | <a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a></p>
    </footer>
</body>
</html>