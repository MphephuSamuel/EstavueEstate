<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Delete the user from the database
    $sql = "DELETE FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        // Redirect back to the admin dashboard after deleting the user
        header("location: admin_dashboard.php");
        exit();
    } else {
        // If there's an error, display it
        echo "Error deleting user: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
} else {
    // If user ID is not provided in the request, redirect back to the admin dashboard
    header("location: admin_dashboard.php");
    exit();
}
?>
