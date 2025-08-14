<?php
session_start();

// Ensure the user is logged in as a buyer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'buyer') {
    header("Location: ../auth/login.php");
    exit();
}

// Include the database connection
require '../db_connect.php';

// Get the product ID from the query string
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: marketplace.php");
    exit();
}

$product_id = $_GET['id'];

// Fetch product details to ensure the product exists
$sql_product = "SELECT items_id, name, price, stock FROM items WHERE items_id = ?";
$stmt_product = $conn->prepare($sql_product);
$stmt_product->bind_param("i", $product_id);
$stmt_product->execute();
$result_product = $stmt_product->get_result();

if ($result_product->num_rows === 0) {
    header("Location: marketplace.php");
    exit();
}

$product = $result_product->fetch_assoc();

// Initialize the cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add or update the product in the cart
if (isset($_SESSION['cart'][$product_id])) {
    // Increase the quantity if the product is already in the cart
    $_SESSION['cart'][$product_id]['quantity'] += 1;
} else {
    // Add the product to the cart with an initial quantity of 1
    $_SESSION['cart'][$product_id] = [
        'quantity' => 1,
        'name' => $product['name'],
        'price' => $product['price']
    ];
}

// Redirect back to the marketplace or product details page
header("Location: cart.php");
exit();
?>