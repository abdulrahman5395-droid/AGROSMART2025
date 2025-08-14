<?php
require 'categories.php'; // Include the centralized category list
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to AGRO-SMART</title>
    <link rel="stylesheet" href="styles.css"> <!-- External CSS file -->
    <style>
        /* General Styles */
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            color: #333;
        }

        /* Navigation Bar */
        nav.topnav {
            background-color: #4CAF50;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .topnav a {
            color: white;
            text-decoration: none;
            margin-right: 15px;
        }
        .topnav-right {
            display: flex;
            gap: 15px;
        }

        /* Hero Section */
        header.hero {
            position: relative;
            height: 400px;
            background-image: url('images/pexels-timmossholder-974314.jpg'); /* Replace with your image path */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: white;
            text-align: center;
        }
        header.hero .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6); /* Dark translucent overlay */
        }
        .hero-content {
            position: relative;
            z-index: 1;
            padding-top: 100px;
            background-color: rgba(0, 0, 0, 0.8); /* Dark background for the content box */
            margin: 0 auto;
            max-width: 600px;
            padding: 20px;
            border-radius: 10px;
        }
        .hero-content h1 {
            font-size: 3em;
            margin-bottom: 10px;
        }
        .hero-content p {
            font-size: 1.2em;
            margin-bottom: 20px;
        }
        .buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        .btn {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #45a049;
        }

        /* Main Content */
        main {
            padding: 20px;
        }
        .categories h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 20px;
        }
        .category {
            display: block;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .category:hover {
            background-color: #45a049;
        }

        /* Footer */
        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 15px;
            font-size: 0.9em;
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
    <!-- Navigation Bar -->
    <nav class="topnav">
        <a href="index.php">Home</a>
        <a href="item.php">All Items</a>
        <div class="topnav-right">
            <a href="auth/signup.php">Sign Up</a>
            <a href="admin/admin.php">Admin</a>
            <a href="auth/login.php">Login</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero">
        <div class="overlay"></div>
        <div class="hero-content">
            <h1>AGRO-SMART</h1>
            <p>Empowering Agriculture, Connecting Farmers & Consumers</p>
            <div class="buttons">
                <a href="auth/signup.php" class="btn">Join AGRO-SMART</a>
                <a href="marketplace.php" class="btn">Explore Marketplace</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <section class="categories">
            <h2>Browse Categories</h2>
            <div class="category-grid">
                <?php foreach ($categories as $category): ?>
                    <a href="view.php?choice=<?php echo urlencode($category); ?>" class="category"><?php echo htmlspecialchars($category); ?></a>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 AGRO-SMART. All rights reserved. | 
            <a href="#">Privacy Policy</a> | 
            <a href="#">Terms of Service</a>
        </p>
    </footer>
</body>
</html>