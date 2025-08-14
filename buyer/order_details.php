<?php
session_start();

// Ensure the user is logged in as a buyer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'buyer') {
    header("Location: ../auth/login.php");
    exit();
}

// Include the database connection
require '../db_connect.php';

// Get the order ID from the query string
if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    header("Location: order_history.php");
    exit();
}

$order_id = $_GET['order_id'];

// Fetch order details from the database
$sql_order = "SELECT o.order_id, o.total_amount, o.delivery_address, o.payment_method, o.order_status, o.timestamp 
              FROM orders o 
              WHERE o.order_id = ?";
$stmt_order = $conn->prepare($sql_order);
$stmt_order->bind_param("i", $order_id);
$stmt_order->execute();
$result_order = $stmt_order->get_result();

if ($result_order->num_rows === 0) {
    header("Location: order_history.php");
    exit();
}

$order = $result_order->fetch_assoc();

// Fetch order items for the order
$sql_order_items = "SELECT i.name, oi.quantity, oi.price 
                    FROM order_items oi 
                    JOIN items i ON oi.product_id = i.items_id 
                    WHERE oi.order_id = ?";
$stmt_order_items = $conn->prepare($sql_order_items);
$stmt_order_items->bind_param("i", $order_id);
$stmt_order_items->execute();
$result_order_items = $stmt_order_items->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - AGRO-SMART</title>
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
        main.order-details-page {
            flex: 1; /* Push the footer to the bottom */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding-top: 60px; /* Add padding to avoid overlap with the fixed header */
            padding-bottom: 60px; /* Add padding to avoid overlap with the fixed footer */
        }
        .order-details-box {
            background-color: rgba(0, 0, 0, 0.8); /* Dark translucent background */
            padding: 20px;
            border-radius: 10px;
            width: 100%;
            max-width: 800px;
            text-align: center;
        }
        .order-details-box h2 {
            margin-bottom: 20px;
        }
        .order-details-box table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto;
        }
        .order-details-box th, .order-details-box td {
            padding: 10px;
            border: 1px solid #4CAF50;
            text-align: left;
        }
        .order-details-box th {
            background-color: #4CAF50;
            color: white;
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

    <main class="order-details-page">
        <div class="order-details-box">
            <h2>Order Details</h2>

            <h3>Order Information</h3>
            <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order['order_id']); ?></p>
            <p><strong>Delivery Address:</strong> <?php echo htmlspecialchars($order['delivery_address']); ?></p>
            <p><strong>Payment Method:</strong> <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $order['payment_method']))); ?></p>
            <p><strong>Order Status:</strong> <?php echo htmlspecialchars(ucfirst($order['order_status'])); ?></p>
            <p><strong>Date:</strong> <?php echo htmlspecialchars(date('d M Y H:i', strtotime($order['timestamp']))); ?></p>

            <h3>Order Summary</h3>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_cost = 0;
                    while ($item = $result_order_items->fetch_assoc()): ?>
                        <?php
                        $price = $item['price'];
                        $quantity = $item['quantity'];
                        $subtotal = $price * $quantity;
                        $total_cost += $subtotal;
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><?php echo $quantity; ?></td>
                            <td>₦<?php echo number_format($price, 2); ?></td>
                            <td>₦<?php echo number_format($subtotal, 2); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <p class="total-cost">Total Cost: ₦<?php echo number_format($total_cost, 2); ?></p>

            <a href="order_history.php" class="btn">Back to Order History</a>
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