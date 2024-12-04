<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $query = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sss', $username, $password, $role);
    $stmt->execute();

    echo "Registration successful!";

    header('Location: login.php');
    exit();
}
?>

<!-- HTML for registration form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="css/register.css" rel="stylesheet">
</head>
<body>
<div class="container">
        <!-- Logo Container -->
        <div class="logo-container">
            <img src="image/logo3.jpg" alt="Logo"> 
        </div>

    <div class="card">
        <h2>REGISTER</h2>
        <form method="POST" action="register.php">
            <input type="text" name="username" placeholder="Username" autocomplete="new-username" required>
            <input type="password" name="password" placeholder="Password" autocomplete="new-password" required>
            <select name="role" required>
                <option value="" disabled selected>Select Role</option>
                <option value="admin">Admin</option>
                <option value="teacher">Teacher</option>
            </select>
            <button type="submit">Register</button>
            <?php if (isset($success_message)): ?>
                <div class="success-message"><?php echo $success_message; ?></div>
            <?php endif; ?>
        </form>

        <p style="margin-top: 10px; font-size: 14px; margin-top: 10px">
           Already have an account? <a href="login.php" style="color: #007bff; text-decoration: none;">Login here</a>.
        </p>
    </div>
</body>
</html>
