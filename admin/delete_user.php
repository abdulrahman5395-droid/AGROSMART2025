<?php
session_start();

// Redirect if not logged in as an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

require '../db_connect.php';

// Get the user ID from the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage_users.php");
    exit();
}

$user_id = intval($_GET['id']);

// Delete the user from the database
$sql_delete = "DELETE FROM users WHERE users_id = ?";
$stmt_delete = $conn->prepare($sql_delete);

if ($stmt_delete) {
    $stmt_delete->bind_param("i", $user_id);

    if ($stmt_delete->execute()) {
        $_SESSION['delete_user_message'] = "User deleted successfully!";
    } else {
        $_SESSION['delete_user_message'] = "Error deleting user: " . $conn->error;
    }
} else {
    $_SESSION['delete_user_message'] = "Error preparing statement: " . $conn->error;
}

header("Location: manage_users.php");
exit();
?>