<?php
session_start();

// Redirect if not logged in as a farmer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'farmer') {
    header("Location: ../auth/login.php");
    exit();
}

require '../db_connect.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $product_name = trim($_POST['product_name']);
    $brand_name = trim($_POST['brand_name']);
    $description = trim($_POST['description']);
    $category = trim($_POST['category']);

    // Validate form data
    if (empty($product_name) || empty($brand_name) || empty($description) || empty($category)) {
        header("Location: farmer.php?error=All fields are required.");
        exit();
    }

    // Insert the product into the database
    $farmer_id = intval($_SESSION['user_id']); // Ensure user_id is an integer

    $sql_add_product = "INSERT INTO products (farmer_id, product_name, brand, description, category) 
                        VALUES (?, ?, ?, ?, ?)";
    $stmt_add_product = $conn->prepare($sql_add_product);

    if ($stmt_add_product) {
        $stmt_add_product->bind_param("issss", $farmer_id, $product_name, $brand_name, $description, $category);

        if ($stmt_add_product->execute()) {
            header("Location: farmer.php?success=Product added successfully.");
            exit();
        } else {
            // Debugging: Print the error for troubleshooting
            echo "Error: " . $conn->error;
            exit();
        }
    } else {
        // Debugging: Print the error for troubleshooting
        echo "Error preparing statement: " . $conn->error;
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - AGRO-SMART</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap @5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f9;
            font-family: Arial, sans-serif;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <?php
        if (isset($_GET['success'])) {
            echo '<div class="alert alert-success" role="alert">' . htmlspecialchars($_GET['success']) . '</div>';
        } elseif (isset($_GET['error'])) {
            echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($_GET['error']) . '</div>';
        }
        ?>
        <h1>Add a New Product</h1>
        <form method="POST" action="add_product.php">
            <div class="mb-3">
                <label for="product_name" class="form-label">Product Name</label>
                <input type="text" id="product_name" name="product_name" class="form-control" placeholder="Enter product name" required>
            </div>
            <div class="mb-3">
                <label for="brand_name" class="form-label">Brand</label>
                <input type="text" id="brand_name" name="brand_name" class="form-control" placeholder="Enter brand name" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-control" rows="4" placeholder="Enter product description" required></textarea>
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <select id="category" name="category" class="form-control" required>
                    <option value="">Select Category</option>
                    <option value="Vegetables">Vegetables</option>
                    <option value="Fruits">Fruits</option>
                    <option value="Grains">Grains</option>
                    <option value="Livestock">Livestock</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add Product</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap @5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>