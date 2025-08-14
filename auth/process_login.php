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

// Validate inputs
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    $_SESSION['login_error'] = "All fields are required.";
    header("Location: login.php");
    exit();
}

// Include the database connection
require '../db_connect.php';

// Check if the email exists in the database
$sql_check_user = "SELECT * FROM users WHERE email = ?";
$stmt_check_user = $conn->prepare($sql_check_user);
$stmt_check_user->bind_param("s", $email);
$stmt_check_user->execute();
$result_check_user = $stmt_check_user->get_result();

if ($result_check_user->num_rows === 0) {
    $_SESSION['login_error'] = "Invalid email or password.";
    header("Location: login.php");
    exit();
}

// Fetch user details
$user = $result_check_user->fetch_assoc();

// Verify the password
if (!password_verify($password, $user['password'])) {
    $_SESSION['login_error'] = "Invalid email or password.";
    header("Location: login.php");
    exit();
}

// Set session variables
$_SESSION['user_id'] = $user['users_id'];
$_SESSION['user_type'] = $user['user_type'];

// Debugging: Display session variables
echo "<pre>";
var_dump($_SESSION);
echo "</pre>";

// Redirect based on user type
if ($user['user_type'] === 'farmer') {
    echo "Redirecting to farmer dashboard...";
    header("Location: ../farmer/farmer.php");
    exit();
} elseif ($user['user_type'] === 'buyer') {
    echo "Redirecting to buyer dashboard...";
    header("Location: ../buyer/buyer.php");
    exit();
} elseif ($user['user_type'] === 'admin') {
    echo "Redirecting to admin dashboard...";
    header("Location: ../admin/admin.php");
    exit();
} else {
    echo "Redirecting to homepage...";
    header("Location: ../index.php");
    exit();
}

$stmt_check_user->close();
$conn->close();
?>