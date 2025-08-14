<?php
session_start();

// Include the database connection
require 'db_connect.php'; // Adjust the path if needed

// Get the product ID from the query string
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: marketplace.php");
    exit();
}

$product_id = $_GET['id'];

// Fetch product details from the database
$sql_product = "SELECT items.*, users.firstname, users.lastname 
                FROM items 
                JOIN users ON items.farmer_id = users.users_id 
                WHERE items.items_id = ?";
$stmt_product = $conn->prepare($sql_product);
$stmt_product->bind_param("i", $product_id);
$stmt_product->execute();
$result_product = $stmt_product->get_result();

if ($result_product->num_rows === 0) {
    header("Location: marketplace.php");
    exit();
}

$product = $result_product->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details - AGRO-SMART</title>
    <link rel="stylesheet" href="../styles.css"> <!-- Link to centralized CSS -->
    <style>
        /* General Styles */
        body {
            background-image: url('images/annie-spratt-kr_88BakygA-unsplash.jpg'); /* Replace with your image path */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
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
        main.product-details {
            flex: 1; /* Push the footer to the bottom */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding-top: 60px; /* Add padding to avoid overlap with the fixed header */
            padding-bottom: 60px; /* Add padding to avoid overlap with the fixed footer */
        }
        .product-card {
            background-color: rgba(0, 0, 0, 0.8); /* Dark translucent background */
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            max-width: 600px;
            width: 100%;
        }
        .product-card img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
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
        <a href="../index.php">Home</a>
        <a href="marketplace.php">Marketplace</a>
        <div class="topnav-right">
            <a href="../auth/signup.php">Sign Up</a>
            <a href="../admin/admin.php">Admin</a>
            <a href="../auth/login.php">Login</a>
        </div>
    </header>

    <main class="product-details">
        <div class="product-card">
            <img src="<?php echo htmlspecialchars($product['img']); ?>" alt="Product Image">
            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
            <p><strong>Price:</strong> ₦<?php echo number_format($product['price'], 2); ?></p>
            <p><strong>Farmer:</strong> <?php echo htmlspecialchars($product['firstname'] . ' ' . $product['lastname']); ?></p>
            <p><strong>Stock:</strong> <?php echo htmlspecialchars($product['stock']); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($product['description']); ?></p>
            <p><strong>Brand:</strong> <?php echo htmlspecialchars($product['brand']); ?></p>
            <p><strong>Type:</strong> <?php echo htmlspecialchars($product['types']); ?></p>
            <a href="marketplace.php" class="btn">Back to Marketplace</a>
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