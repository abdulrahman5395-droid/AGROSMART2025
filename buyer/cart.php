<?php
session_start();

// Ensure the user is logged in as a buyer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'buyer') {
    header("Location: ../auth/login.php");
    exit();
}

// Include the database connection
require '../db_connect.php';

// Handle cart updates (e.g., updating quantities or removing items)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_cart'])) {
        foreach ($_POST['quantities'] as $product_id => $quantity) {
            if ($quantity > 0) {
                $_SESSION['cart'][$product_id]['quantity'] = $quantity;
            } else {
                unset($_SESSION['cart'][$product_id]);
            }
        }
    }

    if (isset($_POST['remove_item'])) {
        $product_id = $_POST['remove_item'];
        unset($_SESSION['cart'][$product_id]);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - AGRO-SMART</title>
    <link rel="stylesheet" href="../styles.css"> <!-- Link to centralized CSS -->
    <style>
        /* General Styles */
        body {
            background-image: url('../images/mohamed-musthafa-musthaq-ahamed-3rQuoI_VWy8-unsplash.jpg'); /* Replace with your image path */
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
        main.cart-page {
            flex: 1; /* Push the footer to the bottom */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding-top: 60px; /* Add padding to avoid overlap with the fixed header */
            padding-bottom: 60px; /* Add padding to avoid overlap with the fixed footer */
        }
        .cart-table {
            background-color: rgba(0, 0, 0, 0.8); /* Dark translucent background */
            padding: 20px;
            border-radius: 10px;
            width: 100%;
            max-width: 800px;
            text-align: center;
        }
        .cart-table table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto;
        }
        .cart-table th, .cart-table td {
            padding: 10px;
            border: 1px solid #4CAF50;
            text-align: left;
        }
        .cart-table th {
            background-color: #4CAF50;
            color: white;
        }
        .cart-table input[type="number"] {
            width: 60px;
            padding: 5px;
        }
        .cart-table button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .cart-table button:hover {
            background-color: #45a049;
        }
        .total-cost {
            margin-top: 20px;
            font-size: 1.2em;
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
        <a href="buyer.php">Buyer Dashboard</a>
        <div class="topnav-right">
            <a href="../auth/logout.php">Logout</a>
        </div>
    </header>

    <main class="cart-page">
        <h2>Your Cart</h2>

        <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
            <form class="cart-table" method="POST" action="">
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total_cost = 0;
                        foreach ($_SESSION['cart'] as $product_id => $item): ?>
                            <?php
                            $price = $item['price'];
                            $quantity = $item['quantity'];
                            $subtotal = $price * $quantity;
                            $total_cost += $subtotal;
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td>₦<?php echo number_format($price, 2); ?></td>
                                <td>
                                    <input type="number" name="quantities[<?php echo $product_id; ?>]" value="<?php echo $quantity; ?>" min="1">
                                </td>
                                <td>₦<?php echo number_format($subtotal, 2); ?></td>
                                <td>
                                    <button type="submit" name="remove_item" value="<?php echo $product_id; ?>">Remove</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="total-cost">
                    Total Cost: ₦<?php echo number_format($total_cost, 2); ?>
                </div>

                <div style="margin-top: 20px;">
                    <button type="submit" name="update_cart">Update Cart</button>
                    <a href="checkout.php" class="btn">Proceed to Checkout</a>
                </div>
            </form>
        <?php else: ?>
            <p>Your cart is empty.</p>
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