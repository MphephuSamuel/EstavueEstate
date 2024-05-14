<?php
ob_start(); // Start output buffering
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if (empty($username) || empty($password)) {
        // Return an error response if username or password is empty
        http_response_code(400); // Bad Request
        echo json_encode(array("success" => false, "message" => "Please enter both username and password."));
        exit();
    }

    $sql = "SELECT user_id, username, password FROM users WHERE username = ? AND role = 'seller'";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $username);

        if ($stmt->execute()) {
            $stmt->store_result();
            if ($stmt->num_rows == 1) {
                $stmt->bind_result($id, $username, $hashed_password);
                if ($stmt->fetch()) {
                    if (password_verify($password, $hashed_password)) {
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $id;
                        $_SESSION["username"] = $username;
                        $_SESSION["role"] = 'seller';

                        // Return a success response with the redirect URL
                        echo json_encode(array("success" => true, "redirectUrl" => "seller_dashboard.php"));
                        exit();
                    } else {
                        // Return an error response for invalid password
                        http_response_code(401); // Unauthorized
                        echo json_encode(array("success" => false, "message" => "Invalid password."));
                        exit();
                    }
                }
            }
        } else {
            // Return an error response for SQL execution error
            http_response_code(500); // Internal Server Error
            echo json_encode(array("success" => false, "message" => "Oops! Something went wrong. Please try again later."));
            exit();
        }
    } else {
        // Return an error response for SQL preparation error
        http_response_code(500); // Internal Server Error
        echo json_encode(array("success" => false, "message" => "Oops! Something went wrong. Please try again later."));
        exit();
    }

    // Return an error response for invalid username
    http_response_code(401); // Unauthorized
    echo json_encode(array("success" => false, "message" => "Invalid username."));
    exit();
}
?>
