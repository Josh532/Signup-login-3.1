<?php
session_start(); // MUST be first

// Redirect to login if not logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Connect to database
$mysqli = require __DIR__ . "/data-base.php";

// Fetch logged-in user info securely using prepared statement
$stmt = $mysqli->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Optional: handle case where user is not found
if (!$user) {
    // Clear session and redirect to login
    session_destroy();
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="style.css?v=<?= filemtime('style.css'); ?>">
    <style>
body {
            background-size: cover;
            background-image: url("https://wallpapers.com/images/featured/mountain-sunset-background-e94f78n373kjvxpn.jpg");
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, <?= htmlspecialchars($user["name"]); ?>!</h1>
        <div class="links">
         <a href="home.php">Home</a></p>
        <p><a href="logout.php">Logout</a></p>
        </div>
    </div>
</body>
</html>
