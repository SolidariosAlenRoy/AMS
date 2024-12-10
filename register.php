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

$userName = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
?>

<!-- HTML for registration form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin's Dashboard</title>
    <!-- FontAwesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- FullCalendar CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.js"></script>
    <link href="css/registeracc.css" rel="stylesheet">
</head>

<body>
    
    <div class="container">
    <div class="example-box">
        <div class="background-shapes">
    </div>
        <!-- Sidebar -->
        <aside class="sidebar">
        <img src="image/logo3.jpg" alt="Logo" class="logo"> 
    <h4 class="text-primary"><i class=""></i>CLASS TRACK</h4>
    <nav class="nav">
        <ul>
            <li><a href="admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="addstudent.php"><i class="fas fa-user-graduate"></i> Manage Students</a></li>
            <li><a href="addteacher.php"><i class="fas fa-chalkboard-teacher"></i> Manage Teacher</a></li>
            <li><a href="subject.php"><i class="fas fa-book-open"></i> Manage Subject</a></li>
            <li><a href="register.php"><i class="fas fa-user-plus"></i>Register Acc.</a></li>
        </ul>
    </nav>
</aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="header">
                <h1>Admin's Dashboard</h1>
                <div class="header-content">
                <!-- Profile Bar -->
                <div class="profile-bar" onclick="toggleDropdown(event)">
                    <img src="image/profile.png" alt="Profile Picture" class="profile-picture"> <!-- Example profile image -->
                    <div class="profile-info">
                        <h5 class="profile-name"><?php echo htmlspecialchars($userName); ?></h5>
                        <p class="profile-role"><?php echo htmlspecialchars($_SESSION['role'] ?? 'admin'); ?></p>
                    </div>
                </div>
                <!-- Dropdown Menu -->
                <div id="profileDropdown" class="profile-dropdown">
                    <a href="login.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
                </div>
            </div>

            <div class="card">
        <h2>REGISTRATION</h2>
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

        
    </div>
</div>
</div>
</main>
</div>
<script src="js/register.js"></script>
</body>
</html>
