<?php
session_start();

// Ensure the user is logged in as a buyer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'buyer') {
    header("Location: ../auth/login.php");
    exit();
}

// Include the database connection
require '../db_connect.php';

// Check if the cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

$error_message = '';
$success_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $delivery_address = trim($_POST['delivery_address']);
    $payment_method = trim($_POST['payment_method']);

    // Validate form data
    if (empty($delivery_address) || empty($payment_method)) {
        $error_message = "All fields are required.";
    } else {
        // Calculate total amount
        $total_amount = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total_amount += $item['price'] * $item['quantity'];
        }

        // Insert the order into the database
        $buyer_id = intval($_SESSION['user_id']); // Ensure user_id is an integer

        // Start a transaction to ensure atomicity
        $conn->begin_transaction();

        try {
            // Fetch farmer_id for each product in the cart
            $farmer_ids = [];
            foreach ($_SESSION['cart'] as $product_id => $item) {
                $sql_farmer = "SELECT farmer_id FROM items WHERE items_id = ?";
                $stmt_farmer = $conn->prepare($sql_farmer);
                $stmt_farmer->bind_param("i", $product_id);
                $stmt_farmer->execute();
                $result_farmer = $stmt_farmer->get_result();

                if ($result_farmer->num_rows === 0) {
                    throw new Exception("Product ID $product_id not found.");
                }

                $row_farmer = $result_farmer->fetch_assoc();
                $farmer_ids[$product_id] = $row_farmer['farmer_id'];
            }

            // Insert the main order record
            $sql_order = "INSERT INTO orders (buyer_id, total_amount, delivery_address, payment_method, order_status, farmer_id)
                          VALUES (?, ?, ?, ?, 'pending', ?)";
            $stmt_order = $conn->prepare($sql_order);

            if (!$stmt_order) {
                throw new Exception("Prepare failed: " . $conn->error);
            }

            // Use the first farmer_id for the main order (assuming all products belong to the same farmer)
            $first_farmer_id = reset($farmer_ids); // Get the first farmer_id
            $stmt_order->bind_param("idssi", $buyer_id, $total_amount, $delivery_address, $payment_method, $first_farmer_id);

            if (!$stmt_order->execute()) {
                throw new Exception("Execute failed: " . $stmt_order->error);
            }

            $order_id = $stmt_order->insert_id;

            // Insert order items into the database
            $sql_order_items = "INSERT INTO order_items (order_id, product_id, quantity, price, farmer_id) VALUES (?, ?, ?, ?, ?)";
            $stmt_order_items = $conn->prepare($sql_order_items);

            if (!$stmt_order_items) {
                throw new Exception("Prepare failed: " . $conn->error);
            }

            foreach ($_SESSION['cart'] as $product_id => $item) {
                $quantity = intval($item['quantity']);
                $price = floatval($item['price']);
                $farmer_id = $farmer_ids[$product_id]; // Get the farmer_id for the product
                $stmt_order_items->bind_param("iiidi", $order_id, $product_id, $quantity, $price, $farmer_id);

                if (!$stmt_order_items->execute()) {
                    throw new Exception("Execute failed: " . $stmt_order_items->error);
                }
            }

            // Commit the transaction
            $conn->commit();

            // Clear the cart
            unset($_SESSION['cart']);

            // Redirect to order confirmation page
            header("Location: order_confirmation.php?order_id=$order_id");
            exit();
        } catch (Exception $e) {
            // Rollback the transaction in case of an error
            $conn->rollback();
            $error_message = "Error placing order: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - AGRO-SMART</title>
    <link rel="stylesheet" href="../styles.css"> <!-- Link to centralized CSS -->
    <style>
        /* General Styles */
        body {
            background-image: url('../images/erwan-hesry-1q75BReKpms-unsplash.jpg'); /* Replace with your image path */
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
        main.checkout-page {
            flex: 1; /* Push the footer to the bottom */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding-top: 60px; /* Add padding to avoid overlap with the fixed header */
            padding-bottom: 60px; /* Add padding to avoid overlap with the fixed footer */
        }
        .checkout-form {
            background-color: rgba(0, 0, 0, 0.8); /* Dark translucent background */
            padding: 20px;
            border-radius: 10px;
            width: 100%;
            max-width: 600px;
            text-align: center;
        }
        .checkout-form h2 {
            margin-bottom: 20px;
        }
        .checkout-form label {
            display: block;
            margin: 10px 0 5px;
            text-align: left;
            font-weight: bold;
        }
        .checkout-form input, .checkout-form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #4CAF50;
            border-radius: 5px;
            background-color: rgba(255, 255, 255, 0.8);
            color: black;
        }
        .checkout-form button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .checkout-form button:hover {
            background_color: #45a049;
        }
        .error-message {
            color: #f44336;
            margin-bottom: 20px;
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

    <main class="checkout-page">
        <h2>Checkout</h2>
        <p>Please fill in the details below to place your order.</p>

        <?php if (isset($error_message)): ?>
            <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>

        <form class="checkout-form" method="POST" action="">
            <label for="delivery_address">Delivery Address:</label>
            <input type="text" id="delivery_address" name="delivery_address" required>

            <label for="payment_method">Payment Method:</label>
            <select id="payment_method" name="payment_method" required>
                <option value="cash_on_delivery">Cash on Delivery</option>
                <option value="bank_transfer">Bank Transfer</option>
            </select>

            <h3>Order Summary</h3>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                <thead>
                    <tr>
                        <th style="border: 1px solid #4CAF50; padding: 10px;">Product</th>
                        <th style="border: 1px solid #4CAF50; padding: 10px;">Quantity</th>
                        <th style="border: 1px solid #4CAF50; padding: 10px;">Price</th>
                        <th style="border: 1px solid #4CAF50; padding: 10px;">Subtotal</th>
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
                            <td style="border: 1px solid #4CAF50; padding: 10px;"><?php echo htmlspecialchars($item['name']); ?></td>
                            <td style="border: 1px solid #4CAF50; padding: 10px;"><?php echo $quantity; ?></td>
                            <td style="border: 1px solid #4CAF50; padding: 10px;">₦<?php echo number_format($price, 2); ?></td>
                            <td style="border: 1px solid #4CAF50; padding: 10px;">₦<?php echo number_format($subtotal, 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <p>Total Cost: ₦<?php echo number_format($total_cost, 2); ?></p>

            <button type="submit">Place Order</button>
        </form>
    </main>

    <footer>
        <p>&copy; 2025 AGRO-SMART. All rights reserved. | 
            <a href="#">Privacy Policy</a> | 
            <a href="#">Terms of Service</a>
        </p>
    </footer>
</body>
</html>