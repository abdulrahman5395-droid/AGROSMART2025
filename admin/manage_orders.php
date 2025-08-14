<?php
session_start();

// Ensure the user is logged in as an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Include the database connection
require '../db_connect.php';

$error_message = '';
$success_message = '';

// Fetch all orders from the database
$sql_orders = "SELECT o.order_id, u.firstname, u.lastname, o.total_amount, o.status, o.created_at 
               FROM orders o 
               JOIN users u ON o.buyer_id = u.users_id 
               ORDER BY o.created_at DESC";
$stmt_orders = $conn->prepare($sql_orders);

if ($stmt_orders) {
    $stmt_orders->execute();
    $result_orders = $stmt_orders->get_result();
} else {
    $error_message = "Error preparing statement: " . $conn->error;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - AGRO-SMART</title>
    <link rel="stylesheet" href="../styles.css"> <!-- Link to centralized CSS -->
    <style>
        /* General Styles */
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
        .order-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .order-table th, .order-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .order-table th {
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
            <h1>Manage Orders</h1>

            <?php if (isset($error_message)): ?>
                <p class="message error-message"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>

            <?php if (isset($success_message)): ?>
                <p class="message success-message"><?php echo htmlspecialchars($success_message); ?></p>
            <?php endif; ?>

            <table class="order-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer Name</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_orders->num_rows > 0): ?>
                        <?php while ($row = $result_orders->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?></td>
                                <td>₦<?php echo htmlspecialchars(number_format($row['total_amount'], 2)); ?></td>
                                <td><?php echo htmlspecialchars(ucfirst($row['status'])); ?></td>
                                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                <td class="actions">
                                    <a href="view_order_details.php?id=<?php echo htmlspecialchars($row['order_id']); ?>">View Details</a>
                                    <a href="edit_order.php?id=<?php echo htmlspecialchars($row['order_id']); ?>">Edit</a>
                                    <a href="delete_order.php?id=<?php echo htmlspecialchars($row['order_id']); ?>" class="delete" onclick="return confirm('Are you sure you want to delete this order?');">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">No orders found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>