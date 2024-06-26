<?php
session_start();
include 'db.php';

$username = $password = "";
$username_err = $password_err = $login_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(empty(trim($_POST["username"]))) {
        $username_err = "Please enter your username.";
    } else {
        $username = trim($_POST["username"]);
    }

    if(empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    if(empty($username_err) && empty($password_err)) {
        $sql = "SELECT user_id, username, password, role FROM users WHERE username = ? AND role = 'agent'";
        if($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_username);
            $param_username = $username;

            if($stmt->execute()) {
                $stmt->store_result();
                if($stmt->num_rows == 1) {
                    $stmt->bind_result($id, $username, $hashed_password, $role);
                    if($stmt->fetch()) {
                        if(password_verify($password, $hashed_password)) {
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            $_SESSION["role"] = $role;

                            echo json_encode(array("success" => true, "redirectUrl" => "agent_dashboard.php"));
                        } else {
                            echo json_encode(array("success" => false, "message" => "Invalid password."));
                        }
                    }
                } else {
                    echo json_encode(array("success" => false, "message" => "Invalid username."));
                }
            } else {
                echo json_encode(array("success" => false, "message" => "Oops! Something went wrong. Please try again later."));
            }
            $stmt->close();
        }
    }
    $conn->close();
}
?>
