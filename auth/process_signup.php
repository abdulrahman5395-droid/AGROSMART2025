<?php
// Enable full error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Debugging: Display submitted form data
echo "<pre>";
var_dump($_POST);
echo "</pre>";

// Step 1: Validate and sanitize inputs
$user_type = trim($_POST['user_type'] ?? '');
$firstname = trim($_POST['firstname'] ?? '');
$lastname = trim($_POST['lastname'] ?? '');
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$mobile = trim($_POST['mobile'] ?? '');
$password = $_POST['password'] ?? '';
$address = trim($_POST['address'] ?? '');
$country = trim($_POST['country'] ?? '');
$state = trim($_POST['state'] ?? '');
$district = trim($_POST['district'] ?? '');
$city = trim($_POST['city'] ?? '');

// Sanitize admin code regardless, then validate only if needed
$admin_code = $user_type === 'admin' ? trim($_POST['admin_code'] ?? '') : '';

if ($user_type === 'admin') {
    $correct_admin_code = 'admin123'; // Define the correct admin code
    if ($admin_code !== $correct_admin_code) {
        $_SESSION['signup_error'] = "Invalid admin code.";
        header("Location: signup.php");
        exit();
    }
}

// Step 2: Validate required fields
if (empty($user_type) || empty($firstname) || empty($lastname) || empty($email) || empty($mobile) || empty($password) || empty($address) || empty($country) || empty($state) || empty($district) || empty($city)) {
    $_SESSION['signup_error'] = "All fields are required.";
    header("Location: signup.php");
    exit();
}

// Step 3: Include the database connection
require '../db_connect.php';

// Step 4: Check if email already exists in the database
$sql_check_email = "SELECT * FROM users WHERE email = ?";
$stmt_check_email = $conn->prepare($sql_check_email);
$stmt_check_email->bind_param("s", $email);
$stmt_check_email->execute();
$result_check_email = $stmt_check_email->get_result();

if ($result_check_email->num_rows > 0) {
    $_SESSION['signup_error'] = "An account with this email already exists.";
    header("Location: signup.php");
    exit();
}

// Step 5: Insert user into the database
$hashed_password = password_hash($password, PASSWORD_DEFAULT); // Secure password hashing
$sql_insert_user = "INSERT INTO users (user_type, firstname, lastname, email, mobile, password, adr, country, state, district, city) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt_insert_user = $conn->prepare($sql_insert_user);

if (!$stmt_insert_user) {
    $_SESSION['signup_error'] = "Database preparation error: " . $conn->error;
    header("Location: signup.php");
    exit();
}

$stmt_insert_user->bind_param("sssssssssss", $user_type, $firstname, $lastname, $email, $mobile, $hashed_password, $address, $country, $state, $district, $city);

if ($stmt_insert_user->execute()) {
    // Step 6: Set session variables
    $new_user_id = $conn->insert_id;
    $_SESSION['user_id'] = $new_user_id;
    $_SESSION['user_type'] = $user_type;

    // Step 7: Redirect based on user type
    if ($user_type === 'farmer') {
        header("Location: ../farmer/farmer.php");
        exit();
    } elseif ($user_type === 'buyer') {
        header("Location: ../buyer/buyer.php");
        exit();
    } elseif ($user_type === 'admin') {
        header("Location: ../admin/admin.php");
        exit();
    } else {
        header("Location: ../index.html");
        exit();
    }
} else {
    $_SESSION['signup_error'] = "Database insertion error: " . $stmt_insert_user->error;
    header("Location: signup.php");
    exit();
}

$stmt_insert_user->close();
$conn->close();
?>
