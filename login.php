<?php
session_start();
require 'db.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute query
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Verify password
    if ($user && password_verify($password, $user['password'])) {
        // Determine role based on the length of the 'id' field
        $idLength = strlen((string)$user['id']); // Convert to string and get length
        if ($idLength === 1) {
            $role = 'admin';
        } elseif ($idLength === 2) {
            $role = 'teacher';
        } else {
            $role = 'unknown';
        }

        // Set session variables
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $role;

        // Redirect based on role
        if ($role === 'admin') {
            header('Location: admin.php'); // Redirect to admin page
        } elseif ($role === 'teacher') {
            header('Location: teacherint.php'); // Redirect to teacher page
        } else {
            echo "Invalid role.";
        }
        exit(); // Ensure no further code is executed
    } else {
        echo "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="css/login.css" rel="stylesheet">     
</head>
<body>
<div class="example-box">
        <div class="background-shapes">
    </div>
    <div class="container">
        
        <!-- Logo Container -->
        <div class="logo-container">
            <img src="image/logo3.jpg" alt="Logo"> 
        </div>

        <!-- Login Form -->
        <div class="card">
            <h2>LOG-IN</h2>
            <form method="POST" action="login.php">
                <input type="text" name="username" placeholder="Username" autocomplete="new-username" required>
                <input type="password" name="password" placeholder="Password" autocomplete="new-password" required>
                <button type="submit">Login</button>
                <?php if (isset($error_message)): ?>
                    <div class="error-message"><?php echo $error_message; ?></div>
                <?php endif; ?>
            </form>

            <p style="margin-top: 10px; font-size: 14px; margin-top: 10px">
               Don't have an account? <a href="register.php" style="color: #007bff; text-decoration: none;">Register here</a>.
            </p>
        </div>
    </div>
</body>
</html>

