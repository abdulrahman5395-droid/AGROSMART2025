<?php
session_start();

// Redirect if not logged in as an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

require '../db_connect.php';

// Get the order ID from the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage_orders.php");
    exit();
}

$order_id = intval($_GET['id']);

// Delete the order from the database
$sql_delete_order_items = "DELETE FROM order_items WHERE order_id = ?";
$stmt_delete_order_items = $conn->prepare($sql_delete_order_items);

$sql_delete_order = "DELETE FROM orders WHERE order_id = ?";
$stmt_delete_order = $conn->prepare($sql_delete_order);

if ($stmt_delete_order_items && $stmt_delete_order) {
    // Start transaction to ensure both deletions succeed or fail together
    $conn->begin_transaction();

    try {
        // Delete related order items first
        $stmt_delete_order_items->bind_param("i", $order_id);
        $stmt_delete_order_items->execute();

        // Then delete the order itself
        $stmt_delete_order->bind_param("i", $order_id);
        $stmt_delete_order->execute();

        // Commit the transaction
        $conn->commit();

        $_SESSION['order_message'] = "Order deleted successfully!";
    } catch (Exception $e) {
        // Rollback the transaction in case of error
        $conn->rollback();
        $_SESSION['order_message'] = "Error deleting order: " . $e->getMessage();
    }
} else {
    $_SESSION['order_message'] = "Error preparing statement: " . $conn->error;
}

header("Location: manage_orders.php");
exit();
?>