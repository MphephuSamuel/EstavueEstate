<?php
session_start();
include 'db.php';

// Redirect if not logged in or if the user is not an agent
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'agent') {
    header("location: agent_login.php");
    exit;
}

// Handle reply submissions
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reply_message'])) {
    $reply_message = $_POST['reply_message'];
    $notification_id = $_POST['notification_id']; // Make sure this is being sent by the form

    $reply_sql = "INSERT INTO replies (notification_id, sender_id, message) VALUES (?, ?, ?)";
    if ($stmt = $conn->prepare($reply_sql)) {
        $stmt->bind_param("iis", $notification_id, $_SESSION['id'], $reply_message);
        if ($stmt->execute()) {
            // Set success message
            $_SESSION['reply_status'] = 'Reply sent successfully.';
            // Redirect to clear POST data
            header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
            exit;
        } else {
            $_SESSION['reply_status'] = 'Failed to send reply.';
        }
        $stmt->close();
    }
}

// Fetch notifications for the logged-in agent, including sender details
$sql = "SELECT n.notification_id, n.message, n.image_path, u.username as sender_username
        FROM notifications n
        JOIN users u ON n.sender_id = u.user_id
        LEFT JOIN replies r ON n.notification_id = r.notification_id
        WHERE n.receiver_id = ? AND r.notification_id IS NULL"; // Filter out notifications with replies
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Dashboard</title>
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
        <h1>Welcome, agent <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        
        <h2>Your Notifications</h2>
        <?php if (isset($_SESSION['reply_status'])): ?>
            <div class="notification">
                <p><?php echo htmlspecialchars($_SESSION['reply_status']); ?></p>
            </div>
            <?php unset($_SESSION['reply_status']); // Clear the success message ?>
        <?php endif; ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="notification">
                <?php if ($row['sender_username']): ?>
                    <p>From: <?php echo htmlspecialchars($row['sender_username']); ?></p>
                <?php endif; ?>
                <?php if ($row['message']): ?>
                    <p>Message: <?php echo htmlspecialchars($row['message']); ?></p>
                <?php endif; ?>
                <?php if ($row['image_path']): ?>
                    <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="Notification Image">
                <?php endif; ?>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <textarea name="reply_message" required placeholder="Reply here..."></textarea>
                    <input type="hidden" name="notification_id" value="<?php echo $row['notification_id']; ?>">
                    <button type="submit">Reply</button>
                </form>
            </div>
        <?php endwhile; ?>
    </div>
    <form action="logout.php?user_type=agent" method="post">
        <button type="submit">Logout</button>
    </form>

</body>
</html>
