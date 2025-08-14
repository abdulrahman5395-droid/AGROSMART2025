<?php
session_start();

// Redirect if not logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

require '../db_connect.php';

// Fetch total users
$sql_total_users = "SELECT COUNT(*) AS total_users FROM users";
$stmt_total_users = $conn->prepare($sql_total_users);
$stmt_total_users->execute();
$result_total_users = $stmt_total_users->get_result();
$total_users = $result_total_users->fetch_assoc()['total_users'];

// Fetch total products
$sql_total_products = "SELECT COUNT(*) AS total_products FROM items";
$stmt_total_products = $conn->prepare($sql_total_products);
$stmt_total_products->execute();
$result_total_products = $stmt_total_products->get_result();
$total_products = $result_total_products->fetch_assoc()['total_products'];

// Fetch total orders
$sql_total_orders = "SELECT COUNT(*) AS total_orders FROM orders";
$stmt_total_orders = $conn->prepare($sql_total_orders);
$stmt_total_orders->execute();
$result_total_orders = $stmt_total_orders->get_result();
$total_orders = $result_total_orders->fetch_assoc()['total_orders'];

// Fetch total revenue
$sql_total_revenue = "SELECT SUM(total_amount) AS total_revenue FROM orders WHERE status = 'Completed'";
$stmt_total_revenue = $conn->prepare($sql_total_revenue);
$stmt_total_revenue->execute();
$result_total_revenue = $stmt_total_revenue->get_result();
$total_revenue = $result_total_revenue->fetch_assoc()['total_revenue'] ?? 0;

// Format total revenue
$total_revenue_formatted = number_format($total_revenue, 2);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - AGRO-SMART</title>
    <link rel="stylesheet" href="../styles.css"> <!-- Link to centralized CSS -->
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            background-color: #2ecc71; /* Green sidebar color */
            color: white;
            padding: 20px;
            height: 100vh;
        }
        .main-content {
            flex: 1;
            padding: 20px;
        }
        .overview-cards {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .overview-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            width: calc(25% - 20px); /* Adjust width for four cards in a row */
        }
        .overview-card h3 {
            color: green;
        }
    </style>
</head>
<body>
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

    <div class="main-content">
        <h1>Welcome, Admin!</h1>
        <p>Here's an overview of your platform:</p>

        <div class="overview-cards">
            <div class="overview-card">
                <h3>Total Users</h3>
                <p><?php echo htmlspecialchars($total_users); ?></p>
            </div>
            <div class="overview-card">
                <h3>Total Products</h3>
                <p><?php echo htmlspecialchars($total_products); ?></p>
            </div>
            <div class="overview-card">
                <h3>Total Orders</h3>
                <p><?php echo htmlspecialchars($total_orders); ?></p>
            </div>
            <div class="overview-card">
                <h3>Total Revenue</h3>
                <p>₦<?php echo htmlspecialchars($total_revenue_formatted); ?></p>
            </div>
        </div>
    </div>
</body>
</html>