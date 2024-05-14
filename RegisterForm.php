<?php
include 'db.php';

$username_err = $password_err = $phone_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $phone = trim($_POST["phone"]);
    $email = trim($_POST["email"]);
    $firstName = trim($_POST["firstName"]);
    $secondName = trim($_POST["secondName"]);
    $lastName = trim($_POST["lastName"]);

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

    if (empty($phone)) {
        $phone_err = "Please enter a phone number.";
    } elseif (!preg_match("/^[1-9][0-9]{8}$/", $phone)) {
        $phone_err = "Invalid phone number format.";
    }

    if (empty($username_err) && empty($password_err) && empty($phone_err)) {
        // Insert user data into the database
        $sql = "INSERT INTO users (username, password, role, phone, email, firstName, secondName, lastName) VALUES (?, ?, 'seller', ?, ?, ?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bind_param("sssssss", $username, $param_password, $phone, $email, $firstName, $secondName, $lastName);
            if ($stmt->execute()) {
                // Redirect to index.html
                header("location: registration_success.php");
                exit;
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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Registration Page</title>
<link href="assets/css/style.css" rel="stylesheet">

<script lang="Javascript">
  document.getElementById("phone").addEventListener("input", function() {
    if (this.value.length > 9) {
        this.value = this.value.slice(0, 9);
    }
});
</script>


<style>
    .containerr {
  max-width: 400px;
  margin: 50px auto;
  background-color: #fff;
  padding: 20px;
  border-radius: 5px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.Rbutton{
  text-align: center;
}

.containerr h2{
  text-align: center;
  margin-bottom: 20px;
}

.form-group {
  margin-bottom: 20px;
}

.form-group {
  display: block;
  font-weight: bold;
}

input[type="text"],
input[type="email"],
input[type="tel"],
input[type="password"],
input[type="file"] {
  width: calc(100% - 20px);
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 3px;
}

input[type="text"]:focus,
input[type="email"]:focus,
input[type="tel"]:focus,
input[type="password"]:focus,
input[type="file"]:focus {
  outline: none;
}

small {
  color: #777;
}


input[type="file"] {
  cursor: not-allowed;
  background-color: #eee;
}

</style>

</head>
<body>
    <div class="containerr">
        <h2>Registration Form</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required readonly>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required readonly>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number:</label>
                <input type="tel" id="phone" name="phone" pattern="[1-9][0-9]{8}" maxlength="9" required>
            </div>
            <div class="form-group">
                <label for="username">Username:</label> 
                <input type="text" id="username" name="username" required>
                <span><?php echo $username_err; ?></span>
            </div>
            <div class="form-group">
                <label for="firstName">First Name:</label>
                <input type="text" id="firstName" name="firstName" required>
            </div>
            <div class="form-group">
                <label for="secondName">Second Name:</label>
                <input type="text" id="secondName" name="secondName">
            </div>
            <div class="form-group">
                <label for="lastName">Last Name:</label>
                <input type="text" id="lastName" name="lastName" required>
            </div>
            <div class="form-group">
                <label for="upload">Profile Image:</label>
                <input type="file" id="upload" name="upload" accept="image/*" required>
            </div>
            <div class="Rbutton">
                <button type="submit">Register</button>
            </div>
        </form>
    </div>
    <script src="assets/js/main.js"></script>
    <script>
        window.onload = function() {
            var urlParams = new URLSearchParams(window.location.search);
            var email = urlParams.get('email');
            var password = urlParams.get('password');
            
            if (email && password) {
                document.getElementById("email").value = email;
                document.getElementById("password").value = password;
            }
        };
</script>

</body>
</html>
