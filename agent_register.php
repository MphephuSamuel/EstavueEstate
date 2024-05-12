<?php
include 'db.php';  // Include your database connection

// Define variables and initialize with empty values
$agent_username = $agent_password = "";
$username_err = $password_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate username
    if(empty(trim($_POST["agent_username"]))) {
        $username_err = "Please enter a username.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["agent_username"]))) {
        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else {
        // Prepare a select statement to check if username exists
        $sql = "SELECT user_id FROM users WHERE username = ?";
        if($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_username);
            $param_username = trim($_POST["agent_username"]);
            if($stmt->execute()) {
                $stmt->store_result();
                if($stmt->num_rows == 1) {
                    $username_err = "This username is already taken.";
                } else {
                    $agent_username = trim($_POST["agent_username"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
    }

    // Validate password
    if(empty(trim($_POST["agent_password"]))) {
        $password_err = "Please enter a password.";
    } elseif(strlen(trim($_POST["agent_password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $agent_password = trim($_POST["agent_password"]);
    }

    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, 'agent')";
        if($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ss", $param_username, $param_password);
            $param_username = $agent_username;
            $param_password = password_hash($agent_password, PASSWORD_DEFAULT); // Creates a password hash

            if($stmt->execute()) {
                header("location: agent_login.php");
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
    <title>Sign Up</title>
</head>
<body>
    <div>
        <h2>Register Agent</h2>
        <p>Please fill this form to create an account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div>
                <label>Username</label>
                <input type="text" name="agent_username" class="form-control" value="<?php echo $agent_username; ?>">
                <span><?php echo $username_err; ?></span>
            </div>    
            <div>
                <label>Password</label>
                <input type="password" name="agent_password" class="form-control">
                <span><?php echo $password_err; ?></span>
            </div>
            <div>
                <button type="submit">Submit</button>
            </div>
        </form>
    </div>
</body>
</html>
