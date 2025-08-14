<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AGRO-SMART</title>
    <link rel="stylesheet" href="../styles.css"> <!-- Link to external CSS file -->
    <style>
        /* General Styles */
        body {
            background-image: url('../images/irewolede-PvwdlXqo85k-unsplash (1).jpg'); /* Replace with the path to your image */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: white;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh; /* Ensure the body takes up the full viewport height */
            display: flex;
            flex-direction: column;
        }
        header.topnav {
            background-color: rgba(0, 0, 0, 0.7);
            padding: 10px;
            width: 100%;
            text-align: center;
            position: fixed; /* Fix the header at the top */
            top: 0;
            left: 0;
            z-index: 1000; /* Ensure the header stays above other elements */
        }
        .topnav a {
            color: white;
            text-decoration: none;
            margin-right: 15px;
        }
        main.login-form {
            flex: 1; /* Push the footer to the bottom */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding-top: 60px; /* Add padding to avoid overlap with the fixed header */
        }
        .login-form {
            background-color: rgba(0, 0, 0, 0.7); /* Semi-transparent overlay for better readability */
            padding: 20px;
            border-radius: 10px;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-form h2 {
            margin-bottom: 10px;
        }
        .login-form p {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
        }
        input[type="email"]::placeholder, input[type="password"]::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }
        .btn {
            display: inline-block;
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
        footer {
            background-color: rgba(0, 0, 0, 0.8); /* Slightly lighter background */
            text-align: center;
            padding: 15px;
            color: #ffffff; /* Light text for better contrast */
            font-size: 0.9em;
            position: fixed;
            bottom: 0;
            width: 100%;
            z-index: 1000; /* Ensure the footer stays above other elements */
        }
        footer a {
            color: #4CAF50; /* Green links for better visibility */
            text-decoration: none;
        }
        footer a:hover {
            text-decoration: underline;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <header class="topnav">
        <a href="../index.php">Home</a>
        <div class="topnav-right">
            <a href="signup.php">Sign Up</a>
            <a href="../admin/admin.php">Admin</a>
            <a href="../farmer/farmer.php">Farmer Dashboard</a>
        </div>
    </header>

    <main class="login-form">
        <h2>Login to AGRO-SMART</h2>
        <p>Enter your credentials to access your account.</p>

        <?php
        // Example PHP code to display errors (if any)
        if (isset($_SESSION['login_error'])) {
            echo '<p class="error">' . htmlspecialchars($_SESSION['login_error']) . '</p>';
            unset($_SESSION['login_error']);
        }
        ?>

        <form action="process_login.php" method="POST">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>

            <button type="submit" class="btn">Login</button>
        </form>
    </main>

    <footer>
        <p>&copy; 2025 AGRO-SMART. All rights reserved. | 
            <a href="#">Privacy Policy</a> | 
            <a href="#">Terms of Service</a>
        </p>
    </footer>
</body>
</html>