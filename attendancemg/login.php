<?php
session_start();

// Check if the user is already logged in
if (isset($_SESSION['teacher_id'])) {
    header('Location: index.php'); // Redirect to dashboard if already logged in
    exit();
}

// Database connection
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate the credentials
    $query = "SELECT * FROM teachers WHERE username = :username";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['username' => $username]);
    $teacher = $stmt->fetch();

    if ($teacher && password_verify($password, $teacher['password'])) {
        // Password is correct, create session
        $_SESSION['teacher_id'] = $teacher['id'];
        $_SESSION['teacher_username'] = $teacher['username'];

        // Redirect to the teacher's dashboard
        header('Location: index.php');
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Login</title>
    <style>
        /* Global Styles */
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

/* Login Container */
.login-container {
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

    </style>
</head>
<body>
    <div class="login-container">
        <h2>Teacher Login</h2>

        <?php if (isset($error)): ?>
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

            <button type="submit" class="btn">Login</button>
        </form>
    </div>
</body>
</html>
