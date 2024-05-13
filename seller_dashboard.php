<?php
session_start();
include 'db.php';

// Redirect if not logged in or not the right user role
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'seller') {
    header("location: index.html");
    exit;
}

// Fetch notifications and their replies
$sql = "SELECT n.notification_id, n.message AS notification_message, n.image_path, r.message AS reply_message, r.created_at 
        FROM notifications n
        LEFT JOIN replies r ON n.notification_id = r.notification_id
        WHERE n.sender_id = ?
        ORDER BY n.notification_id, r.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    // Check if the notification already exists in the $notifications array
    if (!isset($notifications[$row['notification_id']])) {
        // If it doesn't exist, create a new entry for it
        $notifications[$row['notification_id']] = [
            'notification_message' => $row['notification_message'],
            'image_path' => $row['image_path'],
            'replies' => []
        ];
    }

    // Add the reply to the notification
    $notifications[$row['notification_id']]['replies'][] = [
        'message' => $row['reply_message'],
        'created_at' => $row['created_at']
    ];
}


// Handle notification and image upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_notification'])) {
    $message = $_POST['message'];  // Message from form input
    $image_path = "";  // Default no image
    $upload_status = "";

    // Check if a file is uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $image_path = $upload_dir . basename($_FILES['image']['name']);
        if (move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
            $upload_status = "Image uploaded successfully!";
        } else {
            $upload_status = "Failed to upload image.";
        }
    }

    // Insert notification into the database (assuming you have an 'agents' table to fetch all agents)
    $sql = "INSERT INTO notifications (sender_id, receiver_id, message, image_path) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // Example: Notify all agents (you might want to select specific agents based on some criteria)
    $agents = $conn->query("SELECT user_id FROM users WHERE role = 'agent'");
    while ($agent = $agents->fetch_assoc()) {
        // Prepare the statement outside the loop
        $stmt->bind_param("iiss", $_SESSION['id'], $agent['user_id'], $message, $image_path);
        $stmt->execute();
    }
    $stmt->close();
    $upload_status .= " Notification sent successfully!";

    // Redirect to prevent form resubmission
    header("Location: {$_SERVER['REQUEST_URI']}");
    exit(); // Stop further execution
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #000;
            color: #fff;
            margin: 0;
            padding: 0;
        }

        h1, h2 {
            color: #d99115; /* Gold color */
            text-align: center;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.8);
            border-radius: 10px;
        }

        .notification {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .notification img {
            max-width: 100%;
            border-radius: 5px;
        }

        .notification p {
            margin: 10px 0;
        }

        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
            border: none;
            border-radius: 5px;
        }

        button[type="submit"] {
            padding: 10px 20px;
            background-color: #d99115; /* Gold color */
            color: #000;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #ffd700; /* Lighter gold color */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <h2>Your Notifications and Replies</h2>
        <?php foreach ($notifications as $notification_id => $data): ?>
            <div class="notification">
                <p><strong>Notification:</strong> <?php echo htmlspecialchars($data['notification_message']); ?></p>
                <?php if ($data['image_path']): ?>
                    <img src="<?php echo htmlspecialchars($data['image_path']); ?>" alt="Notification Image" style="width:200px;">
                <?php endif; ?>
                <?php if (!empty($data['replies'])): ?>
                    <h4>Replies:</h4>
                    <?php foreach ($data['replies'] as $reply): ?>
                        <?php if ($reply['message']): ?>
                            <div><p><?php echo htmlspecialchars($reply['message']); ?> (<?php echo $reply['created_at']; ?>)</p></div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <?php if (!empty($upload_status)) echo "<p>$upload_status</p>"; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <h2>Send Notification to Agents</h2>
            <textarea name="message" required>Does my house qualify?</textarea><br>
            <input type="file" name="image"><br>
            <button type="submit" name="send_notification">Send Notification</button>
        </form>
        <form action="logout.php?user_type=seller" method="post">
            <button type="submit">Logout</button>
        </form>
    </div>
</body>
</html>
