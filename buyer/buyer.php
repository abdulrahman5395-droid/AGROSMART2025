<?php
session_start();

// Ensure the user is logged in as a buyer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'buyer') {
    header("Location: ../auth/login.php");
    exit();
}

// Include the database connection
require '../db_connect.php';

// Fetch recent orders for the buyer
$buyer_id = $_SESSION['user_id'];
$sql_orders = "SELECT o.order_id, o.total_amount, o.delivery_address, o.payment_method, o.order_status, o.timestamp 
               FROM orders o 
               WHERE o.buyer_id = ? 
               ORDER BY o.timestamp DESC LIMIT 5";
$stmt_orders = $conn->prepare($sql_orders);
$stmt_orders->bind_param("i", $buyer_id);
$stmt_orders->execute();
$result_orders = $stmt_orders->get_result();

// Fetch new notifications for the buyer
$sql_notifications = "SELECT order_id, order_status FROM orders WHERE buyer_id = ? AND notified = 0";
$stmt_notifications = $conn->prepare($sql_notifications);
$stmt_notifications->bind_param("i", $buyer_id);
$stmt_notifications->execute();
$result_notifications = $stmt_notifications->get_result();

if ($result_notifications->num_rows > 0) {
    // Mark notifications as read
    $sql_mark_read = "UPDATE orders SET notified = 1 WHERE buyer_id = ? AND notified = 0";
    $stmt_mark_read = $conn->prepare($sql_mark_read);
    $stmt_mark_read->bind_param("i", $buyer_id);
    $stmt_mark_read->execute();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buyer Dashboard - AGRO-SMART</title>
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
        main.buyer-dashboard {
            flex: 1; /* Push the footer to the bottom */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding-top: 60px; /* Add padding to avoid overlap with the fixed header */
            padding-bottom: 60px; /* Add padding to avoid overlap with the fixed footer */
        }
        .dashboard-overview {
            background-color: rgba(0, 0, 0, 0.8); /* Dark translucent background */
            padding: 20px;
            border-radius: 10px;
            width: 100%;
            max-width: 800px;
            text-align: center;
        }
        .dashboard-overview h2 {
            margin-bottom: 20px;
        }
        .recent-orders table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto;
        }
        .recent-orders th, .recent-orders td {
            padding: 10px;
            border: 1px solid #4CAF50;
            text-align: left;
        }
        .recent-orders th {
            background-color: #4CAF50;
            color: white;
        }
        .notifications {
            background-color: rgba(0, 0, 0, 0.8);
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .notifications ul {
            list-style: none;
            padding: 0;
        }
        .notifications li {
            margin: 5px 0;
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
        <a href="cart.php">Cart</a>
        <a href="order_history.php">Order History</a>
        <a href="buyer_profile.php">Profile</a>
        <div class="topnav-right">
            <a href="../auth/logout.php">Logout</a>
        </div>
    </header>

    <main class="buyer-dashboard">
        <h2>Buyer Dashboard</h2>
        <p>Welcome back, <?php echo htmlspecialchars($_SESSION['firstname'] ?? ''); ?>!</p>

        <?php if ($result_notifications->num_rows > 0): ?>
            <div class="notifications">
                <h3>New Notifications</h3>
                <ul>
                    <?php while ($notification = $result_notifications->fetch_assoc()): ?>
                        <li>
                            Order #<?php echo htmlspecialchars($notification['order_id']); ?> 
                            has been updated to "<?php echo htmlspecialchars(ucfirst($notification['order_status'])); ?>".
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="dashboard-overview">
            <h3>Recent Orders</h3>
            <?php if ($result_orders->num_rows > 0): ?>
                <div class="recent-orders">
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result_orders->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                                    <td>₦<?php echo number_format($row['total_amount'], 2); ?></td>
                                    <td><?php echo htmlspecialchars(ucfirst($row['order_status'])); ?></td>
                                    <td><?php echo htmlspecialchars(date('d M Y', strtotime($row['timestamp']))); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>No recent orders found.</p>
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