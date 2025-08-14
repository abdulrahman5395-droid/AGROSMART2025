<?php
session_start();

// Redirect if not logged in as an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

require '../db_connect.php';

$error_message = '';
$success_message = '';

// Get the product ID from the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage_products.php");
    exit();
}

$item_id = intval($_GET['id']);

// Fetch the product details
$sql_fetch = "SELECT * FROM items WHERE items_id = ?";
$stmt_fetch = $conn->prepare($sql_fetch);
$stmt_fetch->bind_param("i", $item_id);
$stmt_fetch->execute();
$result_fetch = $stmt_fetch->get_result();

if ($result_fetch->num_rows === 0) {
    header("Location: manage_products.php");
    exit();
}

$product = $result_fetch->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $name = trim($_POST['name']);
    $brand = trim($_POST['brand']);
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $reorder_level = intval($_POST['rol']);
    $current_image = $product['img']; // Preserve the current image unless a new one is uploaded

    // Validate inputs
    if (empty($name) || empty($brand) || empty($category) || empty($price) || empty($stock)) {
        $error_message = "All fields marked with * are required.";
    } elseif ($price <= 0) {
        $error_message = "Price must be greater than zero.";
    } elseif ($stock < 0) {
        $error_message = "Stock cannot be negative.";
    } else {
        // Handle image upload
        $image_url = $current_image; // Default to the current image

        if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
            $target_dir = "../uploads/"; // Directory to store uploaded images
            $target_file = $target_dir . basename($_FILES["img"]["name"]);

            // Check if the file is an actual image
            $check = getimagesize($_FILES["img"]["tmp_name"]);
            if ($check !== false) {
                // Move the uploaded file to the target directory
                if (move_uploaded_file($_FILES["img"]["tmp_name"], $target_file)) {
                    $image_url = "uploads/" . basename($_FILES["img"]["name"]); // Store relative path
                } else {
                    $error_message = "Sorry, there was an error uploading your file.";
                }
            } else {
                $error_message = "File is not an image.";
            }
        }

        if (empty($error_message)) {
            // Update the product in the database
            $sql_update = "UPDATE items 
                           SET name = ?, brand = ?, description = ?, category = ?, price = ?, stock = ?, rol = ?, img = ? 
                           WHERE items_id = ?";
            $stmt_update = $conn->prepare($sql_update);

            if ($stmt_update) {
                $stmt_update->bind_param("ssssdddsi", $name, $brand, $description, $category, $price, $stock, $reorder_level, $image_url, $item_id);

                if ($stmt_update->execute()) {
                    $success_message = "Product updated successfully!";
                    // Refresh the product details
                    $product = [
                        'name' => $name,
                        'brand' => $brand,
                        'category' => $category,
                        'description' => $description,
                        'price' => $price,
                        'stock' => $stock,
                        'rol' => $reorder_level,
                        'img' => $image_url,
                    ];
                } else {
                    $error_message = "Error updating product: " . $conn->error;
                }
            } else {
                $error_message = "Error preparing statement: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - AGRO-SMART</title>
    <link rel="stylesheet" href="../styles.css"> <!-- Link to centralized CSS -->
    <style>
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
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"], textarea, select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
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
            <h1>Edit Product</h1>

            <?php if (!empty($success_message)): ?>
                <p class="message success-message"><?php echo htmlspecialchars($success_message); ?></p>
            <?php endif; ?>

            <?php if (!empty($error_message)): ?>
                <p class="message error-message"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Product Name *</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="brand">Brand *</label>
                    <input type="text" id="brand" name="brand" value="<?php echo htmlspecialchars($product['brand']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="category">Category *</label>
                    <input type="text" id="category" name="category" value="<?php echo htmlspecialchars($product['category']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3"><?php echo htmlspecialchars($product['description']); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="price">Price (₦) *</label>
                    <input type="number" step="0.01" id="price" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="stock">Stock *</label>
                    <input type="number" id="stock" name="stock" value="<?php echo htmlspecialchars($product['stock']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="rol">Reorder Level *</label>
                    <input type="number" id="rol" name="rol" value="<?php echo htmlspecialchars($product['rol']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="img">Current Image</label>
                    <?php if (!empty($product['img'])): ?>
                        <img src="../<?php echo htmlspecialchars($product['img']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="max-width: 100px; height: auto;">
                    <?php else: ?>
                        <p>No image available</p>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="img">Upload New Image</label>
                    <input type="file" id="img" name="img" accept="image/*">
                </div>
                <button type="submit" class="btn">Update Product</button>
            </form>
        </div>
    </div>
</body>
</html>