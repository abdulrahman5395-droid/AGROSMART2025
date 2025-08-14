<?php
session_start();

// Redirect if not logged in as a buyer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'buyer') {
    header("Location: ../auth/login.php");
    exit();
}

require '../db_connect.php';

$user_id = $_SESSION['user_id'];

// Fetch current profile data
$sql_profile = "SELECT * FROM users WHERE users_id = ?";
$stmt_profile = $conn->prepare($sql_profile);
$stmt_profile->bind_param("i", $user_id);
$stmt_profile->execute();
$result_profile = $stmt_profile->get_result();
$buyer = $result_profile->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $mobile = trim($_POST['mobile']);
    $adr = trim($_POST['adr']);
    $country = trim($_POST['country']);
    $state = trim($_POST['state']);
    $district = trim($_POST['district']);
    $city = trim($_POST['city']);

    // Validate inputs
    if (empty($firstname) || empty($lastname) || empty($email) || empty($mobile)) {
        $error_message = "All fields marked with * are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } elseif (!is_numeric($mobile)) {
        $error_message = "Mobile number must be numeric.";
    } else {
        // Update the profile in the database
        $sql_update = "UPDATE users SET firstname = ?, lastname = ?, email = ?, mobile = ?, adr = ?, country = ?, state = ?, district = ?, city = ? WHERE users_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sssssssssi", $firstname, $lastname, $email, $mobile, $adr, $country, $state, $district, $city, $user_id);

        if ($stmt_update->execute()) {
            $success_message = "Profile updated successfully!";
            // Refresh the buyer's session data
            $_SESSION['firstname'] = $firstname;
            $_SESSION['lastname'] = $lastname;
        } else {
            $error_message = "Error updating profile: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buyer Profile - AGRO-SMART</title>
    <link rel="stylesheet" href="../styles.css"> <!-- Link to centralized CSS -->
    <style>
        body {
            background-image: url('../images/lumin-osity-6DMht7wYt6g-unsplash.jpg'); /* Replace with your image path */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: white;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        header.topnav {
            background-color: #4CAF50;
            padding: 10px;
            width: 100%;
            text-align: center;
            position: fixed; /* Fix the header at the top */
            top: 0;
            left: 0;
            z-index: 1000;
        }
        .topnav a {
            color: white;
            text-decoration: none;
            margin-right: 15px;
        }
        main.buyer-profile-page {
            flex: 1; /* Push the footer to the bottom */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding-top: 60px; /* Add padding to avoid overlap with the fixed header */
            padding-bottom: 60px; /* Add padding to avoid overlap with the fixed footer */
        }
        .profile-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.7); /* Dark translucent background */
            border-radius: 10px;
            text-align: left;
        }
        h1, h2 {
            color: #fff;
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
            transition: background-color 0.3s ease;
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
        .section-header {
            margin-top: 20px;
            margin-bottom: 10px;
            font-size: 1.2em;
            font-weight: bold;
        }
        .two-columns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
    </style>
</head>
<body>
    <header class="topnav">
        <a href="../index.php">Home</a>
        <div class="topnav-right">
            <a href="../auth/logout.php">Logout</a>
        </div>
    </header>

    <main class="buyer-profile-page">
        <div class="profile-container">
            <h1>Your Profile</h1>

            <?php if (isset($success_message)): ?>
                <p class="message success-message"><?php echo htmlspecialchars($success_message); ?></p>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <p class="message error-message"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>

            <form action="" method="POST" class="edit-profile-form">

                <!-- Section 1: Personal Information -->
                <div class="section-header">Personal Information</div>
                <div class="two-columns">
                    <div class="form-group">
                        <label for="firstname">First Name *</label>
                        <input type="text" id="firstname" name="firstname" value="<?php echo htmlspecialchars($buyer['firstname']); ?>" placeholder="Enter your first name" required>
                    </div>
                    <div class="form-group">
                        <label for="lastname">Last Name *</label>
                        <input type="text" id="lastname" name="lastname" value="<?php echo htmlspecialchars($buyer['lastname']); ?>" placeholder="Enter your last name" required>
                    </div>
                </div>

                <!-- Section 2: Contact Information -->
                <div class="section-header">Contact Information</div>
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($buyer['email']); ?>" placeholder="Enter your email address" required>
                </div>
                <div class="form-group">
                    <label for="mobile">Mobile Number *</label>
                    <input type="text" id="mobile" name="mobile" value="<?php echo htmlspecialchars($buyer['mobile']); ?>" placeholder="Enter your mobile number" required>
                </div>

                <!-- Section 3: Address Details -->
                <div class="section-header">Address Details</div>
                <div class="form-group">
                    <label for="adr">Address</label>
                    <textarea id="adr" name="adr" rows="3" placeholder="Enter your address"><?php echo htmlspecialchars($buyer['adr']); ?></textarea>
                </div>
                <div class="two-columns">
                    <div class="form-group">
                        <label for="country">Country</label>
                        <input type="text" id="country" name="country" value="<?php echo htmlspecialchars($buyer['country']); ?>" placeholder="Enter your country">
                    </div>
                    <div class="form-group">
                        <label for="state">State</label>
                        <input type="text" id="state" name="state" value="<?php echo htmlspecialchars($buyer['state']); ?>" placeholder="Enter your state">
                    </div>
                </div>
                <div class="two-columns">
                    <div class="form-group">
                        <label for="district">District</label>
                        <input type="text" id="district" name="district" value="<?php echo htmlspecialchars($buyer['district']); ?>" placeholder="Enter your district">
                    </div>
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($buyer['city']); ?>" placeholder="Enter your city">
                    </div>
                </div>

                <button type="submit" class="btn">Update Profile</button>
            </form>
        </div>
    </main>

    <footer>
        <p>© 2025 AGRO-SMART. All rights reserved.</p>
    </footer>
</body>
</html>