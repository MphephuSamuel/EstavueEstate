<?php
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Determine the type of user and redirect accordingly
if ($_GET['user_type'] === 'agent') {
    // Redirect agents to the agent login page
    header("location: agent_login.html");
} elseif ($_GET['user_type'] === 'seller') {
    // Redirect sellers to the seller login page
    header("location: index.html");
} else {
    // Redirect to a default login page if no user type is specified
    header("location: index.html");
}

exit;
?>
