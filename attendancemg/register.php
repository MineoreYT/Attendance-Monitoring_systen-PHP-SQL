<?php
session_start();

// Check if the user is already logged in
if (isset($_SESSION['teacher_id'])) {
    header('Location: dashboard.php'); // Redirect if already logged in
    exit();
}

// Database connection
require_once 'config.php';

// Error message for registration failure
$error = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate form data
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = "Please fill in all fields.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if username already exists
        $query = "SELECT * FROM teachers WHERE username = :username";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['username' => $username]);
        if ($stmt->rowCount() > 0) {
            $error = "Username already taken.";
        } else {
            // Hash password and insert new teacher into the database
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $query = "INSERT INTO teachers (username, password) VALUES (:username, :password)";
            $stmt = $pdo->prepare($query);
            if ($stmt->execute(['username' => $username, 'password' => $hashed_password])) {
                header('Location: login.php'); // Redirect to login page after successful registration
                exit();
            } else {
                $error = "Error registering teacher. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Registration</title>
    <style>/* Global Styles */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f7fc;
    margin: 0;
    padding: 0;
}

h2 {
    text-align: center;
    margin-bottom: 20px;
}

/* Register Container */
.register-container {
    width: 100%;
    max-width: 400px;
    margin: 100px auto;
    padding: 20px;
    background-color: #ffffff;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}

/* Input Group */
.input-group {
    margin-bottom: 20px;
}

.input-group label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
}

.input-group input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}

/* Button */
.btn {
    width: 100%;
    padding: 12px;
    background-color: #28a745;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.btn:hover {
    background-color: #218838;
}

/* Error Message */
.error {
    color: red;
    text-align: center;
    margin-bottom: 15px;
}

/* Links */
a {
    color: #007bff;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}
</style>
</head>
<body>
    <div class="register-container">
        <h2>Teacher Registration</h2>

        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="input-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <button type="submit" class="btn">Register</button>
        </form>

        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>
