<?php
session_start();

// Include the database connection
require 'db_connect.php';

// Get the selected category from the query parameter
$selected_category = isset($_GET['choice']) ? trim($_GET['choice']) : '';

// Fetch products and farmer details based on the selected category
$sql_products = "SELECT items.*, users.firstname, users.lastname 
                 FROM items 
                 JOIN users ON items.farmer_id = users.users_id 
                 WHERE items.types = ?";
$stmt_products = $conn->prepare($sql_products);
$stmt_products->bind_param("s", $selected_category);
$stmt_products->execute();
$result_products = $stmt_products->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Products - AGRO-SMART</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to centralized CSS -->
    <style>
        /* General Styles */
        body {
            background-image: url('images/erwan-hesry-1q75BReKpms-unsplash.jpg'); /* Replace with the path to your image */
            background-size: cover;
            background-position: center;
            color: white;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh; /* Ensure the body takes up the full viewport height */
        }
        header {
            background-color: rgba(0, 0, 0, 0.7);
            padding: 10px;
        }
        .topnav a {
            color: white;
            text-decoration: none;
            margin-right: 15px;
        }
        .dashboard-container {
            flex: 1; /* Push the footer to the bottom */
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.7); /* Semi-transparent overlay for better readability */
            border-radius: 10px;
        }
        h1, h2 {
            color: #fff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid white;
            text-align: left;
            padding: 8px;
        }
        th {
            background-color: #4CAF50;
        }
        footer {
            background-color: rgba(0, 0, 0, 0.7);
            text-align: center;
            padding: 10px;
            color: white;
        }
    </style>
</head>
<body>
    <header class="topnav">
        <a href="index.php">Home</a>
        <div class="topnav-right">
            <a href="auth/login.php">Login</a>
        </div>
    </header>

    <main class="dashboard-container">
        <h1>Products in Category: <?php echo htmlspecialchars($selected_category); ?></h1>

        <?php if ($result_products->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Product Name</th>
                    <th>Brand</th>
                    <th>Price (₦)</th>
                    <th>Stock</th>
                    <th>Farmer</th>
                    <th>Image</th>
                </tr>
                <?php while ($row = $result_products->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['brand']); ?></td>
                        <td>₦<?php echo number_format($row['price'], 2); ?></td>
                        <td><?php echo htmlspecialchars($row['stock']); ?></td>
                        <td><?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?></td>
                        <td><img src="<?php echo htmlspecialchars($row['img']); ?>" alt="Product Image" width="50"></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No products found in this category.</p>
        <?php endif; ?>
    </main>

    <footer>
        <p>© 2025 AGRO-SMART. All rights reserved.</p>
    </footer>
</body>
</html>