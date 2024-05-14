<?php
session_start();
include 'db.php';

// Check if user is logged in as admin
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header("location: admin_login.php");
    exit;
}

// Fetch seller accounts from the database
$sql = "SELECT * FROM users WHERE role IN ('seller', 'agent')";
$result = $conn->query($sql);
$users = $result->fetch_all(MYSQLI_ASSOC);

// Fetch notification data for analytics
$sql = "SELECT DATE(timestamp) AS notification_date, COUNT(*) AS notification_count
        FROM notifications
        GROUP BY DATE(timestamp)
        ORDER BY notification_date";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

// Process data for visualization
$notificationDates = [];
$notificationCounts = [];
while ($row = $result->fetch_assoc()) {
    $notificationDates[] = $row['notification_date'];
    $notificationCounts[] = $row['notification_count'];
}

// Retrieve survey responses from the database
$sql = "SELECT rating, COUNT(*) AS count FROM survey_responses GROUP BY rating";
$result = $conn->query($sql);
$ratings = [];
while ($row = $result->fetch_assoc()) {
    $ratings[$row['rating']] = $row['count'];
}

/* Debugging: Print retrieved ratings
echo "<pre>";
print_r($ratings);
echo "</pre>";*/

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1, h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        a {
            text-decoration: none;
            color: #007bff;
            margin-right: 10px;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to the Admin Dashboard, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>

        <h2>User Management</h2>
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                        <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                        <td>
                            <!-- Add edit and delete buttons with appropriate links -->
                            <a href="edit_user.php?id=<?php echo $user['user_id']; ?>">Edit</a>
                            <a href="delete_user.php?id=<?php echo $user['user_id']; ?>" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <br>

        <h2>Add New User</h2>
<form action="add_user.php" method="post">
    <div>
        <label>Username</label>
        <input type="text" name="username" required>
    </div>
    <div>
        <label>Email</label>
        <input type="email" name="email" required>
    </div>
    <div>
        <label>Password</label>
        <input type="password" name="password" required>
    </div>
    <div>
        <label>User Role</label>
        <select name="role" required>
            <option value="seller">Seller</option>
            <option value="agent">Agent</option>
        </select>
    </div>
    <button type="submit">Add User</button>
</form>
        

<h2>Notification analytics chart to see if the sellers really need that part</h2>
        <!-- Display Notification Analytics Chart -->
    <div style="width: 800px; height: 400px;">
        <canvas id="notificationChart"></canvas>
    </div>
    
    <div>
        <h2>User Satisfaction Ratings</h2>
        <canvas id="ratingChart" width="200" height="200"></canvas>
    </div>

    <script>
        var ratings = <?php echo json_encode($ratings); ?>;
        var labels = [];
        var data = [];

        for (var rating in ratings) {
            labels.push(rating + ' Stars');
            data.push(ratings[rating]);
        }

        // Debugging: Print labels and data
        console.log(labels);
        console.log(data);

        var ctx = document.getElementById('ratingChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)', // Red
                        'rgba(54, 162, 235, 0.7)', // Blue
                        'rgba(255, 206, 86, 0.7)', // Yellow
                        'rgba(75, 192, 192, 0.7)', // Green
                        'rgba(153, 102, 255, 0.7)' // Purple
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                title: {
                    display: true,
                    text: 'User Satisfaction Ratings'
                }
            }
        });
    </script>
    </script>
    <!-- JavaScript to render the chart -->
    <script>
        var ctx = document.getElementById('notificationChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($notificationDates); ?>,
                datasets: [{
                    label: 'Number of Notifications',
                    data: <?php echo json_encode($notificationCounts); ?>,
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    </script>

    <!-- Logout Button -->
    </form>

        <a href="logout.php?user_type=admin">Logout</a>
    </div>
    
    
</body>
</html>

