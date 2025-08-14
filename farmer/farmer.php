<?php
session_start();

// Redirect if not logged in as a farmer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'farmer') {
    header("Location: ../auth/login.php");
    exit();
}

require '../db_connect.php';
require '../categories.php'; // Include the centralized category list

$user_id = $_SESSION['user_id'];

// Fetch farmer profile
$sql_profile = "SELECT * FROM users WHERE users_id = ?";
$stmt_profile = $conn->prepare($sql_profile);
$stmt_profile->bind_param("i", $user_id);
$stmt_profile->execute();
$result_profile = $stmt_profile->get_result();
$farmer = $result_profile->fetch_assoc();

// Fetch farmer's products
$sql_products = "SELECT * FROM items WHERE farmer_id = ?";
$stmt_products = $conn->prepare($sql_products);
$stmt_products->bind_param("i", $user_id);
$stmt_products->execute();
$result_products = $stmt_products->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Dashboard - AGRO-SMART</title>
    <link rel="stylesheet" href="../styles.css"> <!-- Link to centralized CSS -->
    <style>
        /* General Styles */
        body {
            background-image: url('../images/irewolede-PvwdlXqo85k-unsplash.jpg'); /* Replace with your image path */
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
            background-color: #4CAF50;
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
        main.farmer-dashboard {
            flex: 1; /* Push the footer to the bottom */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding-top: 60px; /* Add padding to avoid overlap with the fixed header */
            padding-bottom: 60px; /* Add padding to avoid overlap with the fixed footer */
        }
        .dashboard-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.7); /* Dark translucent background */
            border-radius: 10px;
            text-align: center;
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
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"], textarea, select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .product-card {
            background-color: rgba(0, 0, 0, 0.7);
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .product-card img {
            max-width: 100%;
            height: auto;
        }
        .add-product-form {
            margin-top: 20px;
        }
        .your-products {
            margin-top: 20px;
        }
        .transaction-history {
            margin-top: 20px;
        }
        .profile-info ul {
            list-style-type: none;
            padding-left: 0;
        }
        .profile-info li::before {
            content: "•";
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <header class="topnav">
        <a href="../index.php">Home</a>
        <div class="topnav-right">
            <a href="../auth/logout.php">Logout</a>
        </div>
    </header>
    <main class="farmer-dashboard">
        <div class="dashboard-container">
            <h1>Welcome, <?php echo htmlspecialchars($farmer['firstname']); ?>!</h1>
            <p>This is your farmer dashboard. You can manage your products, view transactions, and update your profile here.</p>

            <!-- Section 1: Profile Information -->
            <section class="profile-info">
                <h2>Profile Information</h2>
                <ul>
                    <li><strong>Name:</strong> <?php echo htmlspecialchars($farmer['firstname'] . ' ' . $farmer['lastname']); ?></li>
                    <li><strong>Email:</strong> <?php echo htmlspecialchars($farmer['email']); ?></li>
                    <li><strong>Mobile:</strong> <?php echo htmlspecialchars($farmer['mobile']); ?></li>
                    <li><strong>Address:</strong> <?php echo htmlspecialchars($farmer['adr']); ?></li>
                    <li><strong>Country:</strong> <?php echo htmlspecialchars($farmer['country']); ?></li>
                    <li><strong>State:</strong> <?php echo htmlspecialchars($farmer['state']); ?></li>
                    <li><strong>District:</strong> <?php echo htmlspecialchars($farmer['district']); ?></li>
                    <li><strong>City:</strong> <?php echo htmlspecialchars($farmer['city']); ?></li>
                </ul>
            </section>

            <!-- Section 2: Manage Orders -->
            <section class="manage-orders">
                <h2>Manage Orders</h2>
                <p>View and update the status of orders placed for your products.</p>
                <a href="manage_orders.php" class="btn">Manage Orders</a>
            </section>

            <!-- Section 3: Add Product -->
            <section class="add-product">
                <h2>Add a New Product</h2>
                <form action="process_add_product.php" method="POST" enctype="multipart/form-data" class="add-product-form">
                    <div class="form-group">
                        <label for="name">Product Name:</label>
                        <input type="text" id="name" name="name" placeholder="Enter product name" required>
                    </div>
                    <div class="form-group">
                        <label for="brand">Brand:</label>
                        <input type="text" id="brand" name="brand" placeholder="Enter brand name" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea id="description" name="description" placeholder="Enter product description" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="types">Category:</label>
                        <select id="types" name="types" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category); ?>"><?php echo htmlspecialchars($category); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="price">Price (₦):</label>
                        <input type="number" id="price" name="price" placeholder="Enter price" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="stock">Stock Quantity:</label>
                        <input type="number" id="stock" name="stock" placeholder="Enter stock quantity" required>
                    </div>
                    <div class="form-group">
                        <label for="rol">Reorder Level:</label>
                        <input type="number" id="rol" name="rol" placeholder="Enter reorder level" required>
                    </div>
                    <div class="form-group">
                        <label for="img">Product Image:</label>
                        <input type="file" id="img" name="img" accept="image/*" required>
                    </div>
                    <button type="submit" class="btn">Add Product</button>
                </form>
            </section>

            <!-- Section 4: Your Products -->
            <section class="your-products">
                <h2>Your Products</h2>
                <?php
                if ($result_products->num_rows > 0) {
                    while ($row = $result_products->fetch_assoc()) {
                        echo '<div class="product-card">';
                        echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
                        echo "<p><strong>Brand:</strong> " . htmlspecialchars($row['brand']) . "</p>";
                        echo "<p><strong>Category:</strong> " . htmlspecialchars($row['types']) . "</p>";
                        echo "<p><strong>Price:</strong> ₦" . number_format($row['price'], 2) . "</p>";
                        echo "<p><strong>Stock:</strong> " . htmlspecialchars($row['stock']) . "</p>";
                        // Display the product image
                        if (!empty($row['img'])) {
                            echo '<img src="' . htmlspecialchars($row['img']) . '" alt="Product Image" style="max-width: 100%;">';
                        } else {
                            echo '<p>No image available.</p>';
                        }
                        echo "<p><a href='edit_product.php?id=" . htmlspecialchars($row['items_id']) . "'>Edit</a> | <a href='delete_product.php?id=" . htmlspecialchars($row['items_id']) . "' onclick='return confirm(\"Are you sure?\")'>Delete</a></p>";
                        echo '</div>';
                    }
                } else {
                    echo "<p>No products found. Add a product using the form above.</p>";
                }
                ?>
            </section>
        </div>
    </main>
    <footer>
        <p>© 2025 AGRO-SMART. All rights reserved.</p>
    </footer>
</body>
</html>