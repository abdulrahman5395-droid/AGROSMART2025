<?php
session_start();

// Redirect if not logged in as an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

require '../db_connect.php';

$action = $_GET['action'] ?? '';
$product_id = $_GET['id'] ?? null;

// Fetch product details for editing
if ($action === 'edit' && $product_id) {
    $sql_product = "SELECT * FROM items WHERE item_id = ?";
    $stmt_product = $conn->prepare($sql_product);
    $stmt_product->bind_param("i", $product_id);
    $stmt_product->execute();
    $result_product = $stmt_product->get_result();
    $product = $result_product->fetch_assoc();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $name = trim($_POST['name']);
    $brand = trim($_POST['brand']);
    $description = trim($_POST['description']);
    $category = trim($_POST['category']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $rol = intval($_POST['rol']);
    $farmer_id = intval($_POST['farmer_id']);

    // Handle image upload
    $image_url = isset($product['img']) ? $product['img'] : ''; // Preserve existing image

    if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($_FILES["img"]["name"]);
        $check = getimagesize($_FILES["img"]["tmp_name"]);

        if ($check !== false) {
            if (move_uploaded_file($_FILES["img"]["tmp_name"], $target_file)) {
                $image_url = "uploads/" . basename($_FILES["img"]["name"]);
            } else {
                $error_message = "Error uploading image.";
            }
        } else {
            $error_message = "File is not an image.";
        }
    }

    if (empty($error_message)) {
        if ($action === 'add') {
            // Insert new product
            $sql_insert = "INSERT INTO items (farmer_id, name, brand, description, category, price, stock, rol, img) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("issssdiss", $farmer_id, $name, $brand, $description, $category, $price, $stock, $rol, $image_url);

            if ($stmt_insert->execute()) {
                header("Location: manage_products.php");
                exit();
            } else {
                $error_message = "Error adding product: " . $conn->error;
            }
        } elseif ($action === 'edit') {
            // Update existing product
            $sql_update = "UPDATE items SET farmer_id = ?, name = ?, brand = ?, description = ?, category = ?, price = ?, stock = ?, rol = ?, img = ? WHERE item_id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("issssdissi", $farmer_id, $name, $brand, $description, $category, $price, $stock, $rol, $image_url, $product_id);

            if ($stmt_update->execute()) {
                header("Location: manage_products.php");
                exit();
            } else {
                $error_message = "Error updating product: " . $conn->error;
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
    <title><?php echo ucfirst($action); ?> Product - AGRO-SMART</title>
    <link rel="stylesheet" href="../styles.css"> <!-- Link to centralized CSS -->
</head>
<body>
    <div class="container">
        <h1><?php echo ucfirst($action); ?> Product</h1>

        <?php if (isset($error_message)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="farmer_id">Farmer ID:</label>
                <input type="number" id="farmer_id" name="farmer_id" value="<?php echo htmlspecialchars($product['farmer_id'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="brand">Brand:</label>
                <input type="text" id="brand" name="brand" value="<?php echo htmlspecialchars($product['brand'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label for="category">Category:</label>
                <input type="text" id="category" name="category" value="<?php echo htmlspecialchars($product['category'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="price">Price (₦):</label>
                <input type="number" step="0.01" id="price" name="price" value="<?php echo htmlspecialchars($product['price'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="stock">Stock:</label>
                <input type="number" id="stock" name="stock" value="<?php echo htmlspecialchars($product['stock'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="rol">Reorder Level:</label>
                <input type="number" id="rol" name="rol" value="<?php echo htmlspecialchars($product['rol'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="img">Image:</label>
                <input type="file" id="img" name="img">
                <?php if (isset($product['img'])): ?>
                    <p>Current Image: <img src="../<?php echo htmlspecialchars($product['img']); ?>" alt="Current Image" style="width: 50px; height: 50px;"></p>
                <?php endif; ?>
            </div>
            <button type="submit" class="btn"><?php echo ucfirst($action); ?> Product</button>
        </form>
    </div>
</body>
</html>