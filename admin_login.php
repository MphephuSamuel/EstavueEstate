<?php
session_start();
include 'db.php';

// Initialize variables
$username = $password = "";
$username_err = $password_err = $login_err = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter your username.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Check if username and password are not empty
    if (empty($username_err) && empty($password_err)) {
        // SQL query to retrieve user data
        $sql = "SELECT admin_id, username, password FROM admins WHERE username = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_username);
            $param_username = $username;

            // Execute the prepared statement
            if ($stmt->execute()) {
                $stmt->store_result();

                // Check if username exists
                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($admin_id, $username, $hashed_password);
                    if ($stmt->fetch()) {
                        // Verify password
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, start a new session
                            $_SESSION["loggedin"] = true;
                            $_SESSION["admin_id"] = $admin_id;
                            $_SESSION["username"] = $username;
                            $_SESSION["role"] = 'admin';

                            // Set response data for successful login
                            $response = array(
                                "success" => true,
                                "redirectUrl" => "admin_dashboard.php"
                            );
                        } else {
                            // Password is incorrect
                            $response = array(
                                "success" => false,
                                "message" => "Invalid password."
                            );
                        }
                    }
                } else {
                    // Username does not exist
                    $response = array(
                        "success" => false,
                        "message" => "Invalid username."
                    );
                }
            } else {
                // Database error
                $response = array(
                    "success" => false,
                    "message" => "Oops! Something went wrong. Please try again later."
                );
            }

            // Close statement
            $stmt->close();
        }
    }

    // Close connection
    $conn->close();

    // Send response data as JSON
    echo json_encode($response);
}
?>
