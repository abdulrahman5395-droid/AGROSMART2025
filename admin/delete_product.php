<?php
session_start();

// Redirect if not logged in as an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

require '../db_connect.php';

// Get the product ID from the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage_products.php");
    exit();
}

$items_id = intval($_GET['id']); // Use 'items_id' to match the database column name

// Fetch the product details to check if it exists and retrieve the image path
$sql_fetch = "SELECT img FROM items WHERE items_id = ?"; // Updated column name
$stmt_fetch = $conn->prepare($sql_fetch);
$stmt_fetch->bind_param("i", $items_id); // Bind parameter with 'items_id'
$stmt_fetch->execute();
$result_fetch = $stmt_fetch->get_result();

if ($result_fetch->num_rows === 0) {
    header("Location: manage_products.php");
    exit();
}

$product = $result_fetch->fetch_assoc();

// Delete related rows in the order_items table
$sql_delete_order_items = "DELETE FROM order_items WHERE product_id = ?";
$stmt_delete_order_items = $conn->prepare($sql_delete_order_items);

if ($stmt_delete_order_items) {
    $stmt_delete_order_items->bind_param("i", $items_id);
    $stmt_delete_order_items->execute();
} else {
    $_SESSION['delete_message'] = "Error preparing statement for order_items: " . $conn->error;
    header("Location: manage_products.php");
    exit();
}

// Delete the product from the database
$sql_delete = "DELETE FROM items WHERE items_id = ?"; // Updated column name
$stmt_delete = $conn->prepare($sql_delete);

if ($stmt_delete) {
    $stmt_delete->bind_param("i", $items_id); // Bind parameter with 'items_id'

    if ($stmt_delete->execute()) {
        // Optionally delete the associated image file
        if (!empty($product['img'])) {
            $image_path = "../" . $product['img']; // Path to the image file
            if (file_exists($image_path)) {
                unlink($image_path); // Delete the file
            }
        }

        // Redirect to the manage products page with a success message
        $_SESSION['delete_message'] = "Product deleted successfully!";
        header("Location: manage_products.php");
        exit();
    } else {
        $_SESSION['delete_message'] = "Error deleting product: " . $conn->error;
        header("Location: manage_products.php");
        exit();
    }
} else {
    $_SESSION['delete_message'] = "Error preparing statement: " . $conn->error;
    header("Location: manage_products.php");
    exit();
}
?>