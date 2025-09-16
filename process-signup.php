<?php
header('Content-Type: application/json'); // Tell the browser it's JSON

// Input validation
if (empty($_POST["name"])) {
    echo json_encode(["success" => false, "field" => "name", "message" => "Name is required"]);
    exit;
}

if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["success" => false, "field" => "email", "message" => "Valid email is required"]);
    exit;
}

if (strlen($_POST["password"]) < 8) {
    echo json_encode(["success" => false, "field" => "password", "message" => "Password must be at least 8 characters"]);
    exit;
}

if (!preg_match("/[a-z]/i", $_POST["password"])) {
    echo json_encode(["success" => false, "field" => "password", "message" => "Password must contain at least one letter"]);
    exit;
}

if (!preg_match("/[0-9]/", $_POST["password"])) {
    echo json_encode(["success" => false, "field" => "password", "message" => "Password must contain at least one number"]);
    exit;
}

if ($_POST["password"] !== $_POST["password_confirmation"]) {
    echo json_encode(["success" => false, "field" => "password_confirmation", "message" => "Passwords must match"]);
    exit;
}

// Hash the password
$password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);

// Connect to database
$mysqli = require __DIR__ . "/data-base.php";

// Prepare SQL statement (table is 'users')
$sql = "INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)";
$stmt = $mysqli->stmt_init();

if (!$stmt->prepare($sql)) {
    echo json_encode(["success" => false, "field" => "email", "message" => "SQL error: " . $mysqli->error]);
    exit;
}

// Bind parameters
$stmt->bind_param("sss", $_POST["name"], $_POST["email"], $password_hash);

// Execute with try/catch to handle duplicate email and other DB errors
try {
    $stmt->execute();
    echo json_encode(["success" => true]);
    exit;
} catch (mysqli_sql_exception $e) {
    if ($e->getCode() === 1062) { // Duplicate email
        echo json_encode([
            "success" => false,
            "field" => "email",
            "message" => "Email already taken"
        ]);
        exit;
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Database error: " . $e->getMessage()
        ]);
        exit;
    }
}
