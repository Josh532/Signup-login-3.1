<?php 
session_start();

$is_invalid = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $mysqli = require __DIR__ . "/data-base.php";

    // Use prepared statement for security
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $_POST["email"]);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($_POST["password"], $user["password_hash"])) {
        // Login successful
        $_SESSION["user_id"] = $user["id"];
        header("Location: account.php");
        exit;
    }

    $is_invalid = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Keep cache-busting for CSS -->
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
        <div class="intercontainer">
            <h1>Login</h1>

            <?php if ($is_invalid): ?>
                <em>Invalid login</em>
            <?php endif; ?>

            <form method="post" novalidate>
                <div>
                    <input type="email" name="email" id="email" placeholder="Enter your email"
                        value="<?= htmlspecialchars($_POST["email"] ?? ""); ?>" required>
                </div>

                <div>
                    <input type="password" name="password" id="password" placeholder="Enter your password" required>
                </div>

                <button>Login</button>
<!--
                <div class="alt">
                    <img src="https://cdn-icons-png.flaticon.com/512/25/25231.png" alt="github logo" width="40">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c1/Google_%22G%22_logo.svg/1200px-Google_%22G%22_logo.svg.png" alt="google logo" width="40">
                </div>
            -->

                <div class="links">
                   <!-- <p>Forgot password?</p> 
                    <a href="forgot-password.php">Reset password</a>-->
                    <p>Don't have an account? <a href="signup.php">Sign up</a></p> 
                    
                </div>
            </form>
        </div>
    </div>
</body>
</html>
