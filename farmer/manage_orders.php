<?php
session_start();

// Ensure the user is logged in as a farmer/vendor
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'farmer') {
    header("Location: ../auth/login.php");
    exit();
}

// Include the database connection
require '../db_connect.php'; // Corrected path

// Fetch all orders for the farmer/vendor
$farmer_id = $_SESSION['user_id'];
$sql_orders = "SELECT o.order_id, o.total_amount, o.delivery_address, o.payment_method, o.order_status, o.timestamp 
               FROM orders o 
               JOIN order_items oi ON o.order_id = oi.order_id 
               JOIN items i ON oi.product_id = i.items_id 
               WHERE i.farmer_id = ? 
               ORDER BY o.timestamp DESC";
$stmt_orders = $conn->prepare($sql_orders);
$stmt_orders->bind_param("i", $farmer_id);
$stmt_orders->execute();
$result_orders = $stmt_orders->get_result();

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];

    // Update the order status in the database
    $sql_update = "UPDATE orders SET order_status = ? WHERE order_id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("si", $new_status, $order_id);

    if ($stmt_update->execute()) {
        // Reset the notified flag
        $sql_notify = "UPDATE orders SET notified = 0 WHERE order_id = ?";
        $stmt_notify = $conn->prepare($sql_notify);
        $stmt_notify->bind_param("i", $order_id);
        $stmt_notify->execute();

        // Redirect back to manage_orders.php after update
        header("Location: manage_orders.php");
        exit();
    } else {
        $error_message = "Error updating order status: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - AGRO-SMART</title>
    <link rel="stylesheet" href="../../styles.css"> <!-- Link to centralized CSS -->
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
        main.manage-orders-page {
            flex: 1; /* Push the footer to the bottom */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding-top: 60px; /* Add padding to avoid overlap with the fixed header */
            padding-bottom: 60px; /* Add padding to avoid overlap with the fixed footer */
        }
        .orders-box {
            background-color: rgba(0, 0, 0, 0.8); /* Dark translucent background */
            padding: 20px;
            border-radius: 10px;
            width: 100%;
            max-width: 800px;
            text-align: center;
        }
        .orders-box h2 {
            margin-bottom: 20px;
        }
        .orders-box table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto;
        }
        .orders-box th, .orders-box td {
            padding: 10px;
            border: 1px solid #4CAF50;
            text-align: left;
        }
        .orders-box th {
            background-color: #4CAF50;
            color: white;
        }
        .orders-box select {
            padding: 5px;
            border-radius: 5px;
        }
        .orders-box button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .orders-box button:hover {
            background-color: #45a049;
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
        <a href="farmer.php">Farmer Dashboard</a>
        <div class="topnav-right">
            <a href="../auth/logout.php">Logout</a>
        </div>
    </header>

    <main class="manage-orders-page">
        <div class="orders-box">
            <h2>Manage Orders</h2>

            <?php if (isset($error_message)): ?>
                <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>

            <?php if ($result_orders->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result_orders->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                                <td>₦<?php echo number_format($row['total_amount'], 2); ?></td>
                                <td>
                                    <form method="POST" action="">
                                        <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($row['order_id']); ?>">
                                        <select name="status">
                                            <option value="pending" <?php echo ($row['order_status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                                            <option value="shipped" <?php echo ($row['order_status'] === 'shipped') ? 'selected' : ''; ?>>Shipped</option>
                                            <option value="delivered" <?php echo ($row['order_status'] === 'delivered') ? 'selected' : ''; ?>>Delivered</option>
                                        </select>
                                        <button type="submit" name="update_status">Update</button>
                                    </form>
                                </td>
                                <td><?php echo htmlspecialchars(date('d M Y H:i', strtotime($row['timestamp']))); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No orders found.</p>
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