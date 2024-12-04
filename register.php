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
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url(image/loginbg2.jpg);
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            display: flex;
            background-color: rgba(255, 255, 255, 0.4);
            border-radius: 20px;
            width: 800px;
            overflow: hidden;
            box-shadow: 0 0 15px rgba(0, 123, 255, 0.6), 
                        0 0 25px rgba(0, 255, 0, 0.4);   
        }

        .logo-container, .card {
            width: 50%;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .logo-container {
            background-color: rgba(255, 255, 255, 0.4);
            box-shadow: 0 0 15px rgba(0, 123, 255, 0.6), 
                        0 0 25px rgba(0, 255, 0, 0.4);   
        }

        .logo-container img {
            max-width: 100%;
            height: auto;
            border-radius: 20px;
        }

        .card {
            text-align: center;
            background-color: rgba(255, 255, 255, 0.4);
            box-shadow: 0 0 15px rgba(0, 123, 255, 0.6), 
                        0 0 25px rgba(0, 255, 0, 0.4);  
        }

        h2 {
            margin-bottom: 20px;
            font-weight: bold;
            font-family: "Times New Roman", serif;
            color: #333;
        }

        input[type="text"],
        input[type="password"],
        select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ced4da;
            border-radius: 5px;
            box-sizing: border-box;
        }

        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }

        button:hover {
            background-color: #0056b3;
        }

        .success-message {
            color: green;
            margin-top: 10px;
        }
    </style>
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
