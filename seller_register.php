<?php
include 'db.php';

$username_err = $password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if (empty($username)) {
        $username_err = "Please enter a username.";
    } else {
        $sql = "SELECT user_id FROM users WHERE username = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows == 1) {
                $username_err = "This username is already taken.";
            }
            $stmt->close();
        }
    }

    if (empty($password)) {
        $password_err = "Please enter a password.";
    } elseif (strlen($password) < 6) {
        $password_err = "Password must have at least 6 characters.";
    }

    if (empty($username_err) && empty($password_err)) {
        $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, 'seller')";
        if ($stmt = $conn->prepare($sql)) {
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bind_param("ss", $username, $param_password);
            if ($stmt->execute()) {
                header("location: seller_login.php");
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Seller</title>
</head>
<body>
    <h2>Register Seller</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div>
            <label>Username</label>
            <input type="text" name="username" value="<?php echo $username; ?>">
            <span><?php echo $username_err; ?></span>
        </div>
        <div>
            <label>Password</label>
            <input type="password" name="password">
            <span><?php echo $password_err; ?></span>
        </div>
        <div>
            <button type="submit">Register</button>
        </div>
    </form>
</body>
</html>
