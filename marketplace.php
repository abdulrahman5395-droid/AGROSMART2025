<?php
session_start();

// Include the database connection
require 'db_connect.php';

// Fetch all products from the database
$sql_products = "SELECT items.items_id, items.name, items.price, items.stock, items.img, users.firstname, users.lastname 
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
    <title>Marketplace - AGRO-SMART</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to centralized CSS -->
    <style>
        /* General Styles */
        body {
            background-image: url('images/thomas-le-pRJhn4MbsMM-unsplash.jpg'); /* Replace with your image path */
            background-size: cover;
            background-position: center;
            color: white;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        header.topnav {
            background-color: rgba(0, 0, 0, 0.7);
            padding: 10px;
            width: 100%;
            text-align: center;
            position: fixed; /* Fix the header at the top */
            top: 0;
            left: 0;
            z-index: 1000;
        }
        .topnav a {
            color: white;
            text-decoration: none;
            margin-right: 15px;
        }
        main.marketplace {
            flex: 1; /* Push the footer to the bottom */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding-top: 60px; /* Add padding to avoid overlap with the fixed header */
            padding-bottom: 60px; /* Add padding to avoid overlap with the fixed footer */
        }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
            max-width: 1200px;
            width: 100%;
        }
        .product-card {
            background-color: rgba(0, 0, 0, 0.8); /* Dark translucent background */
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
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #45a049;
        }
        footer {
            background-color: rgba(0, 0, 0, 0.8);
            text-align: center;
            padding: 15px;
            color: #ffffff;
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
        <a href="index.php">Home</a>
        <a href="item.php">All Items</a>
        <div class="topnav-right">
            <a href="auth/signup.php">Sign Up</a>
            <a href="admin/admin.php">Admin</a>
            <a href="auth/login.php">Login</a>
        </div>
    </header>

    <main class="marketplace">
        <h1>AGRO-SMART Marketplace</h1>
        <p>Explore a wide range of agricultural products.</p>

        <!-- Search Bar -->
        <form action="marketplace.php" method="GET" style="margin-bottom: 20px;">
            <input type="text" name="search" placeholder="Search for products..." required>
            <button type="submit" class="btn">Search</button>
        </form>

        <?php if ($result_products->num_rows > 0): ?>
            <div class="product-grid">
                <?php while ($row = $result_products->fetch_assoc()): ?>
                    <!-- Debugging Output -->
                    <?php // print_r($row); ?>

                    <div class="product-card">
                        <img src="<?php echo htmlspecialchars($row['img']); ?>" alt="Product Image">
                        <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                        <p><strong>Price:</strong> ₦<?php echo number_format($row['price'], 2); ?></p>
                        <p><strong>Farmer:</strong> <?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?></p>
                        <p><strong>Stock:</strong> <?php echo htmlspecialchars($row['stock']); ?></p>
                        <a href="view_product.php?id=<?php echo htmlspecialchars($row['items_id'] ?? ''); ?>" class="btn">View Details</a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>No products found. Check back later!</p>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2025 AGRO-SMART. All rights reserved. | 
            <a href="#">Privacy Policy</a> | 
            <a href="#">Terms of Service</a>
        </p>
    </footer>
</body>
</html>