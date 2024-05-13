<?php
session_start();
include 'db.php';

// Check if user is logged in as admin
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header("location: admin_login.php"); // Redirect to admin login page
    exit;
}

// Check if user ID is provided in the URL
if (!isset($_GET['id'])) {
    header("location: admin_dashboard.php"); // Redirect back to admin dashboard if user ID is not provided
    exit;
}

$user_id = $_GET['id'];

// Fetch user details from the database
$sql = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit;
}

// Handle form submission for updating user details
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $firstName = $_POST['firstName'];
    $secondName = $_POST['secondName'];
    $lastName = $_POST['lastName'];
    // Add more fields as needed

    // Update user details in the database
    $update_sql = "UPDATE users SET username = ?, email = ?, phone = ?, firstName = ?, secondName = ?, lastName = ? WHERE user_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssssssi", $username, $email, $phone, $firstName, $secondName, $lastName, $user_id);
    if ($update_stmt->execute()) {
        header("location: admin_dashboard.php"); // Redirect to admin dashboard after successful update
        exit;
    } else {
        echo "Failed to update user.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
</head>
<body>
    <h2>Edit User</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . '?id=' . $user_id); ?>" method="post">
        <div>
            <label>Username</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>">
        </div>
        <div>
            <label>Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
        </div>
        <div>
            <label>Phone</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
        </div>
        <div>
            <label>First Name</label>
            <input type="text" name="firstName" value="<?php echo htmlspecialchars($user['firstName']); ?>">
        </div>
        <div>
            <label>Second Name</label>
            <input type="text" name="secondName" value="<?php echo htmlspecialchars($user['secondName']); ?>">
        </div>
        <div>
            <label>Last Name</label>
            <input type="text" name="lastName" value="<?php echo htmlspecialchars($user['lastName']); ?>">
        </div>
        <!-- Add more fields for editing user details -->
        <div>
            <button type="submit">Update</button>
        </div>
    </form>
</body>
</html>
