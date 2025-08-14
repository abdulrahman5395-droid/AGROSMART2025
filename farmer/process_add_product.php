<?php
session_start();
require '../db_connect.php';

// Redirect if not logged in as a farmer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'farmer') {
    header("Location: ../auth/login.php");
    exit();
}

// Get the logged-in farmer's ID
$user_id = $_SESSION['user_id'];

// Validate and sanitize form inputs
$name = trim($_POST['name']);
$brand = trim($_POST['brand']);
$description = trim($_POST['description']);
$category = trim($_POST['types']); // Ensure this matches the name attribute in the form
$price = floatval($_POST['price']);
$stock = intval($_POST['stock']);
$rol = intval($_POST['rol']);

// Handle file upload
if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = "../uploads/";
    $file_name = basename($_FILES['img']['name']);
    $file_path = $upload_dir . $file_name;

    // Move the uploaded file to the uploads directory
    if (move_uploaded_file($_FILES['img']['tmp_name'], $file_path)) {
        $img = $file_path;
    } else {
        die("Error uploading file.");
    }
} else {
    die("No file uploaded or an error occurred.");
}

// Insert the product into the database
$sql = "INSERT INTO items (farmer_id, name, brand, description, types, price, stock, rol, img)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param(
    "issssdiii",
    $user_id,
    $name,
    $brand,
    $description,
    $category,
    $price,
    $stock,
    $rol,
    $img
);

if ($stmt->execute()) {
    header("Location: farmer.php?success=Product added successfully.");
    exit();
} else {
    die("Execute failed: " . $stmt->error);
}
?>