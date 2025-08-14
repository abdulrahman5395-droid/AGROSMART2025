<?php
session_start();

// Ensure the user is logged in as an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

require '../db_connect.php';

$error_message = '';

// Get the order ID from the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage_orders.php");
    exit();
}

$order_id = intval($_GET['id']);

// Fetch the order details
$sql_order = "SELECT o.order_id, u.firstname, u.lastname, o.total_amount, o.status, o.created_at 
              FROM orders o 
              JOIN users u ON o.buyer_id = u.users_id 
              WHERE o.order_id = ?";
$stmt_order = $conn->prepare($sql_order);
$stmt_order->bind_param("i", $order_id);
$stmt_order->execute();
$result_order = $stmt_order->get_result();

if ($result_order->num_rows === 0) {
    header("Location: manage_orders.php");
    exit();
}

$order = $result_order->fetch_assoc();

// Fetch the order items
$sql_order_items = "SELECT i.name AS item_name, oi.quantity, oi.price 
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
    <title>View Order Details - AGRO-SMART</title>
    <link rel="stylesheet" href="../styles.css"> <!-- Link to centralized CSS -->
    <style>
        body {
            background-color: #f4f4f9;
            color: #333;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
        }
        .admin-container {
            display: flex;
            flex-direction: row;
            width: 100%;
        }
        .sidebar {
            width: 250px;
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            height: 100vh;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }
        .sidebar h2 {
            margin-top: 0;
            font-size: 1.5em;
        }
        .sidebar ul {
            list-style-type: none;
            padding: 0;
        }
        .sidebar ul li {
            margin-bottom: 10px;
        }
        .sidebar ul li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }
        .sidebar ul li a:hover {
            text-decoration: underline;
        }
        .main-content {
            flex: 1;
            padding: 20px;
        }
        .order-details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .order-details-table th, .order-details-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .order-details-table th {
            background-color: #4CAF50;
            color: white;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn:hover {
            background-color: #45a049;
        }
        .message {
            margin-bottom: 20px;
        }
        .success-message {
            color: #4CAF50;
        }
        .error-message {
            color: #f44336;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2>AGRO-SMART Admin</h2>
            <ul>
                <li><a href="admin.php">Dashboard</a></li>
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="manage_products.php">Manage Products</a></li>
                <li><a href="manage_orders.php">Manage Orders</a></li>
            </ul>
            <div class="logout">
                <a href="../auth/logout.php">Logout</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <h1>Order Details</h1>

            <?php if (!empty($error_message)): ?>
                <p class="message error-message"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>

            <div>
                <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order['order_id']); ?></p>
                <p><strong>Customer Name:</strong> <?php echo htmlspecialchars($order['firstname'] . ' ' . $order['lastname']); ?></p>
                <p><strong>Total Amount:</strong> ₦<?php echo htmlspecialchars(number_format($order['total_amount'], 2)); ?></p>
                <p><strong>Status:</strong> <?php echo htmlspecialchars(ucfirst($order['status'])); ?></p>
                <p><strong>Date:</strong> <?php echo htmlspecialchars($order['created_at']); ?></p>
            </div>

            <h2>Order Items</h2>

            <table class="order-details-table">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_order_items->num_rows > 0): ?>
                        <?php while ($row = $result_order_items->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                                <td>₦<?php echo htmlspecialchars(number_format($row['price'], 2)); ?></td>
                                <td>₦<?php echo htmlspecialchars(number_format($row['quantity'] * $row['price'], 2)); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No items found for this order.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <a href="manage_orders.php" class="btn">Back to Orders</a>
        </div>
    </div>
</body>
</html>