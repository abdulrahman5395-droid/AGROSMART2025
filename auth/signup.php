<?php
session_start();

// Display error messages if any
if (isset($_SESSION['signup_error'])) {
    echo '<p class="error">' . htmlspecialchars($_SESSION['signup_error']) . '</p>';
    unset($_SESSION['signup_error']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - AGRO-SMART</title>
    <link rel="stylesheet" href="../styles.css"> <!-- Link to centralized CSS -->
    <style>
        /* General Styles */
        body {
            background-image: url('../images/lukasz-szmigiel-gmsiVT5sfl0-unsplash.jpg'); /* Replace with the path to your image */
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
        main.signup-form {
            flex: 1; /* Push the footer to the bottom */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding-top: 60px; /* Add padding to avoid overlap with the fixed header */
            padding-bottom: 60px; /* Add padding to avoid overlap with the fixed footer */
        }
        .signup-form {
            background-color: rgba(0, 0, 0, 0.7); /* Semi-transparent overlay for better readability */
            padding: 20px;
            border-radius: 10px;
            width: 100%;
            max-width: 600px; /* Increase max-width for two-column layout */
            text-align: center;
        }
        .signup-form h2 {
            margin-bottom: 10px;
        }
        .signup-form p {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"], input[type="email"], input[type="password"], input[type="tel"], textarea, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
        }
        input[type="text"]::placeholder, input[type="email"]::placeholder, input[type="password"]::placeholder, input[type="tel"]::placeholder, textarea::placeholder {
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

        /* Two-Column Layout */
        .form-row {
            display: flex;
            gap: 20px; /* Space between columns */
            margin-bottom: 15px;
        }
        .form-row > div {
            flex: 1; /* Each column takes equal width */
        }
        @media (max-width: 768px) {
            .form-row {
                flex-direction: column; /* Stack fields vertically on smaller screens */
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <header class="topnav">
        <a href="../index.php">Home</a>
        <div class="topnav-right">
            <a href="login.php">Login</a>
        </div>
    </header>

    <main class="signup-form">
        <h2>Create Your Account</h2>
        <p>Please fill out the form below to sign up.</p>

        <form action="process_signup.php" method="POST">
            <!-- User Type -->
            <label for="user_type">User Type:</label>
            <select id="user_type" name="user_type" required>
                <option value="">Select User Type</option>
                <option value="farmer">Farmer</option>
                <option value="buyer">Buyer</option>
                <option value="admin">Admin</option>
            </select>

            <!-- Two-Column Layout -->
            <div class="form-row">
                <div>
                    <label for="firstname">First Name:</label>
                    <input type="text" id="firstname" name="firstname" placeholder="Enter your first name" required>
                </div>
                <div>
                    <label for="lastname">Last Name:</label>
                    <input type="text" id="lastname" name="lastname" placeholder="Enter your last name" required>
                </div>
            </div>

            <div class="form-row">
                <div>
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>
                <div>
                    <label for="mobile">Mobile:</label>
                    <input type="tel" id="mobile" name="mobile" placeholder="Enter your mobile number" required>
                </div>
            </div>

            <!-- Single-Column Layout -->
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Minimum: 6 Characters" minlength="6" required>

            <label for="address">Address:</label>
            <textarea id="address" name="address" placeholder="Enter your address" rows="3" required></textarea>

            <div class="form-row">
                <div>
                    <label for="country">Country:</label>
                    <select id="country" name="country" required>
                        <option value="">Select Country</option>
                        <option value="Nigeria">Nigeria</option>
                        <option value="Ghana">Ghana</option>
                        <option value="Kenya">Kenya</option>
                        <option value="South Africa">South Africa</option>
                    </select>
                </div>
                <div>
                    <label for="state">State:</label>
                    <select id="state" name="state" required>
                        <option value="">Select State</option>
                        <option value="Lagos">Lagos</option>
                        <option value="Abuja">Abuja</option>
                        <option value="Rivers">Rivers</option>
                        <option value="Kano">Kano</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div>
                    <label for="district">District:</label>
                    <input type="text" id="district" name="district" placeholder="Enter your district" required>
                </div>
                <div>
                    <label for="city">City:</label>
                    <input type="text" id="city" name="city" placeholder="Enter your city" required>
                </div>
            </div>

            <!-- Admin Code Field (Hidden by default) -->
            <div id="admin-code-section" style="display: none;">
                <label for="admin_code">Admin Code:</label>
                <input type="text" id="admin_code" name="admin_code" placeholder="Enter admin code" required>
            </div>

            <button type="submit" class="btn">Sign Up</button>
        </form>

        <script>
            const userTypeSelect = document.getElementById('user_type');
            const adminCodeSection = document.getElementById('admin-code-section');
            const adminCodeInput = document.getElementById('admin_code');

            userTypeSelect.addEventListener('change', () => {
                if (userTypeSelect.value === 'admin') {
                    adminCodeSection.style.display = 'block';
                    adminCodeInput.setAttribute('required', 'required');
                } else {
                    adminCodeSection.style.display = 'none';
                    adminCodeInput.removeAttribute('required');
                }
            });
        </script>
    </main>

    <footer>
        <p>&copy; 2025 AGRO-SMART. All rights reserved. | 
            <a href="#">Privacy Policy</a> | 
            <a href="#">Terms of Service</a>
        </p>
    </footer>
</body>
</html>