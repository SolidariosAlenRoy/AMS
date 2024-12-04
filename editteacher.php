<?php
session_start();
require 'db.php'; // Include your database connection

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the teacher details
    $query = "SELECT * FROM teachers WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $teacher = $result->fetch_assoc();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $contact_number = $_POST['contact_number'];

        // Update teacher details
        $update_query = "UPDATE teachers SET name = ?, email = ?, contact_number = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param('sssi', $name, $email, $contact_number, $id);

        if ($update_stmt->execute()) {
            echo "Teacher updated successfully!";
            // Optionally, redirect after successful update
            header("Location: addteacher.php");
            exit;
        } else {
            echo "Error: " . $update_stmt->error;
        }
    }
}

$userName = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Teachers</title>
    <!-- FontAwesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- FullCalendar CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.js"></script>
    <link href="css/editteacher.css" rel="stylesheet"> 
    <script src="js/editteacher.js"></script>
</head>
<body>
    <div class="container">
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
                <h1>Manage Teachers</h1>
                <div class="header-content">
                        <!-- Profile Bar -->
                <div class="profile-bar" onclick="toggleDropdown(event)">
                    <img src="image/profile.png" alt="Profile Picture" class="profile-picture"> 
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

            <div class="form-container-wrapper">
            <form method="POST" action="editteacher.php?id=<?php echo $id; ?>" onsubmit="return confirmUpdate();">
                    <input type="text" name="name" value="<?php echo $teacher['name']; ?>" required>
                    <input type="email" name="email" value="<?php echo $teacher['email']; ?>">
                    <input type="text" name="contact_number" value="<?php echo $teacher['contact_number']; ?>">
                    <button type="submit">Update Teacher</button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
