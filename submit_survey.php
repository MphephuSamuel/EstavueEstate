<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $rating = $_POST['rating'];
    $feedback = $_POST['feedback'];

    // Insert survey response into the database
    $sql = "INSERT INTO survey_responses (name, email, rating, feedback) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssis", $name, $email, $rating, $feedback);
    $stmt->execute();
    $stmt->close();
    
    // Close the database connection
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Survey Submitted</title>
    <script>
        setTimeout(function(){
            window.location.href = "index.html"; // Redirect to index.html after 2 seconds
        }, 2000); // 2000 milliseconds = 2 seconds
    </script>
</head>
<body>
    <p>Survey submitted. Redirecting...</p>
</body>
</html>
