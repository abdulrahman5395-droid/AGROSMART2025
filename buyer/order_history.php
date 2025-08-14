<?php
session_start();

// Ensure the user is logged in as a buyer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'buyer') {
    header("Location: ../auth/login.php");
    exit();
}

// Include the database connection
require '../db_connect.php';

// Fetch all orders for the buyer
$buyer_id = $_SESSION['user_id'];
$sql_orders = "SELECT o.order_id, o.total_amount, o.delivery_address, o.payment_method, o.order_status, o.timestamp 
               FROM orders o 
               WHERE o.buyer_id = ? 
               ORDER BY o.timestamp DESC";
$stmt_orders = $conn->prepare($sql_orders);
$stmt_orders->bind_param("i", $buyer_id);
$stmt_orders->execute();
$result_orders = $stmt_orders->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History - AGRO-SMART</title>
    <link rel="stylesheet" href="../styles.css"> <!-- Link to centralized CSS -->
    <style>
        /* General Styles */
        body {
            background-image: url('../images/lumin-osity-6DMht7wYt6g-unsplash.jpg'); /* Replace with your image path */
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
        main.order-history-page {
            flex: 1; /* Push the footer to the bottom */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding-top: 60px; /* Add padding to avoid overlap with the fixed header */
            padding-bottom: 60px; /* Add padding to avoid overlap with the fixed footer */
        }
        .order-history-box {
            background-color: rgba(0, 0, 0, 0.8); /* Dark translucent background */
            padding: 20px;
            border-radius: 10px;
            width: 100%;
            max-width: 800px;
            text-align: center;
        }
        .order-history-box h2 {
            margin-bottom: 20px;
        }
        .order-history-box table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto;
        }
        .order-history-box th, .order-history-box td {
            padding: 10px;
            border: 1px solid #4CAF50;
            text-align: left;
        }
        .order-history-box th {
            background-color: #4CAF50;
            color: white;
        }
        .order-history-box a {
            color: #4CAF50;
            text-decoration: none;
        }
        .order-history-box a:hover {
            text-decoration: underline;
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

    <main class="order-history-page">
        <div class="order-history-box">
            <h2>Order History</h2>

            <?php if ($result_orders->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result_orders->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                                <td>₦<?php echo number_format($row['total_amount'], 2); ?></td>
                                <td><?php echo htmlspecialchars(ucfirst($row['order_status'])); ?></td>
                                <td><?php echo htmlspecialchars(date('d M Y H:i', strtotime($row['timestamp']))); ?></td>
                                <td>
                                    <a href="order_details.php?order_id=<?php echo htmlspecialchars($row['order_id']); ?>">View Details</a>
                                </td>
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