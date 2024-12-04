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
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
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
            margin-top: 15px;
        }

        button:hover {
            background-color: #0056b3;
        }

        .error-message {
            color: red;
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

